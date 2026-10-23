@extends('layouts.vertical', ['subtitle' => 'Import Costing ' . $po->number])

@section('content')
    <x-page-header :title="'Import Costing - ' . $po->number" :crumbs="['Purchase Orders' => route('purchase-orders.index'), $po->number => route('purchase-orders.show', $po), 'Costing']">
        <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-light">Back to PO</a>
    </x-page-header>

    @include('purchasing.partials.workflow', ['po' => $po])

    @php
        $editable = $po->isEditable();
        $charges = old('charges', $po->charges->map(fn ($c) => $c->only('type', 'description', 'amount'))->all());
        foreach (array_keys(\App\Models\PurchaseOrder::CHARGE_TYPES) as $type) {
            if (! collect($charges)->firstWhere('type', $type)) {
                $charges[] = ['type' => $type, 'description' => '', 'amount' => ''];
            }
        }
        $goodsLkr = $po->supplierCostLkr();
        $byProduct = $po->items->groupBy('product_id');
    @endphp

    <div class="row">
        <div class="col-xl-5">
            <form method="POST" action="{{ route('purchase-orders.costing.save', $po) }}">
                @csrf
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Costs for this shipment</h5></div>
                    <div class="card-body">
                        @unless ($editable)<div class="alert alert-warning small">Goods have been received, so costing is locked.</div>@endunless
                        <div class="row g-2 mb-3">
                            <div class="col-6"><label class="form-label">Exchange rate ({{ $po->currency }} &rarr; Rs)</label><input type="number" step="0.0001" name="exchange_rate" value="{{ old('exchange_rate', $po->exchange_rate) }}" class="form-control" @disabled(! $editable)></div>
                            <div class="col-6"><label class="form-label">Share charges by</label>
                                <select name="allocation_method" class="form-select" @disabled(! $editable)>
                                    <option value="value" @selected($po->allocation_method === 'value')>Value (price)</option>
                                    <option value="qty" @selected($po->allocation_method === 'qty')>Quantity (pairs)</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mb-1"><span>Supplier invoice</span><span>{{ $po->currency }} {{ number_format($po->subtotal_fx, 2) }} = <strong>{{ money($goodsLkr) }}</strong></span></div>
                        <hr>
                        @foreach (array_values($charges) as $i => $c)
                            <div class="row g-2 align-items-center mb-2">
                                <div class="col-5 small fw-semibold">{{ \App\Models\PurchaseOrder::CHARGE_TYPES[$c['type']] ?? $c['type'] }}<input type="hidden" name="charges[{{ $i }}][type]" value="{{ $c['type'] }}"></div>
                                <div class="col-7"><div class="input-group input-group-sm"><span class="input-group-text">Rs</span><input type="number" step="0.01" min="0" name="charges[{{ $i }}][amount]" value="{{ $c['amount'] }}" class="form-control text-end" @disabled(! $editable)></div></div>
                                <div class="col-12"><input name="charges[{{ $i }}][description]" value="{{ $c['description'] }}" class="form-control form-control-sm" placeholder="Note (agent, CUSDEC no...)" @disabled(! $editable)></div>
                            </div>
                        @endforeach
                        <div class="totals-box border-top mt-3 pt-2">
                            <div class="row-total"><span>Goods (Rs)</span><span class="money">{{ money($goodsLkr) }}</span></div>
                            <div class="row-total"><span>Import charges</span><span class="money">{{ money($po->charges_total) }}</span></div>
                            <div class="row-total grand"><span>Landed total</span><span class="money">{{ money($po->landed_total) }}</span></div>
                        </div>
                    </div>
                    @if ($editable)
                        <div class="card-footer text-end sticky-actions"><button class="btn btn-primary">Save &amp; recalculate</button></div>
                    @endif
                </div>
            </form>
        </div>
        <div class="col-xl-7">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Landed cost per pair</h5></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Product</th><th class="text-end">Pairs</th><th class="text-end">Supplier price</th><th class="text-end">+ Charges</th><th class="text-end">Landed / pair</th><th class="text-end">Suggested price</th></tr></thead>
                            <tbody>
                                @foreach ($byProduct as $rows)
                                    @php $f = $rows->first(); $base = $f->unit_price_fx * $po->exchange_rate; @endphp
                                    <tr>
                                        <td class="td-title"><strong>{{ $f->product->code }}</strong> <span class="small text-muted">{{ $f->product->name }} {{ $f->product->colour }}</span></td>
                                        <td data-label="Pairs" class="text-end">{{ qty($rows->sum('qty')) }}</td>
                                        <td data-label="Supplier price" class="text-end money">{{ money($base) }}<div class="small text-muted">{{ $po->currency }} {{ number_format($f->unit_price_fx, 2) }}</div></td>
                                        <td data-label="+ Charges" class="text-end money text-warning">{{ money($f->landed_unit_cost - $base) }}</td>
                                        <td data-label="Landed / pair" class="text-end money fw-bold">{{ money($f->landed_unit_cost) }}</td>
                                        <td data-label="Suggested" class="text-end money text-success">{{ money(round($f->landed_unit_cost * (1 + $f->product->effectiveMarkup() / 100), -1)) }}<div class="small text-muted">+{{ $f->product->effectiveMarkup() }}%</div></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer small text-muted">
                    When the GRN is posted, these landed costs are blended into each product's average cost and used for profit on every sale.
                    Update selling prices afterwards from <a href="{{ route('products.bulk-price') }}">Bulk Price Update</a>.
                </div>
            </div>
        </div>
    </div>
@endsection

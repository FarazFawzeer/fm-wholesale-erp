@extends('layouts.vertical', ['subtitle' => 'Shipping & Duty Costs ' . $po->number])

@section('content')
    <x-page-header :title="'Shipping & duty costs - ' . $po->number" :crumbs="['Purchase Orders' => route('purchase-orders.index'), $po->number => route('purchase-orders.show', $po), 'Costs']">
        <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-light">Back to order</a>
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
        $goods = $po->supplierCostLkr();
    @endphp

    <div class="row">
        <div class="col-xl-5">
            <form method="POST" action="{{ route('purchase-orders.costing.save', $po) }}">
                @csrf
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Extra costs for this order</h5></div>
                    <div class="card-body">
                        @unless ($editable)<div class="alert alert-warning small">The goods are already received, so these costs can no longer be changed.</div>@endunless
                        <div class="d-flex justify-content-between mb-3"><span class="text-muted">Supplier bill</span><strong class="money">{{ money($goods) }}</strong></div>
                        @foreach (array_values($charges) as $i => $c)
                            <div class="mb-2">
                                <input type="hidden" name="charges[{{ $i }}][type]" value="{{ $c['type'] }}">
                                <label class="form-label small mb-1">{{ \App\Models\PurchaseOrder::CHARGE_TYPES[$c['type']] ?? $c['type'] }}</label>
                                <div class="input-group"><span class="input-group-text">Rs</span><input type="number" step="0.01" min="0" name="charges[{{ $i }}][amount]" value="{{ $c['amount'] }}" class="form-control text-end" inputmode="decimal" @disabled(! $editable)></div>
                                <input type="hidden" name="charges[{{ $i }}][description]" value="{{ $c['description'] }}">
                            </div>
                        @endforeach
                        <div class="mt-3"><label class="form-label small mb-1">Share the costs</label>
                            <select name="allocation_method" class="form-select" @disabled(! $editable)>
                                <option value="value" @selected($po->allocation_method === 'value')>More to expensive shoes (by price)</option>
                                <option value="qty" @selected($po->allocation_method === 'qty')>Same for every pair</option>
                            </select>
                        </div>
                        <div class="totals-box border-top mt-3 pt-2">
                            <div class="row-total"><span>Supplier bill</span><span class="money">{{ money($goods) }}</span></div>
                            <div class="row-total"><span>Extra costs</span><span class="money">{{ money($po->charges_total) }}</span></div>
                            <div class="row-total grand"><span>Total real cost</span><span class="money">{{ money($po->landed_total) }}</span></div>
                        </div>
                    </div>
                    @if ($editable)
                        <div class="card-footer text-end sticky-actions"><button class="btn btn-primary">Save costs</button></div>
                    @endif
                </div>
            </form>
        </div>
        <div class="col-xl-7">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Real cost per pair</h5></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Product</th><th class="text-end">Pairs</th><th class="text-end">Buying price</th><th class="text-end">+ Extra costs</th><th class="text-end">Real cost</th><th class="text-end">Suggested price</th></tr></thead>
                            <tbody>
                                @foreach ($po->items as $item)
                                    <tr>
                                        <td class="td-title"><strong>{{ $item->product->name }}</strong> <span class="small text-muted">{{ $item->product->code }} &middot; {{ $item->product->sizeLabel() }} {{ $item->product->colour }}</span></td>
                                        <td data-label="Pairs" class="text-end">{{ qty($item->qty) }}</td>
                                        <td data-label="Buying price" class="text-end money">{{ money($item->unit_price_fx) }}</td>
                                        <td data-label="+ Extra costs" class="text-end money text-warning">{{ money($item->landed_unit_cost - $item->unit_price_fx) }}</td>
                                        <td data-label="Real cost" class="text-end money fw-bold">{{ money($item->landed_unit_cost) }}</td>
                                        <td data-label="Suggested price" class="text-end money text-success">{{ money(round($item->landed_unit_cost * (1 + $item->product->effectiveMarkup() / 100), -1)) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer small text-muted">When the goods are received, this real cost is used for profit on every sale. Update selling prices from <a href="{{ route('products.bulk-price') }}">Change Prices</a>.</div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.vertical', ['subtitle' => $po->number])

@section('content')
    <x-page-header :title="$po->number . ' - ' . $po->supplier->name" :crumbs="['Purchase Orders' => route('purchase-orders.index'), $po->number]">
        @if ($po->isEditable())
            <a href="{{ route('purchase-orders.edit', $po) }}" class="btn btn-light">Edit</a>
        @endif
        @if ($po->status === 'draft')
            <form method="POST" action="{{ route('purchase-orders.status', $po) }}">@csrf<input type="hidden" name="status" value="ordered"><button class="btn btn-soft-primary">Mark as ordered</button></form>
        @endif
        <a href="{{ route('purchase-orders.costing', $po) }}" class="btn btn-soft-info"><iconify-icon icon="solar:calculator-outline" class="align-middle me-1"></iconify-icon>Import costing</a>
        @if ($po->canReceive())
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#receiveModal"><iconify-icon icon="solar:box-outline" class="align-middle me-1"></iconify-icon>Receive goods (GRN)</button>
        @endif
        @if ($po->payable_amount > 0 && $po->balance() > 0)
            <a href="{{ route('supplier-payments.create', ['supplier_id' => $po->supplier_id]) }}" class="btn btn-soft-success">Pay supplier</a>
        @endif
    </x-page-header>

    @include('purchasing.partials.workflow', ['po' => $po])

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><x-kpi label="Supplier value" :value="$po->currency . ' ' . number_format($po->subtotal_fx, 2)" icon="solar:dollar-outline" color="secondary" :sub="'@ ' . $po->exchange_rate . ' = ' . money($po->supplierCostLkr())" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Import charges" :value="money($po->charges_total)" icon="solar:globus-outline" color="warning" :sub="$po->supplierCostLkr() > 0 ? number_format($po->charges_total / $po->supplierCostLkr() * 100, 1) . '% of goods value' : ''" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Landed total" :value="money($po->landed_total)" icon="solar:calculator-outline" color="primary" :sub="'Allocated by ' . ($po->allocation_method === 'qty' ? 'quantity' : 'value')" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Payable / paid" :value="money($po->payable_amount)" icon="solar:wallet-money-outline" color="success" :sub="'Paid ' . money($po->paid_amount) . ($po->due_date ? ' &middot; due ' . $po->due_date->format('d M') : '')" /></div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title mb-0">Products & landed cost</h5>{!! status_badge($po->status) !!}</div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Product</th><th class="text-end">Ordered</th><th class="text-end">Received</th><th class="text-end">Unit ({{ $po->currency }})</th><th class="text-end">Landed / pair</th><th class="text-end">Landed value</th></tr></thead>
                            <tbody>
                                @foreach ($byProduct as $rows)
                                    @php $first = $rows->first(); @endphp
                                    <tr>
                                        <td class="td-title">
                                            <a href="{{ route('products.show', $first->product) }}" class="doc-link">{{ $first->product->code }}</a>
                                            <span class="small text-muted">{{ $first->product->brand?->name }} {{ $first->product->name }} {{ $first->product->colour }}</span>
                                            <div class="small text-muted mt-1">
                                                @foreach ($rows->sortBy(fn ($r) => (float) $r->variant->size) as $r)
                                                    <span class="badge bg-light text-dark fw-normal">{{ $r->variant->size }}: {{ $r->received_qty }}/{{ $r->qty }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td data-label="Ordered" class="text-end">{{ qty($rows->sum('qty')) }}</td>
                                        <td data-label="Received" class="text-end {{ $rows->sum('received_qty') < $rows->sum('qty') && $rows->sum('received_qty') > 0 ? 'text-warning fw-semibold' : '' }}">{{ qty($rows->sum('received_qty')) }}</td>
                                        <td data-label="Unit FX" class="text-end money">{{ number_format($first->unit_price_fx, 2) }}</td>
                                        <td data-label="Landed / pair" class="text-end money fw-semibold">{{ money($first->landed_unit_cost) }}</td>
                                        <td data-label="Landed value" class="text-end money">{{ money($rows->sum(fn ($r) => $r->qty * $r->landed_unit_cost)) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-semibold">
                                <tr><td>Total</td><td class="text-end">{{ qty($po->items->sum('qty')) }}</td><td class="text-end">{{ qty($po->items->sum('received_qty')) }}</td><td></td><td></td><td class="text-end money">{{ money($po->landed_total) }}</td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Order info</h5></div>
                <div class="card-body info-list">
                    <div><div class="label">Supplier</div><div class="value"><a href="{{ route('suppliers.show', $po->supplier) }}">{{ $po->supplier->name }}</a></div></div>
                    <div><div class="label">Order date</div><div class="value">{{ $po->order_date->format('d M Y') }}</div></div>
                    <div><div class="label">Expected</div><div class="value">{{ $po->expected_date?->format('d M Y') ?? '-' }}</div></div>
                    <div><div class="label">Shipment</div><div class="value">{{ $po->shipment_ref ?: '-' }}</div></div>
                    <div><div class="label">Exchange rate</div><div class="value">{{ $po->currency }} 1 = Rs {{ $po->exchange_rate }}</div></div>
                    <div><div class="label">Created by</div><div class="value">{{ $po->user?->name ?? '-' }}</div></div>
                </div>
                @if ($po->notes)<div class="card-footer small">{{ $po->notes }}</div>@endif
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Import charges</h5></div>
                <ul class="list-group list-group-flush">
                    @forelse ($po->charges as $c)
                        <li class="list-group-item d-flex justify-content-between"><span>{{ \App\Models\PurchaseOrder::CHARGE_TYPES[$c->type] ?? $c->type }} <span class="small text-muted">{{ $c->description }}</span></span><span class="money">{{ money($c->amount) }}</span></li>
                    @empty
                        <li class="list-group-item text-muted small">No charges yet. <a href="{{ route('purchase-orders.costing', $po) }}">Add import costs</a></li>
                    @endforelse
                </ul>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Goods received notes</h5></div>
                <ul class="list-group list-group-flush">
                    @forelse ($po->grns as $g)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><a href="{{ route($g->status === 'draft' ? 'grns.edit' : 'grns.show', $g) }}" class="fw-semibold">{{ $g->number }}</a><br><span class="small text-muted">{{ $g->received_date->format('d M Y') }} &middot; {{ $g->warehouse->code }}</span></span>
                            {!! status_badge($g->status) !!}
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">Nothing received yet.</li>
                    @endforelse
                </ul>
            </div>
            @if ($allocations->count())
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Payments</h5></div>
                    <ul class="list-group list-group-flush">
                        @foreach ($allocations as $a)
                            <li class="list-group-item d-flex justify-content-between"><span><a href="{{ route('payments.show', $a->payment) }}">{{ $a->payment->number }}</a> <span class="small text-muted">{{ $a->payment->methodSummary() }}</span></span><span class="money">{{ money($a->amount) }}</span></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    @if ($po->canReceive())
        <div class="modal fade" id="receiveModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="{{ route('grns.store', $po) }}">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title">Receive goods</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <p class="text-muted small">A GRN will be created with all outstanding quantities. Check the cartons, then adjust received and damaged pairs per size before posting.</p>
                        <div class="mb-3"><label class="form-label">Receive into</label>
                            <select name="warehouse_id" class="form-select">@foreach ($warehouses as $w)<option value="{{ $w->id }}" @selected($w->is_default)>{{ $w->name }}</option>@endforeach</select></div>
                        <div><label class="form-label">Received date</label><input type="date" name="received_date" value="{{ now()->format('Y-m-d') }}" class="form-control"></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-success">Start GRN</button></div>
                </form>
            </div>
        </div>
    @endif
@endsection

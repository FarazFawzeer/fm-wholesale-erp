@extends('layouts.vertical', ['subtitle' => 'GRN ' . $grn->number])

@section('content')
    <x-page-header :title="'GRN ' . $grn->number" :crumbs="['GRN' => route('grns.index'), $grn->number]">
        <button onclick="window.print()" class="btn btn-light"><iconify-icon icon="solar:printer-outline" class="align-middle me-1"></iconify-icon>Print</button>
    </x-page-header>

    @php
        $received = $grn->items->sum('received_qty');
        $damaged = $grn->items->sum('damaged_qty');
        $short = $grn->items->sum(fn ($i) => max(0, $i->ordered_qty - $i->received_qty));
    @endphp
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><x-kpi label="Pairs received" :value="qty($received)" icon="solar:box-outline" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Good stock" :value="qty($received - $damaged)" icon="solar:check-circle-outline" color="success" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Damaged" :value="qty($damaged)" icon="solar:danger-triangle-outline" color="danger" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Short shipped" :value="qty($short)" icon="solar:minus-circle-outline" color="warning" :sub="'Stock value ' . money($grn->total_value)" /></div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between gap-2">
            <span>PO <a href="{{ route('purchase-orders.show', $grn->purchaseOrder) }}">{{ $grn->purchaseOrder->number }}</a> &middot; {{ $grn->supplier->name }} &middot; {{ $grn->warehouse->name }} &middot; {{ $grn->received_date->format('d M Y') }}</span>
            {!! status_badge($grn->status) !!}
        </div>
        <div class="card-body">
            @foreach ($byProduct as $items)
                @php $p = $items->first()->variant->product; $items = $items->sortBy(fn ($i) => (float) $i->variant->size); @endphp
                <div class="mb-3">
                    <div class="fw-semibold">{{ $p->code }} - {{ $p->name }} {{ $p->colour }} <span class="small text-muted">@ {{ money($items->first()->unit_cost) }}</span></div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered text-center mb-0 mt-1">
                            <thead class="table-light"><tr><th class="text-start">Size</th>@foreach ($items as $i)<th>{{ $i->variant->size }}</th>@endforeach<th>Total</th></tr></thead>
                            <tbody>
                                <tr><td class="text-start small">Expected</td>@foreach ($items as $i)<td class="text-muted">{{ $i->ordered_qty }}</td>@endforeach<td>{{ $items->sum('ordered_qty') }}</td></tr>
                                <tr><td class="text-start small">Received</td>@foreach ($items as $i)<td class="{{ $i->received_qty < $i->ordered_qty ? 'text-warning fw-semibold' : '' }}">{{ $i->received_qty }}</td>@endforeach<td class="fw-semibold">{{ $items->sum('received_qty') }}</td></tr>
                                <tr><td class="text-start small text-danger">Damaged</td>@foreach ($items as $i)<td class="text-danger">{{ $i->damaged_qty ?: '' }}</td>@endforeach<td class="text-danger">{{ $items->sum('damaged_qty') ?: '' }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
            @if ($grn->notes)<div class="small text-muted">Notes: {{ $grn->notes }}</div>@endif
            <div class="small text-muted mt-2">Posted {{ $grn->posted_at?->format('d M Y H:i') }} by {{ $grn->user?->name }}</div>
        </div>
    </div>
@endsection

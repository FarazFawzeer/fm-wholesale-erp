@extends('layouts.vertical', ['subtitle' => 'Received Goods ' . $grn->number])

@section('content')
    <x-page-header :title="'Received goods ' . $grn->number" :crumbs="['Received Goods' => route('grns.index'), $grn->number]">
        <button onclick="window.print()" class="btn btn-light"><iconify-icon icon="solar:printer-outline" class="align-middle me-1"></iconify-icon>Print</button>
    </x-page-header>

    @php
        $received = $grn->items->sum('received_qty');
        $damaged = $grn->items->sum('damaged_qty');
        $short = $grn->items->sum(fn ($i) => max(0, $i->ordered_qty - $i->received_qty));
    @endphp
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><x-kpi label="Pairs received" :value="qty($received)" icon="solar:box-outline" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Good pairs" :value="qty($received - $damaged)" icon="solar:check-circle-outline" color="success" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Damaged" :value="qty($damaged)" icon="solar:danger-triangle-outline" color="danger" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Not received" :value="qty($short)" icon="solar:minus-circle-outline" color="warning" :sub="'Stock value ' . money($grn->total_value)" /></div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between gap-2">
            <span>Order <a href="{{ route('purchase-orders.show', $grn->purchaseOrder) }}">{{ $grn->purchaseOrder->number }}</a> &middot; {{ $grn->supplier->name }} &middot; {{ $grn->warehouse->name }} &middot; {{ $grn->received_date->format('d M Y') }}</span>
            {!! status_badge($grn->status) !!}
        </div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Product</th><th class="text-end">Ordered</th><th class="text-end">Received</th><th class="text-end">Damaged</th><th class="text-end">Real cost / pair</th></tr></thead>
                    <tbody>
                        @foreach ($grn->items as $i)
                            @php $p = $i->variant->product; @endphp
                            <tr>
                                <td class="td-title"><strong>{{ $p->name }}</strong> <span class="small text-muted">{{ $p->code }} &middot; Size {{ $p->sizeLabel() }} {{ $p->colour ? '· ' . $p->colour : '' }}</span></td>
                                <td data-label="Ordered" class="text-end">{{ $i->ordered_qty }}</td>
                                <td data-label="Received" class="text-end fw-semibold {{ $i->received_qty < $i->ordered_qty ? 'text-warning' : '' }}">{{ $i->received_qty }}</td>
                                <td data-label="Damaged" class="text-end {{ $i->damaged_qty ? 'text-danger fw-semibold' : 'text-muted' }}">{{ $i->damaged_qty ?: '-' }}</td>
                                <td data-label="Real cost" class="text-end money">{{ money($i->unit_cost) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer small text-muted">
            @if ($grn->notes)Notes: {{ $grn->notes }} &middot; @endif
            Added to stock {{ $grn->posted_at?->format('d M Y H:i') }} by {{ $grn->user?->name }}
        </div>
    </div>
@endsection

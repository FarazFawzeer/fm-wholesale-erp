@extends('layouts.vertical', ['subtitle' => 'Goods Received Notes'])

@section('content')
    <x-page-header title="Goods Received Notes (GRN)" :crumbs="['Import & Purchasing', 'GRN']">
        <a href="{{ route('purchase-orders.index', ['status' => 'costed']) }}" class="btn btn-primary">Receive a shipment</a>
    </x-page-header>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack mb-0">
                    <thead><tr><th>GRN</th><th>PO</th><th>Supplier</th><th>Received</th><th>Store</th><th class="text-end">Pairs</th><th class="text-end">Damaged</th><th class="text-end">Value</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($grns as $g)
                            <tr>
                                <td class="td-title"><a href="{{ route($g->status === 'draft' ? 'grns.edit' : 'grns.show', $g) }}" class="doc-link">{{ $g->number }}</a></td>
                                <td data-label="PO"><a href="{{ route('purchase-orders.show', $g->purchaseOrder) }}">{{ $g->purchaseOrder->number }}</a></td>
                                <td data-label="Supplier">{{ $g->supplier->name }}</td>
                                <td data-label="Received">{{ $g->received_date->format('d M Y') }}</td>
                                <td data-label="Store">{{ $g->warehouse->code }}</td>
                                <td data-label="Pairs" class="text-end">{{ qty($g->received) }}</td>
                                <td data-label="Damaged" class="text-end {{ $g->damaged ? 'text-danger fw-semibold' : 'text-muted' }}">{{ qty($g->damaged) }}</td>
                                <td data-label="Value" class="text-end money">{{ money($g->total_value) }}</td>
                                <td data-label="Status">{!! status_badge($g->status) !!}</td>
                            </tr>
                        @empty
                            <x-empty :cols="9" text="No goods received yet." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($grns->hasPages())<div class="card-footer">{{ $grns->links() }}</div>@endif
    </div>
@endsection

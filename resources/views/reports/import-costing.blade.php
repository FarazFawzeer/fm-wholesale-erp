@extends('layouts.vertical', ['subtitle' => 'Import Costing'])

@section('content')
    <x-page-header title="Import Costing by Shipment" :crumbs="['Reports' => route('reports.index'), 'Import costing']" />

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead>
                        <tr><th>PO / shipment</th><th class="text-end">Pairs</th><th class="text-end">Goods (Rs)</th>
                            @foreach (\App\Models\PurchaseOrder::CHARGE_TYPES as $l)<th class="text-end">{{ $l }}</th>@endforeach
                            <th class="text-end">Landed total</th><th class="text-end">Uplift</th><th class="text-end">Per pair</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $po)
                            @php $goods = $po->supplierCostLkr(); @endphp
                            <tr>
                                <td class="td-title"><a href="{{ route('purchase-orders.costing', $po) }}" class="doc-link">{{ $po->number }}</a> <span class="small text-muted">{{ $po->supplier->name }} · {{ $po->shipment_ref }}</span></td>
                                <td data-label="Pairs" class="text-end">{{ qty($po->pairs) }}</td>
                                <td data-label="Goods" class="text-end money">{{ money($goods) }}</td>
                                @foreach (\App\Models\PurchaseOrder::CHARGE_TYPES as $k => $l)
                                    @php $v = $po->charges->where('type', $k)->sum('amount'); @endphp
                                    <td data-label="{{ $l }}" class="text-end money {{ $v ? '' : 'text-muted' }}">{{ $v ? money($v) : '-' }}</td>
                                @endforeach
                                <td data-label="Landed" class="text-end money fw-bold">{{ money($po->landed_total) }}</td>
                                <td data-label="Uplift" class="text-end"><span class="badge bg-warning-subtle text-warning">+{{ $goods > 0 ? number_format($po->charges_total / $goods * 100, 1) : 0 }}%</span></td>
                                <td data-label="Per pair" class="text-end money">{{ money($po->landed_total / max(1, $po->pairs)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

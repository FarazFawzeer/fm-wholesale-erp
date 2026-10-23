@extends('layouts.vertical', ['subtitle' => 'Reports'])

@section('content')
    <x-page-header title="Reports" :crumbs="['Reports']" />

    @php
        $groups = [
            'Stock' => [
                ['Stock valuation', 'reports.stock-valuation', 'solar:box-outline', 'Pairs and value at landed cost, by category and store'],
                ['Low stock & missing sizes', 'reports.low-stock', 'solar:box-minimalistic-outline', 'Below re-order level or sizes sold out'],
                ['Import costing', 'reports.import-costing', 'solar:globus-outline', 'Freight, duty and charges per shipment'],
                ['Stock movements', 'stock.movements', 'solar:transfer-horizontal-outline', 'Every stock in / out with reference'],
            ],
            'Sales & profit' => [
                ['Product profit', 'reports.product-profit', 'solar:graph-up-outline', 'Sales, landed cost and margin per product'],
                ['Invoice profit', 'reports.invoice-profit', 'solar:bill-list-outline', 'Gross profit per invoice and net after expenses'],
            ],
            'Customers & money' => [
                ['Customer statement', 'reports.statement', 'solar:document-text-outline', 'Invoices, returns and payments with running balance'],
                ['Outstanding & ageing', 'reports.outstanding', 'solar:alarm-outline', 'Who owes what, by days overdue'],
                ['Cash flow', 'reports.cash-flow', 'solar:wallet-money-outline', 'Money in and out by type and week'],
                ['Expected payments', 'reports.expected', 'solar:calendar-outline', 'Incoming and outgoing for the coming weeks'],
                ['Daily cashbook', 'cashbook', 'solar:book-outline', 'Opening, in, out and closing per account'],
            ],
        ];
    @endphp

    @foreach ($groups as $title => $reports)
        <h6 class="text-uppercase text-muted fw-semibold small mt-2 mb-2">{{ $title }}</h6>
        <div class="row g-3 mb-3">
            @foreach ($reports as [$label, $route, $icon, $desc])
                <div class="col-sm-6 col-xl-3">
                    <a href="{{ route($route) }}" class="card h-100 text-reset mb-0">
                        <div class="card-body d-flex gap-3">
                            <div class="avatar-md flex-shrink-0 rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center"><iconify-icon icon="{{ $icon }}" class="fs-24"></iconify-icon></div>
                            <div><div class="fw-semibold">{{ $label }}</div><div class="small text-muted">{{ $desc }}</div></div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endforeach
@endsection

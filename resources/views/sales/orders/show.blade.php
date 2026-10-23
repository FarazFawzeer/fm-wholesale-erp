@extends('layouts.vertical', ['subtitle' => $doc->number])

@section('content')
    <x-page-header :title="$doc->label() . ' ' . $doc->number" :crumbs="[$doc->label() . 's' => route($doc->type === 'quotation' ? 'quotations.index' : 'sales-orders.index'), $doc->number]">
        <button onclick="window.print()" class="btn btn-light"><iconify-icon icon="solar:printer-outline" class="align-middle me-1"></iconify-icon>Print</button>
        @if ($doc->status === 'open')
            <a href="{{ route('sales-docs.edit', $doc) }}" class="btn btn-light">Edit</a>
        @endif
        @if ($doc->type === 'quotation' && in_array($doc->status, ['open', 'accepted']))
            <form method="POST" action="{{ route('sales-docs.convert', $doc) }}">@csrf<button class="btn btn-soft-primary">Convert to sales order</button></form>
        @endif
        @if (in_array($doc->status, ['open', 'accepted']))
            <form method="POST" action="{{ route('sales-docs.convert', $doc) }}">@csrf<input type="hidden" name="to" value="invoice"><button class="btn btn-primary">Create invoice</button></form>
        @endif
        @if ($doc->type === 'quotation' && $doc->status === 'open')
            <form method="POST" action="{{ route('sales-docs.status', $doc) }}">@csrf<input type="hidden" name="status" value="rejected"><button class="btn btn-soft-danger">Mark rejected</button></form>
        @endif
    </x-page-header>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0">Items</h5>{!! status_badge($doc->status) !!}</div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Product / sizes</th><th class="text-end">Pairs</th><th class="text-end">Price</th><th class="text-end">Disc</th><th class="text-end">Amount</th></tr></thead>
                            <tbody>
                                @foreach ($byProduct as $rows)
                                    @php $f = $rows->first(); @endphp
                                    <tr>
                                        <td class="td-title"><strong>{{ $f->product->code }}</strong> <span class="small text-muted">{{ $f->product->name }} {{ $f->product->colour }}</span>
                                            <div class="mt-1">@foreach ($rows->sortBy(fn ($r) => (float) $r->variant->size) as $r)<span class="badge bg-light text-dark fw-normal me-1">{{ $r->variant->size }} &times; {{ $r->qty }}</span>@endforeach</div></td>
                                        <td data-label="Pairs" class="text-end">{{ $rows->sum('qty') }}</td>
                                        <td data-label="Price" class="text-end money">{{ money($f->unit_price) }}</td>
                                        <td data-label="Disc" class="text-end">{{ $f->discount_percent > 0 ? $f->discount_percent . '%' : '-' }}</td>
                                        <td data-label="Amount" class="text-end money fw-semibold">{{ money($rows->sum('line_total')) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                @if ($doc->discount_amount > 0)<tr><td colspan="4" class="text-end">Discount</td><td class="text-end money text-danger">-{{ money($doc->discount_amount) }}</td></tr>@endif
                                <tr class="fw-bold"><td colspan="4" class="text-end">Total</td><td class="text-end money">{{ money($doc->total) }}</td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body info-list">
                    <div><div class="label">Customer</div><div class="value"><a href="{{ route('customers.show', $doc->customer) }}">{{ $doc->customer->name }}</a></div></div>
                    <div><div class="label">Date</div><div class="value">{{ $doc->date->format('d M Y') }}</div></div>
                    @if ($doc->valid_until)<div><div class="label">Valid until</div><div class="value">{{ $doc->valid_until->format('d M Y') }}</div></div>@endif
                    <div><div class="label">Created by</div><div class="value">{{ $doc->user?->name }}</div></div>
                    @if ($doc->convertedFrom)<div><div class="label">From quotation</div><div class="value"><a href="{{ route('sales-docs.show', $doc->convertedFrom) }}">{{ $doc->convertedFrom->number }}</a></div></div>@endif
                    @foreach ($doc->invoices as $inv)<div><div class="label">Invoice</div><div class="value"><a href="{{ route('invoices.show', $inv) }}">{{ $inv->number }}</a></div></div>@endforeach
                </div>
                @if ($doc->notes)<div class="card-footer small">{{ $doc->notes }}</div>@endif
            </div>
        </div>
    </div>
@endsection

@extends('layouts.vertical', ['subtitle' => $invoice->number])

@section('content')
    @php $c = $invoice->customer; $wa = $c->whatsappNumber(); @endphp
    <x-page-header :title="'Invoice ' . $invoice->number" :crumbs="['Invoices' => route('invoices.index'), $invoice->number]">
        <a href="{{ route('invoices.print', $invoice) }}" class="btn btn-light" target="_blank"><iconify-icon icon="solar:printer-outline" class="align-middle me-1"></iconify-icon>Print</a>
        @if ($invoice->delivery_status !== 'delivered')
            <a href="{{ route('deliveries.create', $invoice) }}" class="btn btn-soft-primary"><iconify-icon icon="solar:delivery-outline" class="align-middle me-1"></iconify-icon>Deliver</a>
        @endif
        @if ($invoice->balance() > 0)
            <a href="{{ route('receipts.create', ['customer_id' => $c->id, 'invoice_id' => $invoice->id]) }}" class="btn btn-success">Receive payment</a>
        @endif
        <a href="{{ route('credit-notes.create', $invoice) }}" class="btn btn-soft-danger">Return / credit note</a>
        @if ($wa)
            <a target="_blank" rel="noopener" class="btn btn-soft-success" href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Invoice ' . $invoice->number . ' from ' . setting('company_name') . ': ' . money($invoice->total) . ', due ' . $invoice->due_date->format('d M Y') . '. Balance ' . money($invoice->balance()) . '.') }}"><iconify-icon icon="ic:baseline-whatsapp" class="align-middle"></iconify-icon></a>
        @endif
    </x-page-header>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><x-kpi label="Invoice total" :value="money($invoice->total)" icon="solar:bill-list-outline" :sub="$invoice->discount_amount > 0 ? 'Discount ' . money($invoice->discount_amount) : ''" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Balance due" :value="money($invoice->balance())" icon="solar:wallet-money-outline" :color="$invoice->balance() > 0 ? 'danger' : 'success'" :sub="'Due ' . $invoice->due_date->format('d M Y') . ($invoice->daysOverdue() ? ' &middot; <span class=\'text-danger\'>' . $invoice->daysOverdue() . ' days overdue</span>' : '')" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Gross profit" :value="money($invoice->profit())" icon="solar:graph-up-outline" color="success" :sub="($invoice->total > 0 ? number_format($invoice->profit() / $invoice->total * 100, 1) : 0) . '% margin'" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Delivered" :value="qty($invoice->lines->sum('delivered_qty')) . ' / ' . qty($invoice->lines->sum('qty'))" icon="solar:delivery-outline" color="info" :sub="'pairs &middot; ' . ucfirst($invoice->delivery_status)" /></div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Items</h5>
                    <span>{!! status_badge($invoice->sale_type) !!} {!! status_badge($invoice->daysOverdue() ? 'overdue' : $invoice->payment_status) !!} {!! status_badge($invoice->delivery_status) !!}</span>
                </div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Product / sizes</th><th class="text-end">Pairs</th><th class="text-end">Price</th><th class="text-end">Disc</th><th class="text-end">Amount</th><th class="text-end">Profit</th></tr></thead>
                            <tbody>
                                @foreach ($byProduct as $rows)
                                    @php $f = $rows->first(); $amount = $rows->sum('line_total'); $cost = $rows->sum(fn ($r) => $r->qty * $r->unit_cost); @endphp
                                    <tr>
                                        <td class="td-title">
                                            <strong>{{ $f->product->code }}</strong> <span class="small text-muted">{{ $f->product->name }} {{ $f->product->colour }}</span>
                                            <div class="mt-1">@foreach ($rows->sortBy(fn ($r) => (float) $r->variant->size) as $r)<span class="badge bg-light text-dark fw-normal me-1">{{ $r->variant->size }} &times; {{ $r->qty }}@if ($r->delivered_qty < $r->qty) <span class="text-warning">({{ $r->delivered_qty }} sent)</span>@endif @if ($r->returned_qty)<span class="text-danger">-{{ $r->returned_qty }}</span>@endif</span>@endforeach</div>
                                        </td>
                                        <td data-label="Pairs" class="text-end">{{ $rows->sum('qty') }}</td>
                                        <td data-label="Price" class="text-end money">{{ money($f->unit_price) }}</td>
                                        <td data-label="Discount" class="text-end">{{ $f->discount_percent > 0 ? $f->discount_percent . '%' : '-' }}</td>
                                        <td data-label="Amount" class="text-end money fw-semibold">{{ money($amount) }}</td>
                                        <td data-label="Profit" class="text-end money text-success">{{ money($amount - $cost) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr><td colspan="4" class="text-end">Gross</td><td class="text-end money">{{ money($invoice->subtotal) }}</td><td></td></tr>
                                @if ($invoice->discount_amount > 0)<tr><td colspan="4" class="text-end">Discount</td><td class="text-end money text-danger">-{{ money($invoice->discount_amount) }}</td><td></td></tr>@endif
                                <tr class="fw-bold"><td colspan="4" class="text-end">Total</td><td class="text-end money">{{ money($invoice->total) }}</td><td class="text-end money text-success">{{ money($invoice->profit()) }}</td></tr>
                                @if ($invoice->credited_amount > 0)<tr><td colspan="4" class="text-end">Credit notes</td><td class="text-end money text-danger">-{{ money($invoice->credited_amount) }}</td><td></td></tr>@endif
                                <tr><td colspan="4" class="text-end">Paid</td><td class="text-end money text-success">-{{ money($invoice->paid_amount) }}</td><td></td></tr>
                                <tr class="fw-bold"><td colspan="4" class="text-end">Balance</td><td class="text-end money {{ $invoice->balance() > 0 ? 'text-danger' : '' }}">{{ money($invoice->balance()) }}</td><td></td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body info-list">
                    <div><div class="label">Customer</div><div class="value"><a href="{{ route('customers.show', $c) }}">{{ $c->name }}</a><div class="small text-muted">{{ $c->group?->name }}</div></div></div>
                    <div><div class="label">Date</div><div class="value">{{ $invoice->date->format('d M Y') }}</div></div>
                    <div><div class="label">Store</div><div class="value">{{ $invoice->warehouse->name }}</div></div>
                    <div><div class="label">Sales person</div><div class="value">{{ $invoice->user?->name ?? '-' }}</div></div>
                    @if ($invoice->salesOrder)<div><div class="label">From</div><div class="value"><a href="{{ route('sales-docs.show', $invoice->salesOrder) }}">{{ $invoice->salesOrder->number }}</a></div></div>@endif
                </div>
                @if ($invoice->notes)<div class="card-footer small">{{ $invoice->notes }}</div>@endif
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Payments</h5></div>
                <ul class="list-group list-group-flush">
                    @forelse ($invoice->allocations->where('amount', '>', 0) as $a)
                        <li class="list-group-item d-flex justify-content-between">
                            <span><a href="{{ route('payments.show', $a->payment) }}">{{ $a->payment->number }}</a><br><span class="small text-muted">{{ $a->payment->date->format('d M Y') }} &middot; {{ $a->payment->methodSummary() }}</span></span>
                            <strong class="money">{{ money($a->amount) }}</strong>
                        </li>
                    @empty
                        <li class="list-group-item small text-muted">No payments yet.</li>
                    @endforelse
                </ul>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Deliveries & returns</h5></div>
                <ul class="list-group list-group-flush">
                    @foreach ($invoice->deliveryNotes as $d)
                        <li class="list-group-item d-flex justify-content-between"><a href="{{ route('deliveries.show', $d) }}">{{ $d->number }}</a><span class="small text-muted">{{ $d->date->format('d M Y') }}</span></li>
                    @endforeach
                    @foreach ($invoice->creditNotes as $cn)
                        <li class="list-group-item d-flex justify-content-between"><a href="{{ route('credit-notes.show', $cn) }}" class="text-danger">{{ $cn->number }}</a><span class="money text-danger">-{{ money($cn->total) }}</span></li>
                    @endforeach
                    @if ($invoice->deliveryNotes->isEmpty() && $invoice->creditNotes->isEmpty())
                        <li class="list-group-item small text-muted">Not delivered yet.</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endsection

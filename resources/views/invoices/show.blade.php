@extends('layouts.vertical', ['subtitle' => $invoice->number])

@php
    $wa = $invoice->customer->whatsappNumber();
    $waLink = 'https://wa.me/' . ($wa ?? '') . '?text=' . rawurlencode($invoice->whatsappText());
    $net = $invoice->total - $invoice->returned_amount;
    $balance = $invoice->balance();
    $c = $invoice->customer;
@endphp

@section('content')
    <x-page-header :title="'Invoice ' . $invoice->number" :crumbs="['Sales' => route('invoices.index'), $invoice->number]">
        <a href="{{ route('invoices.print', $invoice) }}" class="btn btn-dark"><iconify-icon icon="solar:document-text-outline" class="align-middle me-1"></iconify-icon>View invoice</a>
        <a href="{{ route('invoices.create') }}" class="btn btn-light d-none d-sm-inline-block"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>New sale</a>
    </x-page-header>

    {{-- Summary --}}
    <div class="card inv-hero">
        <div class="card-body">
            <div class="inv-hero-top">
                <div class="min-w-0">
                    <div class="text-muted small">Customer</div>
                    <a href="{{ route('customers.show', $c) }}" class="inv-customer">{{ $c->name }}</a>
                    <div class="small text-muted">
                        {{ collect([$c->shop_name, $c->city, $c->phone])->filter()->implode(' · ') }}
                    </div>
                </div>
                <div class="text-sm-end">
                    {!! status_badge($invoice->payment_status) !!}
                    <div class="small text-muted mt-1">{{ $invoice->date->format('d M Y') }} &middot; {{ $invoice->totalPairs() }} pairs</div>
                </div>
            </div>
            <div class="inv-figures">
                <div><span>Bill total</span><b>{{ money($net) }}</b></div>
                <div><span>Paid</span><b class="text-success">{{ money($invoice->paid_amount) }}</b></div>
                <div class="{{ $balance > 0 ? 'is-due' : 'is-clear' }}"><span>Balance</span><b>{{ money($balance) }}</b></div>
            </div>

            @if ($balance > 0)
                <div class="due-banner mt-3 mb-0">
                    <iconify-icon icon="solar:bell-bing-bold" class="due-icon"></iconify-icon>
                    <div class="flex-grow-1">
                        <div class="fw-bold">Not paid yet ({{ $invoice->daysUnpaid() === 0 ? 'today' : $invoice->daysUnpaid() . ' ' . Str::plural('day', $invoice->daysUnpaid()) }})</div>
                        @if ($customerDue - $balance > 0.009)<div class="small">Total owed with older bills: <b>{{ money($customerDue) }}</b></div>@endif
                    </div>
                    <a href="{{ route('payments.receive', ['customer' => $invoice->customer_id, 'invoice' => $invoice->id]) }}" class="btn btn-light btn-sm fw-semibold"><iconify-icon icon="solar:wallet-money-outline" class="align-middle me-1"></iconify-icon>Get paid</a>
                </div>
            @endif
        </div>
    </div>

    {{-- Send --}}
    <div class="card">
        <div class="card-body">
            <div class="fw-semibold mb-2">Send or print the invoice</div>
            <div class="send-actions">
                <a href="{{ route('invoices.print', $invoice) }}" class="send-btn dark"><iconify-icon icon="solar:document-text-outline"></iconify-icon><span>View / PDF</span></a>
                <a href="{{ $waLink }}" target="_blank" rel="noopener" class="send-btn wa"><i class="bx bxl-whatsapp"></i><span>WhatsApp</span></a>
                <button type="button" class="send-btn d-none js-native-share" data-share-title="Invoice {{ $invoice->number }}" data-share-text="{{ $invoice->whatsappText() }}"><iconify-icon icon="solar:share-outline"></iconify-icon><span>Share</span></button>
                <button type="button" class="send-btn" data-copy="{{ $invoice->shareUrl() }}"><iconify-icon icon="solar:link-outline"></iconify-icon><span class="js-copy-label">Copy link</span></button>
                @if ($c->email)
                    <a href="mailto:{{ $c->email }}?subject={{ rawurlencode('Invoice ' . $invoice->number . ' - ' . setting('company_name')) }}&body={{ rawurlencode($invoice->whatsappText()) }}" class="send-btn"><iconify-icon icon="solar:letter-outline"></iconify-icon><span>Email</span></a>
                @endif
            </div>
            @if (! $wa)<div class="small text-muted mt-2">No phone number saved for this customer - WhatsApp will ask you to pick the contact.</div>@endif
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Items sold</h5>
                    <span class="small text-muted">{{ $groups->count() }} {{ Str::plural('product', $groups->count()) }} &middot; {{ $invoice->totalPairs() }} pairs</span>
                </div>
                <div class="card-body pt-2">
                    @foreach ($groups as $items)
                        @php $p = $items->first()->product; $returned = $items->sum('returned_qty'); @endphp
                        <div class="inv-item">
                            <a href="{{ route('products.show', $p) }}"><x-thumb :product="$p" :size="64" /></a>
                            <div class="inv-item-main">
                                <div class="d-flex justify-content-between gap-2">
                                    <div class="min-w-0">
                                        <span class="item-no">{{ $p->code }}</span>
                                        <a href="{{ route('products.show', $p) }}" class="d-block fw-bold text-body text-truncate">{{ $p->name }}</a>
                                    </div>
                                    <strong class="money text-nowrap">{{ money($items->sum('line_total')) }}</strong>
                                </div>
                                <div class="small text-muted">
                                    {{ $p->sizeLabel() ? 'Size ' . $p->sizeLabel() . ' · ' : '' }}{{ $items->sum('qty') }} pairs &times; {{ money($items->first()->unit_price) }}
                                </div>
                                <div class="colour-list mt-1">
                                    @foreach ($items as $it)<x-colour-chip :name="$it->colour->colour" :qty="$it->qty" />@endforeach
                                </div>
                                @if ($returned)<div class="small text-danger mt-1"><iconify-icon icon="solar:undo-left-round-outline" class="align-middle"></iconify-icon> {{ $returned }} pairs returned</div>@endif
                            </div>
                        </div>
                    @endforeach

                    <div class="totals-box ms-auto mt-3">
                        <div class="row-total"><span>Amount ({{ $invoice->totalPairs() }} pairs)</span><span class="money">{{ money($invoice->subtotal) }}</span></div>
                        @if ($invoice->discount > 0)<div class="row-total"><span>Discount</span><span class="money">- {{ money($invoice->discount) }}</span></div>@endif
                        <div class="row-total grand"><span>Total</span><span class="money">{{ money($invoice->total) }}</span></div>
                        @if ($invoice->returned_amount > 0)<div class="row-total text-danger"><span>Returned</span><span class="money">- {{ money($invoice->returned_amount) }}</span></div>@endif
                        <div class="row-total"><span>Paid</span><span class="money text-success">{{ money($invoice->paid_amount) }}</span></div>
                        <div class="row-total fw-bold"><span>Balance</span><span class="money {{ $balance > 0 ? 'text-danger' : '' }}">{{ money($balance) }}</span></div>
                    </div>
                    @if ($invoice->notes)<div class="inv-note mt-3"><iconify-icon icon="solar:notes-outline"></iconify-icon>{{ $invoice->notes }}</div>@endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Payments</h5></div>
                <div class="card-body py-2">
                    @forelse ($invoice->allocations as $a)
                        <a href="{{ route('payments.show', $a->payment) }}" class="pay-row">
                            <span class="pay-icon"><iconify-icon icon="{{ \App\Models\Payment::ICONS[$a->payment->lines->first()?->method] ?? 'solar:wallet-outline' }}"></iconify-icon></span>
                            <span class="flex-grow-1 min-w-0">
                                <span class="d-block fw-semibold">{{ $a->payment->methodSummary() ?: 'Payment' }}</span>
                                <span class="small text-muted">{{ $a->payment->number }} &middot; {{ $a->payment->date->format('d M Y') }}
                                    @foreach ($a->payment->lines->whereNotNull('cheque_id') as $l)&middot; Chq {{ $l->cheque?->cheque_no }}@endforeach</span>
                            </span>
                            <strong class="money">{{ money($a->amount) }}</strong>
                        </a>
                    @empty
                        <div class="text-muted small py-2">Nothing paid yet.</div>
                    @endforelse
                </div>
            </div>

            @if ($invoice->returns->count())
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Returns</h5></div>
                    <div class="list-group list-group-flush">
                        @foreach ($invoice->returns as $r)
                            <a href="{{ route('returns.show', $r) }}" class="list-group-item list-group-item-action d-flex justify-content-between"><span><span class="doc-link">{{ $r->number }}</span> <small class="text-muted">{{ $r->date->format('d M') }} {{ $r->reason }}</small></span><strong class="money">- {{ money($r->total) }}</strong></a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <dl class="inv-meta mb-3">
                        <div><dt>Invoice no</dt><dd>{{ $invoice->number }}</dd></div>
                        <div><dt>Date</dt><dd>{{ $invoice->date->format('d M Y') }}</dd></div>
                        <div><dt>Sold by</dt><dd>{{ $invoice->user?->name ?? '-' }}</dd></div>
                        <div><dt>Saved</dt><dd>{{ $invoice->created_at->format('d M Y, h:i A') }}</dd></div>
                        @if (auth()->user()->isManager())
                            <div><dt>Profit</dt><dd class="{{ $invoice->profit() < 0 ? 'text-danger' : 'text-success' }}">{{ money($invoice->profit()) }}</dd></div>
                        @endif
                    </dl>
                    <a href="{{ route('returns.customer', $invoice) }}" class="btn btn-soft-secondary w-100"><iconify-icon icon="solar:undo-left-round-outline" class="align-middle me-1"></iconify-icon>Customer returned goods</a>
                </div>
            </div>
        </div>
    </div>
@endsection

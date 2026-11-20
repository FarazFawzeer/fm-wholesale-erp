@extends('layouts.vertical', ['subtitle' => $return->number])

@php
    $docUrl = $isCustomer ? route('invoices.show', $doc) : route('purchases.show', $doc);
    $partyUrl = $isCustomer ? route('customers.show', $party) : route('suppliers.show', $party);
    $pairs = $return->items->sum('qty');
@endphp

@section('content')
    <x-page-header :title="'Return ' . $return->number" :crumbs="['Returns' => route('returns.index'), $return->number]">
        <a href="{{ route('returns.print', $return) }}" class="btn btn-dark"><iconify-icon icon="solar:document-text-outline" class="align-middle me-1"></iconify-icon>Return note</a>
    </x-page-header>

    <div class="card inv-hero">
        <div class="card-body">
            <div class="inv-hero-top">
                <div class="d-flex gap-3 align-items-center min-w-0">
                    <span class="ret-icon {{ $isCustomer ? 'in' : 'out' }}"><iconify-icon icon="{{ $isCustomer ? 'solar:box-minimalistic-outline' : 'solar:delivery-outline' }}"></iconify-icon></span>
                    <div class="min-w-0">
                        <div class="text-muted small">{{ $isCustomer ? 'Returned by customer' : 'Sent back to supplier' }}</div>
                        <a href="{{ $partyUrl }}" class="inv-customer text-truncate">{{ $party->name }}</a>
                    </div>
                </div>
                <div class="text-sm-end">
                    <span class="ret-type {{ $isCustomer ? 'in' : 'out' }}">{{ $isCustomer ? 'From customer' : 'To supplier' }}</span>
                    <div class="small text-muted mt-1">{{ $return->date->format('d M Y') }}</div>
                </div>
            </div>
            <div class="inv-figures">
                <div><span>Pairs</span><b>{{ $pairs }}</b></div>
                <div><span>Return amount</span><b>{{ money($return->total) }}</b></div>
                <div><span>From bill</span><b><a href="{{ $docUrl }}" class="text-body">{{ $doc->number }}</a></b></div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ $isCustomer ? 'Pairs back in stock' : 'Pairs taken out of stock' }}</h5>
                    <span class="small text-muted">{{ $pairs }} pairs</span>
                </div>
                <div class="card-body pt-2">
                    @foreach ($groups as $items)
                        @php $p = $items->first()->colour->product; @endphp
                        <div class="inv-item">
                            <a href="{{ route('products.show', $p) }}"><x-thumb :product="$p" :size="56" /></a>
                            <div class="inv-item-main">
                                <div class="d-flex justify-content-between gap-2">
                                    <div class="min-w-0">
                                        <span class="item-no">{{ $p->code }}</span>
                                        <a href="{{ route('products.show', $p) }}" class="d-block fw-bold text-body text-truncate">{{ $p->name }}</a>
                                    </div>
                                    <strong class="money text-nowrap">{{ money($items->sum('line_total')) }}</strong>
                                </div>
                                <div class="small text-muted">{{ $items->sum('qty') }} pairs &times; {{ money($items->first()->unit_price) }}</div>
                                <div class="colour-list mt-1">@foreach ($items as $it)<x-colour-chip :name="$it->colour->title" :qty="$it->qty" />@endforeach</div>
                            </div>
                        </div>
                    @endforeach
                    <div class="totals-box ms-auto mt-3">
                        <div class="row-total grand mt-0 pt-0 border-0"><span>Return total</span><span class="money">{{ money($return->total) }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Money</h5></div>
                <div class="card-body">
                    <dl class="inv-meta mb-0">
                        <div><dt>{{ $isCustomer ? 'Invoice' : 'Purchase' }}</dt><dd><a href="{{ $docUrl }}">{{ $doc->number }}</a></dd></div>
                        <div><dt>Bill total now</dt><dd>{{ money($doc->total - $doc->returned_amount) }}</dd></div>
                        <div><dt>Paid on bill</dt><dd>{{ money($doc->paid_amount) }}</dd></div>
                        <div><dt>Bill balance</dt><dd class="{{ $doc->balance() > 0 ? 'text-danger' : 'text-success' }}">{{ money($doc->balance()) }}</dd></div>
                        <div><dt>{{ $isCustomer ? 'Customer owes (all bills)' : 'You owe (all bills)' }}</dt><dd class="{{ $partyBalance > 0.009 ? 'text-danger' : '' }}">{{ money(max(0, $partyBalance)) }}</dd></div>
                    </dl>
                    @if ($credit > 0.009)
                        <div class="ret-outcome credit mt-3">
                            <b>{{ money($credit) }} credit</b> {{ $isCustomer ? 'for this customer. It will be taken off their next bill automatically.' : 'with this supplier. It will be taken off your next purchase automatically.' }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <dl class="inv-meta mb-0">
                        <div><dt>Return no</dt><dd>{{ $return->number }}</dd></div>
                        <div><dt>Date</dt><dd>{{ $return->date->format('d M Y') }}</dd></div>
                        <div><dt>Reason</dt><dd>{{ $return->reason ?: '-' }}</dd></div>
                        <div><dt>Saved by</dt><dd>{{ $return->user?->name ?? '-' }}</dd></div>
                        <div><dt>Saved</dt><dd>{{ $return->created_at->format('d M Y, h:i A') }}</dd></div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection

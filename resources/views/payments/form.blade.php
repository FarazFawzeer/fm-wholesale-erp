@php
    $isCustomer = $partyType === 'customer';
    $title = $isCustomer ? 'Get paid by customer' : 'Pay supplier';
@endphp
@extends('layouts.vertical', ['subtitle' => $title])

@section('content')
    <x-page-header :title="$title" :crumbs="[$isCustomer ? 'Money in' : 'Money out']" />

    {{-- 1. pick who (reloads the page with their bills) --}}
    <div class="card step-card">
        <div class="card-header"><span class="step-no">1</span><h5 class="card-title mb-0">{{ $isCustomer ? 'Customer' : 'Supplier' }}</h5></div>
        <div class="card-body">
            <form method="GET">
                <select name="{{ $partyType }}" class="form-select" data-choices data-placeholder="Choose..." onchange="this.form.submit()">
                    <option value="">{{ $isCustomer ? 'Choose a customer who owes money...' : 'Choose a supplier you owe...' }}</option>
                    @foreach ($parties as $p)<option value="{{ $p->id }}" @selected($party?->id == $p->id)>{{ $p->name }} - {{ money($p->due) }}</option>@endforeach
                </select>
            </form>
            @if ($parties->isEmpty() && ! $party)<div class="text-muted small mt-2">{{ $isCustomer ? 'No customer owes money right now.' : 'You do not owe any supplier right now.' }}</div>@endif
        </div>
    </div>

    @if ($party)
        <form method="POST" action="{{ route('payments.store') }}">
            @csrf
            <input type="hidden" name="party_type" value="{{ $partyType }}">
            <input type="hidden" name="party_id" value="{{ $party->id }}">
            <input type="hidden" name="first_doc" value="{{ $firstDoc }}">
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ $isCustomer ? 'Owes you' : 'You owe' }}</h5>
                            <span class="fs-4 fw-bold {{ $isCustomer ? 'text-danger' : 'text-warning' }} money">{{ money($due) }}</span>
                        </div>
                        <ul class="list-group list-group-flush">
                            @if ($openingDue > 0)
                                <li class="list-group-item d-flex justify-content-between"><span>Old balance</span><strong class="money">{{ money($openingDue) }}</strong></li>
                            @endif
                            @foreach ($bills as $b)
                                <li class="list-group-item d-flex justify-content-between align-items-center {{ $firstDoc == $b->id ? 'bg-primary-subtle' : '' }}">
                                    <span><a href="{{ $isCustomer ? route('invoices.show', $b) : route('purchases.show', $b) }}" class="fw-semibold">{{ $b->number }}</a>
                                        <small class="text-muted">{{ $b->date->format('d M Y') }} &middot; {{ (int) $b->date->diffInDays(now()->startOfDay()) }} days</small></span>
                                    <strong class="money">{{ money($b->balance()) }}</strong>
                                </li>
                            @endforeach
                        </ul>
                        <div class="card-footer small text-muted">The money is put on the {{ $firstDoc ? 'chosen bill first, then the' : '' }} oldest bills first.</div>
                    </div>
                </div>
                <div class="col-lg-6">
                    @include('partials.payment-box', ['step' => 2, 'title' => 'How much & how', 'target' => $due, 'balanceLabel' => 'Still to pay after this'])
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-5"><label class="form-label small">Date</label><input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" class="form-control" required></div>
                                <div class="col-7"><label class="form-label small">Note</label><input name="notes" value="{{ old('notes') }}" class="form-control" placeholder="Optional"></div>
                            </div>
                            <button class="btn {{ $isCustomer ? 'btn-success' : 'btn-warning' }} btn-lg w-100 mt-3"><iconify-icon icon="solar:check-circle-outline" class="align-middle me-1"></iconify-icon>Save payment</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
@endsection

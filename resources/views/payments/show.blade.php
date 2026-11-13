@extends('layouts.vertical', ['subtitle' => $payment->number])

@php $in = $payment->direction === 'in'; @endphp

@section('content')
    <x-page-header :title="($in ? 'Receipt ' : 'Payment ') . $payment->number" :crumbs="[($in ? 'Money received' : 'Money paid') => route('payments.index', ['direction' => $payment->direction]), $payment->number]">
        <button type="button" class="btn btn-soft-secondary" onclick="window.print()"><iconify-icon icon="solar:printer-outline" class="align-middle me-1"></iconify-icon>Print</button>
    </x-page-header>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
                        <div>
                            <div class="text-muted small">{{ $in ? 'Received from' : 'Paid to' }}</div>
                            @if ($party)<a href="{{ $in ? route('customers.show', $party) : route('suppliers.show', $party) }}" class="fw-bold fs-5">{{ $party->name }}</a>@endif
                            <div class="small text-muted">{{ $payment->date->format('d M Y') }}</div>
                        </div>
                        <div class="text-end"><div class="text-muted small">Amount</div><div class="fs-3 fw-bold money {{ $in ? 'text-success' : '' }}">{{ money($payment->total) }}</div></div>
                    </div>

                    <h6 class="text-muted text-uppercase small">How it was paid</h6>
                    <ul class="list-group mb-3">
                        @foreach ($payment->lines as $l)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><iconify-icon icon="{{ \App\Models\Payment::ICONS[$l->method] ?? 'solar:wallet-outline' }}" class="align-middle me-1 fs-18"></iconify-icon>{{ $l->methodLabel() }}
                                    @if ($l->cheque) <small class="text-muted">#{{ $l->cheque->cheque_no }} {{ $l->cheque->bank }} &middot; {{ $l->cheque->cheque_date->format('d M Y') }}</small> {!! status_badge($l->cheque->status) !!}@endif
                                    @if ($l->reference) <small class="text-muted">{{ $l->reference }}</small>@endif</span>
                                <strong class="money">{{ money($l->amount) }}</strong>
                            </li>
                        @endforeach
                    </ul>

                    <h6 class="text-muted text-uppercase small">For</h6>
                    <ul class="list-group">
                        @foreach ($payment->allocations->where('amount', '>', 0) as $a)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@if ($a->invoice)<a href="{{ route('invoices.show', $a->invoice) }}">{{ $a->invoice->number }}</a>@elseif ($a->purchase)<a href="{{ route('purchases.show', $a->purchase) }}">{{ $a->purchase->number }}</a>@else Old balance @endif</span>
                                <strong class="money">{{ money($a->amount) }}</strong>
                            </li>
                        @endforeach
                    </ul>
                    @if ($payment->notes)<div class="small text-muted mt-3" style="white-space: pre-line">{{ $payment->notes }}</div>@endif
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">{{ $in ? 'Customer still owes' : 'We still owe' }}</div>
                    <div class="fs-4 fw-bold money {{ $due > 0.009 ? 'text-danger' : 'text-success' }}">{{ money(max(0, $due)) }}</div>
                    <div class="small text-muted mt-2">Saved by {{ $payment->user?->name ?? '-' }} on {{ $payment->created_at->format('d M Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection

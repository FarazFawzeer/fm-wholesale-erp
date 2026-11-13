@extends('layouts.vertical', ['subtitle' => $customer->name])

@section('content')
    <x-page-header :title="$customer->name" :crumbs="['Customers' => route('customers.index'), $customer->name]">
        <a href="{{ route('invoices.create', ['customer' => $customer->id]) }}" class="btn btn-primary"><iconify-icon icon="solar:cart-large-2-outline" class="align-middle me-1"></iconify-icon>New sale</a>
        @if ($due > 0.009)
            <a href="{{ route('payments.receive', ['customer' => $customer->id]) }}" class="btn btn-success"><iconify-icon icon="solar:wallet-money-outline" class="align-middle me-1"></iconify-icon>Get paid</a>
        @endif
        <a href="{{ route('customers.statement', $customer) }}" class="btn btn-soft-secondary"><iconify-icon icon="solar:document-text-outline" class="align-middle me-1"></iconify-icon>Statement</a>
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-soft-secondary" title="Edit"><iconify-icon icon="solar:pen-outline" class="align-middle"></iconify-icon></a>
    </x-page-header>

    @if ($due > 0.009)
        @php
            $oldest = $customer->oldestUnpaidDate();
            $wa = $customer->whatsappNumber();
            $msg = 'Dear ' . $customer->name . ', this is a friendly reminder from ' . setting('company_name') . '. Your balance to pay is ' . money($due) . '. Thank you!';
        @endphp
        <div class="due-banner mb-3">
            <iconify-icon icon="solar:bell-bing-bold" class="due-icon"></iconify-icon>
            <div class="flex-grow-1">
                <div class="fw-bold fs-5">Owes {{ money($due) }}</div>
                <div class="small">Not paid since {{ $oldest?->format('d M Y') ?? '-' }} ({{ $oldest ? $oldest->diffForHumans(null, true) : '' }}) &middot; {{ $unpaid->count() }} unpaid {{ Str::plural('bill', $unpaid->count()) }}</div>
            </div>
            @if ($wa)
                <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode($msg) }}" target="_blank" rel="noopener" class="btn btn-sm btn-light"><i class="bx bxl-whatsapp align-middle me-1 text-success fs-16"></i>Remind</a>
            @endif
        </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="info-list">
                        @if ($customer->shop_name)<div><div class="label">Shop</div><div class="value">{{ $customer->shop_name }}</div></div>@endif
                        <div><div class="label">Phone</div><div class="value">@if ($customer->phone)<a href="tel:{{ $customer->phone }}">{{ $customer->phone }}</a>@else-@endif</div></div>
                        <div><div class="label">City</div><div class="value">{{ $customer->city ?: '-' }}</div></div>
                        <div><div class="label">Bought in total</div><div class="value money">{{ money($totalBought) }}</div></div>
                        <div><div class="label">Owes now</div><div class="value money {{ $due > 0.009 ? 'text-danger' : 'text-success' }}">{{ $due < -0.009 ? 'Credit ' . money(-$due) : money(max(0, $due)) }}</div></div>
                        @if ($openingDue > 0)<div><div class="label">Old balance unpaid</div><div class="value money">{{ money($openingDue) }}</div></div>@endif
                    </div>
                    @if ($customer->address)<div class="small text-muted mt-3">{{ $customer->address }}</div>@endif
                    @if ($customer->notes)<div class="small mt-2">{{ $customer->notes }}</div>@endif
                </div>
            </div>
            @if ($pendingCheques->count())
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Cheques not cleared yet</h5></div>
                    <ul class="list-group list-group-flush">
                        @foreach ($pendingCheques as $ch)
                            <li class="list-group-item d-flex justify-content-between"><span>#{{ $ch->cheque_no }} <small class="text-muted">{{ $ch->cheque_date->format('d M Y') }}</small></span><strong class="money">{{ money($ch->amount) }}</strong></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Bills</h5></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Invoice</th><th>Date</th><th class="text-end">Total</th><th class="text-end">Balance</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse ($invoices as $inv)
                                    <tr>
                                        <td class="td-title"><a href="{{ route('invoices.show', $inv) }}" class="doc-link">{{ $inv->number }}</a></td>
                                        <td data-label="Date">{{ $inv->date->format('d M Y') }}</td>
                                        <td data-label="Total" class="text-end money">{{ money($inv->total - $inv->returned_amount) }}</td>
                                        <td data-label="Balance" class="text-end money {{ $inv->balance() > 0 ? 'text-danger fw-semibold' : '' }}">{{ money($inv->balance()) }}</td>
                                        <td data-label="Status">{!! status_badge($inv->payment_status) !!}@if ($inv->balance() > 0) <small class="text-muted">{{ $inv->daysUnpaid() }}d</small>@endif</td>
                                    </tr>
                                @empty
                                    <x-empty :cols="5" text="No bills yet." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Payments received</h5></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>No</th><th>Date</th><th>Method</th><th class="text-end">Amount</th></tr></thead>
                            <tbody>
                                @forelse ($payments as $p)
                                    <tr>
                                        <td class="td-title"><a href="{{ route('payments.show', $p) }}" class="doc-link">{{ $p->number }}</a></td>
                                        <td data-label="Date">{{ $p->date->format('d M Y') }}</td>
                                        <td data-label="Method">{{ $p->methodSummary() }}</td>
                                        <td data-label="Amount" class="text-end money fw-semibold text-success">{{ money($p->total) }}</td>
                                    </tr>
                                @empty
                                    <x-empty :cols="4" text="No payments yet." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

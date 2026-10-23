@extends('layouts.vertical', ['subtitle' => $customer->name])

@section('content')
    @php
        $used = $customer->credit_limit > 0 ? min(100, $balance / $customer->credit_limit * 100) : 0;
        $wa = $customer->whatsappNumber();
    @endphp
    <x-page-header :title="$customer->name" :crumbs="['Customers' => route('customers.index'), $customer->code]">
        <a href="{{ route('invoices.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary">New Invoice</a>
        <a href="{{ route('receipts.create', ['customer_id' => $customer->id]) }}" class="btn btn-soft-success">Receive Payment</a>
        <a href="{{ route('reports.statement', ['customer_id' => $customer->id]) }}" class="btn btn-soft-secondary">Statement</a>
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-light">Edit</a>
    </x-page-header>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><x-kpi label="Outstanding" :value="money($balance)" icon="solar:wallet-money-outline" color="primary" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Overdue" :value="money($overdue)" icon="solar:alarm-outline" :color="$overdue > 0 ? 'danger' : 'success'" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Pending cheques" :value="money($pendingCheques)" icon="solar:card-recive-outline" color="info" sub="Not yet cleared" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Available credit" :value="money(max(0, $customer->credit_limit - $balance))" icon="solar:shield-check-outline" :color="$used >= 90 ? 'danger' : 'success'" :sub="'Limit ' . money($customer->credit_limit)" /></div>
    </div>

    @if ($customer->credit_limit > 0)
        <div class="card"><div class="card-body py-3">
            <div class="d-flex justify-content-between small mb-1"><span>Credit used</span><span class="fw-semibold">{{ number_format($used, 0) }}%</span></div>
            <div class="progress" style="height: 8px"><div class="progress-bar bg-{{ $used >= 90 ? 'danger' : ($used >= 70 ? 'warning' : 'success') }}" style="width: {{ $used }}%"></div></div>
        </div></div>
    @endif

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Invoices</h5></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Invoice</th><th>Date</th><th>Due</th><th>Status</th><th class="text-end">Total</th><th class="text-end">Balance</th></tr></thead>
                            <tbody>
                                @forelse ($invoices as $inv)
                                    <tr>
                                        <td class="td-title"><a href="{{ route('invoices.show', $inv) }}" class="doc-link">{{ $inv->number }}</a> {!! status_badge($inv->sale_type) !!}</td>
                                        <td data-label="Date">{{ $inv->date->format('d M Y') }}</td>
                                        <td data-label="Due" class="{{ $inv->daysOverdue() ? 'text-danger fw-semibold' : '' }}">{{ $inv->due_date->format('d M Y') }}@if ($inv->daysOverdue())<div class="small">{{ $inv->daysOverdue() }} days overdue</div>@endif</td>
                                        <td data-label="Status">{!! status_badge($inv->payment_status) !!}</td>
                                        <td data-label="Total" class="text-end money">{{ money($inv->total) }}</td>
                                        <td data-label="Balance" class="text-end money fw-semibold">{{ money($inv->balance()) }}</td>
                                    </tr>
                                @empty
                                    <x-empty :cols="6" text="No invoices yet." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Contact</h5>
                    @if ($wa)
                        <a target="_blank" rel="noopener" class="btn btn-sm btn-success" href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Dear ' . $customer->name . ', your outstanding balance with ' . setting('company_name') . ' is ' . money($balance) . ($overdue > 0 ? ' (overdue ' . money($overdue) . ')' : '') . '. Thank you.') }}">
                            <iconify-icon icon="ic:baseline-whatsapp" class="align-middle"></iconify-icon> Send reminder</a>
                    @endif
                </div>
                <div class="card-body info-list">
                    <div><div class="label">Group</div><div class="value">{{ $customer->group?->name ?? '-' }}</div></div>
                    <div><div class="label">Terms</div><div class="value">{{ $customer->payment_terms_days }} days</div></div>
                    <div><div class="label">Phone</div><div class="value">{{ $customer->phone ?: '-' }}</div></div>
                    <div><div class="label">City</div><div class="value">{{ $customer->city ?: '-' }}</div></div>
                    <div><div class="label">Address</div><div class="value">{{ $customer->address ?: '-' }}</div></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Cheques</h5></div>
                <ul class="list-group list-group-flush">
                    @forelse ($cheques as $ch)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>#{{ $ch->cheque_no }} <span class="small text-muted">{{ $ch->bank }}</span><br><span class="small text-muted">{{ $ch->cheque_date->format('d M Y') }}</span></span>
                            <span class="text-end"><strong class="money">{{ money($ch->amount) }}</strong><br>{!! status_badge($ch->status) !!}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">No cheques.</li>
                    @endforelse
                </ul>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Recent receipts</h5></div>
                <ul class="list-group list-group-flush">
                    @forelse ($payments as $p)
                        <li class="list-group-item d-flex justify-content-between">
                            <span><a href="{{ route('payments.show', $p) }}">{{ $p->number }}</a><br><span class="small text-muted">{{ $p->date->format('d M Y') }} &middot; {{ $p->methodSummary() }}</span></span>
                            <strong class="money">{{ money($p->total) }}</strong>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">No receipts yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection

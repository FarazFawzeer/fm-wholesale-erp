@extends('layouts.vertical', ['subtitle' => 'Credit Control'])

@section('content')
    <x-page-header title="Credit Control & Reminders" :crumbs="['Credit Control', 'Overdue']">
        <a href="{{ route('reports.outstanding') }}" class="btn btn-light">Ageing report</a>
    </x-page-header>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><x-kpi label="Overdue" :value="money($overdue->sum(fn ($i) => $i->balance()))" icon="solar:alarm-outline" color="danger" :sub="$overdue->count() . ' invoices'" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Due in next {{ setting('reminder_days_before_due') }} days" :value="money($dueSoon->sum(fn ($i) => $i->balance()))" icon="solar:calendar-outline" color="warning" :sub="$dueSoon->count() . ' invoices'" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Over credit limit" :value="$customers->where('used', '>', 100)->count()" icon="solar:shield-warning-outline" color="danger" sub="customers" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Near limit (80%+)" :value="$customers->whereBetween('used', [80, 100])->count()" icon="solar:shield-outline" color="warning" sub="customers" /></div>
    </div>

    @foreach (['Overdue invoices' => $overdue, 'Coming due - send a friendly reminder' => $dueSoon] as $title => $list)
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">{{ $title }}</h5></div>
            <div class="card-body table-wrap p-0">
                <div class="table-responsive">
                    <table class="table erp-table stack mb-0">
                        <thead><tr><th>Invoice</th><th>Customer</th><th>Due date</th><th class="text-end">Balance</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($list as $inv)
                                @php
                                    $c = $inv->customer; $wa = $c->whatsappNumber(); $days = $inv->daysOverdue();
                                    $msg = 'Dear ' . $c->name . ', this is a reminder from ' . setting('company_name') . ' that invoice ' . $inv->number . ' for ' . money($inv->balance())
                                        . ($days ? ' is overdue by ' . $days . ' days' : ' is due on ' . $inv->due_date->format('d M Y')) . '. Kindly arrange payment. Thank you.';
                                @endphp
                                <tr>
                                    <td class="td-title"><a href="{{ route('invoices.show', $inv) }}" class="doc-link">{{ $inv->number }}</a> <span class="small text-muted">{{ $inv->date->format('d M') }}</span></td>
                                    <td data-label="Customer"><a href="{{ route('customers.show', $c) }}">{{ $c->name }}</a><div class="small text-muted">{{ $c->phone }}</div></td>
                                    <td data-label="Due" class="{{ $days ? 'text-danger fw-semibold' : 'text-warning' }}">{{ $inv->due_date->format('d M Y') }}<div class="small">{{ $days ? $days . ' days overdue' : 'in ' . now()->startOfDay()->diffInDays($inv->due_date) . ' days' }}</div></td>
                                    <td data-label="Balance" class="text-end money fw-semibold">{{ money($inv->balance()) }}</td>
                                    <td class="td-actions text-end text-nowrap">
                                        @if ($wa)<a target="_blank" rel="noopener" href="https://wa.me/{{ $wa }}?text={{ rawurlencode($msg) }}" class="btn btn-sm btn-success" title="WhatsApp reminder"><iconify-icon icon="ic:baseline-whatsapp" class="align-middle"></iconify-icon> Remind</a>@endif
                                        @if ($c->phone)<a href="tel:{{ $c->phone }}" class="btn btn-sm btn-light" title="Call"><iconify-icon icon="solar:phone-outline" class="align-middle"></iconify-icon></a>@endif
                                        <a href="{{ route('receipts.create', ['customer_id' => $c->id, 'invoice_id' => $inv->id]) }}" class="btn btn-sm btn-soft-primary">Receive</a>
                                    </td>
                                </tr>
                            @empty
                                <x-empty :cols="5" text="Nothing here." />
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Credit limit usage</h5></div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Customer</th><th>Terms</th><th class="text-end">Limit</th><th class="text-end">Balance</th><th style="min-width: 160px">Used</th><th class="text-end">Overdue</th></tr></thead>
                    <tbody>
                        @foreach ($customers as $row)
                            @php $u = $row['used']; @endphp
                            <tr>
                                <td class="td-title"><a href="{{ route('customers.show', $row['customer']) }}" class="doc-link">{{ $row['customer']->name }}</a> <span class="small text-muted">{{ $row['customer']->group?->name }}</span></td>
                                <td data-label="Terms">{{ $row['customer']->payment_terms_days }} days</td>
                                <td data-label="Limit" class="text-end money">{{ money($row['customer']->credit_limit) }}</td>
                                <td data-label="Balance" class="text-end money">{{ money($row['balance']) }}</td>
                                <td data-label="Used"><div class="d-flex align-items-center gap-2 w-100"><div class="progress flex-grow-1" style="height: 6px"><div class="progress-bar bg-{{ $u > 100 ? 'danger' : ($u >= 80 ? 'warning' : 'success') }}" style="width: {{ min(100, $u) }}%"></div></div><span class="small fw-semibold">{{ number_format($u) }}%</span></div></td>
                                <td data-label="Overdue" class="text-end money {{ $row['overdue'] > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">{{ money($row['overdue']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

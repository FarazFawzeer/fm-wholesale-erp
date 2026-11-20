@extends('layouts.vertical', ['subtitle' => 'Sales'])

@section('content')
    <x-page-header title="Sales">
        <a href="{{ route('invoices.create') }}" class="btn btn-primary"><iconify-icon icon="solar:cart-large-2-outline" class="align-middle me-1"></iconify-icon>New sale</a>
    </x-page-header>

    <form class="filter-bar mb-3" method="GET">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control filter-grow" placeholder="Search invoice no or customer...">
        <input type="date" name="from" value="{{ request('from') }}" class="form-control" title="From">
        <input type="date" name="to" value="{{ request('to') }}" class="form-control" title="To">
        <select name="show" class="form-select">
            <option value="">All</option>
            <option value="unpaid" @selected(request('show') === 'unpaid')>Not fully paid</option>
        </select>
        <button class="btn btn-soft-primary">Show</button>
    </form>

    <div class="row g-3 mb-3">
        <div class="col-6 col-md-4"><x-kpi label="Sales (this list)" :value="money($sum->total ?? 0)" icon="solar:cart-large-2-outline" color="primary" /></div>
        <div class="col-6 col-md-4"><x-kpi label="Not paid yet" :value="money($sum->due ?? 0)" icon="solar:hand-money-outline" color="danger" :href="route('invoices.index', ['show' => 'unpaid'])" /></div>
    </div>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack table-hover mb-0 sales-table">
                    <thead><tr><th>Invoice</th><th>Date</th><th>Customer</th><th class="text-end">Pairs</th><th class="text-end">Total</th><th class="text-end">Balance</th><th>Status</th><th class="text-end">Open</th></tr></thead>
                    <tbody>
                        @forelse ($invoices as $inv)
                            <tr>
                                <td class="td-title">
                                    <a href="{{ route('invoices.show', $inv) }}" class="doc-link">{{ $inv->number }}</a>
                                    <span class="d-md-none float-end">{!! status_badge($inv->payment_status) !!}</span>
                                </td>
                                <td data-label="Date">{{ $inv->date->format('d M Y') }}</td>
                                <td data-label="Customer" class="fw-semibold">{{ $inv->customer->name }}</td>
                                <td data-label="Pairs" class="text-end">{{ (int) $inv->pairs }}</td>
                                <td data-label="Total" class="text-end money">{{ money($inv->total - $inv->returned_amount) }}</td>
                                <td data-label="Balance" class="text-end money {{ $inv->balance() > 0 ? 'text-danger fw-semibold' : '' }}">{{ money($inv->balance()) }}</td>
                                <td data-label="Status" class="d-none d-md-table-cell">{!! status_badge($inv->payment_status) !!}@if ($inv->balance() > 0) <small class="text-muted d-block">{{ $inv->daysUnpaid() }} days</small>@endif</td>
                                <td class="td-actions text-end">
                                    <div class="row-actions">
                                        @if ($inv->balance() > 0)
                                            <a href="{{ route('payments.receive', ['customer' => $inv->customer_id, 'invoice' => $inv->id]) }}" class="btn btn-sm btn-soft-success"><iconify-icon icon="solar:wallet-money-outline"></iconify-icon><span>Get paid</span></a>
                                        @endif
                                        <a href="{{ route('invoices.show', $inv) }}" class="btn btn-sm btn-light"><iconify-icon icon="solar:eye-outline"></iconify-icon><span>Details</span></a>
                                        <a href="{{ route('invoices.print', $inv) }}" class="btn btn-sm btn-dark"><iconify-icon icon="solar:document-text-outline"></iconify-icon><span>Invoice</span></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-empty :cols="8" text="No sales yet." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{ $invoices->links() }}
@endsection

@extends('layouts.vertical', ['subtitle' => 'Invoices'])

@section('content')
    <x-page-header title="Invoices" :crumbs="['Sales', 'Invoices']">
        <a href="{{ route('invoices.create') }}" class="btn btn-primary"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>New Invoice</a>
    </x-page-header>

    <ul class="nav nav-pills nav-tabs-scroll mb-3 gap-1">
        @foreach (['' => 'All', 'unpaid' => 'Unpaid', 'partial' => 'Part paid', 'paid' => 'Paid', 'overdue' => 'Overdue'] as $k => $l)
            <li class="nav-item"><a class="nav-link {{ request('status', '') === $k ? 'active' : '' }}" href="{{ route('invoices.index', array_filter(['status' => $k])) }}">{{ $l }}</a></li>
        @endforeach
    </ul>

    <div class="card">
        <div class="card-body border-bottom">
            <form class="filter-bar">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control filter-grow" placeholder="Invoice no or customer">
                <select name="delivery" class="form-select"><option value="">Any delivery</option>@foreach (['pending', 'partial', 'delivered'] as $d)<option value="{{ $d }}" @selected(request('delivery') == $d)>{{ ucfirst($d) }}</option>@endforeach</select>
                <input type="date" name="from" value="{{ request('from') }}" class="form-control" title="From">
                <input type="date" name="to" value="{{ request('to') }}" class="form-control" title="To">
                <button class="btn btn-light">Filter</button>
            </form>
        </div>
        <div class="card-body py-2 border-bottom small d-flex flex-wrap gap-3">
            <span>Total <strong class="money">{{ money($totals->total) }}</strong></span>
            <span>Outstanding <strong class="money text-danger">{{ money($totals->balance) }}</strong></span>
            <span>Gross profit <strong class="money text-success">{{ money($totals->profit) }}</strong></span>
        </div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack mb-0">
                    <thead><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Due</th><th>Payment</th><th>Delivery</th><th class="text-end">Total</th><th class="text-end">Balance</th></tr></thead>
                    <tbody>
                        @forelse ($invoices as $inv)
                            <tr>
                                <td class="td-title"><a href="{{ route('invoices.show', $inv) }}" class="doc-link">{{ $inv->number }}</a> {!! status_badge($inv->sale_type) !!}</td>
                                <td data-label="Customer">{{ $inv->customer->name }}</td>
                                <td data-label="Date">{{ $inv->date->format('d M Y') }}</td>
                                <td data-label="Due" class="{{ $inv->daysOverdue() ? 'text-danger fw-semibold' : '' }}">{{ $inv->due_date->format('d M Y') }}</td>
                                <td data-label="Payment">{!! status_badge($inv->daysOverdue() ? 'overdue' : $inv->payment_status) !!}</td>
                                <td data-label="Delivery">{!! status_badge($inv->delivery_status) !!}</td>
                                <td data-label="Total" class="text-end money">{{ money($inv->total) }}</td>
                                <td data-label="Balance" class="text-end money fw-semibold">{{ money($inv->balance()) }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="8" text="No invoices found." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($invoices->hasPages())<div class="card-footer">{{ $invoices->links() }}</div>@endif
    </div>
@endsection

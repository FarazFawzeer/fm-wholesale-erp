@extends('layouts.vertical', ['subtitle' => 'Payments'])

@section('content')
    <x-page-header :title="$direction === 'in' ? 'Money received' : 'Money paid to suppliers'">
        @if ($direction === 'in')
            <a href="{{ route('payments.receive') }}" class="btn btn-success"><iconify-icon icon="solar:wallet-money-outline" class="align-middle me-1"></iconify-icon>Get paid</a>
        @else
            <a href="{{ route('payments.pay') }}" class="btn btn-warning"><iconify-icon icon="solar:card-send-outline" class="align-middle me-1"></iconify-icon>Pay supplier</a>
        @endif
    </x-page-header>

    <ul class="nav nav-pills mb-3">
        <li class="nav-item"><a class="nav-link {{ $direction === 'in' ? 'active' : '' }}" href="{{ route('payments.index') }}">Received from customers</a></li>
        <li class="nav-item"><a class="nav-link {{ $direction === 'out' ? 'active' : '' }}" href="{{ route('payments.index', ['direction' => 'out']) }}">Paid to suppliers</a></li>
    </ul>

    <form class="filter-bar mb-3" method="GET">
        <input type="hidden" name="direction" value="{{ $direction }}">
        <input type="date" name="from" value="{{ request('from') }}" class="form-control" title="From">
        <input type="date" name="to" value="{{ request('to') }}" class="form-control" title="To">
        <button class="btn btn-soft-primary">Show</button>
        <div class="align-self-center fw-semibold text-end">Total: <span class="money">{{ money($total) }}</span></div>
    </form>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack table-hover mb-0">
                    <thead><tr><th>No</th><th>Date</th><th>{{ $direction === 'in' ? 'Customer' : 'Supplier' }}</th><th>Method</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                        @forelse ($payments as $p)
                            <tr>
                                <td class="td-title"><a href="{{ route('payments.show', $p) }}" class="doc-link">{{ $p->number }}</a></td>
                                <td data-label="Date">{{ $p->date->format('d M Y') }}</td>
                                <td data-label="{{ $direction === 'in' ? 'Customer' : 'Supplier' }}">{{ $p->party_name }}</td>
                                <td data-label="Method">{{ $p->methodSummary() }}</td>
                                <td data-label="Amount" class="text-end money fw-semibold">{{ money($p->total) }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="5" text="No payments in this period." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{ $payments->links() }}
@endsection

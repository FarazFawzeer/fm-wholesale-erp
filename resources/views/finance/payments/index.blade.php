@php $in = $direction === 'in'; $label = $in ? 'Customer Receipts' : 'Supplier Payments'; @endphp
@extends('layouts.vertical', ['subtitle' => $label])

@section('content')
    <x-page-header :title="$label" :crumbs="['Payments', $label]">
        <a href="{{ route($in ? 'receipts.create' : 'supplier-payments.create') }}" class="btn btn-primary"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>{{ $in ? 'Receive Payment' : 'Pay Supplier' }}</a>
    </x-page-header>

    <div class="card">
        <div class="card-body border-bottom">
            <form class="filter-bar">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control filter-grow" placeholder="Number">
                <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                <button class="btn btn-light">Filter</button>
            </form>
        </div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack mb-0">
                    <thead><tr><th>Number</th><th>Date</th><th>{{ $in ? 'Customer' : 'Supplier' }}</th><th>Method</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                        @forelse ($payments as $p)
                            <tr>
                                <td class="td-title"><a href="{{ route('payments.show', $p) }}" class="doc-link">{{ $p->number }}</a></td>
                                <td data-label="Date">{{ $p->date->format('d M Y') }}</td>
                                <td data-label="{{ $in ? 'Customer' : 'Supplier' }}">{{ $p->party_name }}</td>
                                <td data-label="Method">{{ $p->methodSummary() }}</td>
                                <td data-label="Amount" class="text-end money fw-semibold {{ $in ? 'text-success' : 'text-danger' }}">{{ money($p->total) }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="5" text="Nothing recorded yet." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($payments->hasPages())<div class="card-footer">{{ $payments->links() }}</div>@endif
    </div>
@endsection

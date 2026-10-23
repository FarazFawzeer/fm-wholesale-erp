@extends('layouts.vertical', ['subtitle' => 'Returns & Credit Notes'])

@section('content')
    <x-page-header title="Sales Returns & Credit Notes" :crumbs="['Sales', 'Credit Notes']" />

    <div class="alert alert-light small">To create a return, open the invoice and click <strong>Return / credit note</strong>.</div>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack mb-0">
                    <thead><tr><th>Credit note</th><th>Invoice</th><th>Customer</th><th>Date</th><th>Reason</th><th>Type</th><th class="text-end">Pairs</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                        @forelse ($notes as $n)
                            <tr>
                                <td class="td-title"><a href="{{ route('credit-notes.show', $n) }}" class="doc-link">{{ $n->number }}</a></td>
                                <td data-label="Invoice"><a href="{{ route('invoices.show', $n->invoice) }}">{{ $n->invoice->number }}</a></td>
                                <td data-label="Customer">{{ $n->customer->name }}</td>
                                <td data-label="Date">{{ $n->date->format('d M Y') }}</td>
                                <td data-label="Reason">{{ $n->reason }}</td>
                                <td data-label="Type">{{ $n->restock ? 'Goods returned' : 'Price adjustment' }}</td>
                                <td data-label="Pairs" class="text-end">{{ $n->pairs }}</td>
                                <td data-label="Amount" class="text-end money fw-semibold text-danger">{{ money($n->total) }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="8" text="No returns yet." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($notes->hasPages())<div class="card-footer">{{ $notes->links() }}</div>@endif
    </div>
@endsection

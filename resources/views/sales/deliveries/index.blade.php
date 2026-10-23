@extends('layouts.vertical', ['subtitle' => 'Delivery Notes'])

@section('content')
    <x-page-header title="Delivery Notes" :crumbs="['Sales', 'Delivery Notes']">
        <a href="{{ route('invoices.index', ['delivery' => 'pending']) }}" class="btn btn-soft-primary">Pending deliveries</a>
        <a href="{{ route('invoices.index', ['delivery' => 'partial']) }}" class="btn btn-soft-warning">Partly delivered</a>
    </x-page-header>

    <div class="card">
        <div class="card-body border-bottom"><form class="filter-bar"><input type="search" name="q" value="{{ request('q') }}" class="form-control filter-grow" placeholder="DN number or customer"><button class="btn btn-light">Search</button></form></div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack mb-0">
                    <thead><tr><th>Delivery note</th><th>Invoice</th><th>Customer</th><th>Date</th><th>Store</th><th>Vehicle</th><th class="text-end">Pairs</th></tr></thead>
                    <tbody>
                        @forelse ($notes as $n)
                            <tr>
                                <td class="td-title"><a href="{{ route('deliveries.show', $n) }}" class="doc-link">{{ $n->number }}</a></td>
                                <td data-label="Invoice"><a href="{{ route('invoices.show', $n->invoice) }}">{{ $n->invoice->number }}</a></td>
                                <td data-label="Customer">{{ $n->customer->name }}</td>
                                <td data-label="Date">{{ $n->date->format('d M Y') }}</td>
                                <td data-label="Store">{{ $n->warehouse->code }}</td>
                                <td data-label="Vehicle">{{ $n->vehicle_no ?: '-' }}</td>
                                <td data-label="Pairs" class="text-end fw-semibold">{{ qty($n->pairs) }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="7" text="No deliveries yet." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($notes->hasPages())<div class="card-footer">{{ $notes->links() }}</div>@endif
    </div>
@endsection

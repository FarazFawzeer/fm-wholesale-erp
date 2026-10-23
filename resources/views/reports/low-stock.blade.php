@extends('layouts.vertical', ['subtitle' => 'Low Stock'])

@section('content')
    <x-page-header title="Low Stock & Missing Sizes" :crumbs="['Reports' => route('reports.index'), 'Low stock']">
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">Create purchase order</a>
    </x-page-header>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Product</th><th class="text-end">On hand</th><th class="text-end">Re-order level</th><th>Sizes out of stock</th></tr></thead>
                    <tbody>
                        @forelse ($products as $p)
                            <tr>
                                <td class="td-title"><a href="{{ route('products.show', $p) }}" class="doc-link">{{ $p->code }}</a> <span class="small text-muted">{{ $p->brand?->name }} {{ $p->name }} {{ $p->colour }}</span></td>
                                <td data-label="On hand" class="text-end fw-semibold {{ $p->on_hand <= $p->reorder_level ? 'text-danger' : '' }}">{{ qty($p->on_hand) }}</td>
                                <td data-label="Re-order" class="text-end">{{ $p->reorder_level }}</td>
                                <td data-label="Missing sizes">@forelse ($p->empty_sizes as $s)<span class="badge bg-danger-subtle text-danger me-1">{{ $s }}</span>@empty<span class="text-muted">-</span>@endforelse</td>
                            </tr>
                        @empty
                            <x-empty :cols="4" text="All products are above their re-order level." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

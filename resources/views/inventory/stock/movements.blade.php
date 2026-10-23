@extends('layouts.vertical', ['subtitle' => 'Stock Movements'])

@section('content')
    <x-page-header title="Stock Movements" :crumbs="['Inventory', 'Movements']" />

    <div class="card">
        <div class="card-body border-bottom">
            <form class="filter-bar">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control filter-grow" placeholder="SKU or document no">
                <select name="type" class="form-select"><option value="">All types</option>@foreach ($types as $t)<option value="{{ $t }}" @selected(request('type') == $t)>{{ ucwords(str_replace('_', ' ', $t)) }}</option>@endforeach</select>
                <select name="warehouse_id" class="form-select"><option value="">All stores</option>@foreach ($warehouses as $w)<option value="{{ $w->id }}" @selected(request('warehouse_id') == $w->id)>{{ $w->code }}</option>@endforeach</select>
                <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                <button class="btn btn-light">Filter</button>
            </form>
        </div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Date / SKU</th><th>Product</th><th>Type</th><th>Store</th><th class="text-end">Pairs</th><th class="text-end">Unit cost</th><th>Reference</th><th>User</th></tr></thead>
                    <tbody>
                        @forelse ($movements as $m)
                            <tr>
                                <td class="td-title">{{ $m->date->format('d M Y') }} &middot; <strong>{{ $m->variant->sku }}</strong> <span class="d-md-none small text-muted">{{ $m->variant->product->name }}</span></td>
                                <td data-label="Product" class="d-none d-md-table-cell">{{ $m->variant->product->name }} {{ $m->variant->product->colour }}</td>
                                <td data-label="Type">{{ ucwords(str_replace('_', ' ', $m->type)) }}</td>
                                <td data-label="Store">{{ $m->warehouse->code }}</td>
                                <td data-label="Pairs" class="text-end fw-semibold {{ $m->qty < 0 ? 'text-danger' : 'text-success' }}">{{ $m->qty > 0 ? '+' : '' }}{{ $m->qty }}</td>
                                <td data-label="Cost" class="text-end money">{{ money($m->unit_cost) }}</td>
                                <td data-label="Ref">{{ $m->reference_no }}<div class="small text-muted">{{ $m->note }}</div></td>
                                <td data-label="User">{{ $m->user?->name }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="8" text="No movements." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($movements->hasPages())<div class="card-footer">{{ $movements->links() }}</div>@endif
    </div>
@endsection

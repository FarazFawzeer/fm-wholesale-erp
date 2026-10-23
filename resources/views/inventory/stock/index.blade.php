@extends('layouts.vertical', ['subtitle' => 'Stock on Hand'])

@section('content')
    <x-page-header title="Stock on Hand" :crumbs="['Inventory', 'Stock']">
        <a href="{{ route('stock-transfers.create') }}" class="btn btn-soft-primary">Transfer</a>
        <a href="{{ route('stock-adjustments.create') }}" class="btn btn-soft-secondary">Adjust / damaged</a>
    </x-page-header>

    <div class="row g-3 mb-3">
        <div class="col-6 col-md-4"><x-kpi label="Pairs in stock" :value="qty($totals['pairs'])" icon="solar:box-outline" /></div>
        <div class="col-12 col-md-4 order-last order-md-0"><x-kpi label="Stock value" :value="money($totals['value'])" icon="solar:wallet-money-outline" color="info" sub="At landed cost" /></div>
        <div class="col-6 col-md-4"><x-kpi label="Damaged" :value="qty($totals['damaged'])" icon="solar:danger-triangle-outline" color="danger" sub="Not sellable" /></div>
    </div>

    <div class="card">
        <div class="card-body border-bottom">
            <form class="filter-bar">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control filter-grow" placeholder="Search code, model or colour">
                <select name="warehouse_id" class="form-select" onchange="this.form.submit()"><option value="">All stores</option>@foreach ($warehouses as $w)<option value="{{ $w->id }}" @selected(request('warehouse_id') == $w->id)>{{ $w->name }}</option>@endforeach</select>
                <select name="brand_id" class="form-select" onchange="this.form.submit()"><option value="">All brands</option>@foreach ($brands as $b)<option value="{{ $b->id }}" @selected(request('brand_id') == $b->id)>{{ $b->name }}</option>@endforeach</select>
                <select name="level" class="form-select" onchange="this.form.submit()">
                    <option value="">All stock levels</option>
                    <option value="low" @selected(request('level') == 'low')>Low stock</option>
                    <option value="out" @selected(request('level') == 'out')>Sizes out of stock</option>
                </select>
                <button class="btn btn-light">Filter</button>
            </form>
        </div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Product</th><th>Stock by size</th><th class="text-end">Pairs</th><th class="text-end">Cartons</th><th class="text-end">Value</th></tr></thead>
                    <tbody>
                        @forelse ($products as $p)
                            <tr>
                                <td class="td-title"><a href="{{ route('products.show', $p) }}" class="doc-link">{{ $p->code }}</a> <span class="small text-muted">{{ $p->brand?->name }} {{ $p->name }} {{ $p->colour }}</span>
                                    @if ($p->on_hand <= $p->reorder_level)<span class="badge bg-danger-subtle text-danger">Low</span>@endif</td>
                                <td data-label="Sizes">
                                    <div class="d-flex flex-wrap gap-1 justify-content-end justify-content-md-start">
                                        @foreach ($p->size_stock as $s)
                                            <span class="badge {{ $s['qty'] <= 0 ? 'bg-danger-subtle text-danger' : ($s['qty'] < 6 ? 'bg-warning-subtle text-warning' : 'bg-light text-dark') }} fw-normal">{{ $s['size'] }}: <strong>{{ $s['qty'] }}</strong>@if ($s['damaged'])<span class="text-danger"> +{{ $s['damaged'] }}d</span>@endif</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td data-label="Pairs" class="text-end fw-semibold">{{ qty($p->on_hand) }}</td>
                                <td data-label="Cartons" class="text-end">{{ number_format($p->on_hand / max(1, $p->pairs_per_carton), 1) }}</td>
                                <td data-label="Value" class="text-end money">{{ money($p->on_hand * $p->cost_price) }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="5" text="No stock matches the filter." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

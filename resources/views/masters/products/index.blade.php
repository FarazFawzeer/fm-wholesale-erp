@extends('layouts.vertical', ['subtitle' => 'Products'])

@section('content')
    <x-page-header title="Products" :crumbs="['Inventory', 'Products']">
        <a href="{{ route('products.import') }}" class="btn btn-soft-secondary"><iconify-icon icon="solar:upload-outline" class="align-middle me-1"></iconify-icon>Import</a>
        <a href="{{ route('products.create') }}" class="btn btn-primary"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>New Product</a>
    </x-page-header>

    <div class="card">
        <div class="card-body border-bottom">
            <form class="filter-bar" method="GET">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control filter-grow" placeholder="Search code, model or colour">
                <select name="brand_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All brands</option>
                    @foreach ($brands as $b)
                        <option value="{{ $b->id }}" @selected(request('brand_id') == $b->id)>{{ $b->name }}</option>
                    @endforeach
                </select>
                <select name="category_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All categories</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-light">Filter</button>
            </form>
        </div>
        <div class="card-body table-wrap p-0 p-md-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Sizes</th>
                            <th class="text-end">Stock (pairs)</th>
                            <th class="text-end">Landed cost</th>
                            <th class="text-end">Wholesale price</th>
                            <th class="text-end">Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $p)
                            @php $low = (int) $p->on_hand <= $p->reorder_level; @endphp
                            <tr>
                                <td class="td-title">
                                    <a href="{{ route('products.show', $p) }}" class="doc-link">{{ $p->code }}</a>
                                    <div class="small text-muted">{{ $p->brand?->name }} {{ $p->name }} &middot; {{ $p->colour }}</div>
                                </td>
                                <td data-label="Category">{{ $p->category?->name }}</td>
                                <td data-label="Sizes">{{ str_replace(',', ', ', $p->sizes) }}</td>
                                <td data-label="Stock" class="text-end">
                                    <span class="fw-semibold {{ $low ? 'text-danger' : '' }}">{{ qty($p->on_hand) }}</span>
                                    <div class="small text-muted">{{ number_format($p->on_hand / max(1, $p->pairs_per_carton), 1) }} ctn</div>
                                </td>
                                <td data-label="Landed cost" class="text-end money">{{ money($p->cost_price) }}</td>
                                <td data-label="Wholesale" class="text-end money fw-semibold">{{ money($p->selling_price) }}</td>
                                <td data-label="Margin" class="text-end">
                                    @php $m = $p->marginPercent(); @endphp
                                    <span class="badge {{ $m < setting('min_margin_percent') ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">{{ number_format($m, 1) }}%</span>
                                </td>
                            </tr>
                        @empty
                            <x-empty :cols="7" text="No products found." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($products->hasPages())
            <div class="card-footer">{{ $products->links() }}</div>
        @endif
    </div>
@endsection

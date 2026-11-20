@extends('layouts.vertical', ['subtitle' => 'Products'])

@php
    $view = request('view') === 'grid' ? 'grid' : 'list';
    $all = $products;
    $pairs = $all->sum(fn ($p) => $p->totalStock());
    $lowCount = $all->filter(fn ($p) => $p->isLow() && $p->totalStock() > 0)->count();
    $outCount = $all->filter(fn ($p) => $p->totalStock() <= 0)->count();
    $status = fn ($p) => $p->totalStock() <= 0 ? ['out', 'Out of stock'] : ($p->isLow() ? ['low', 'Low stock'] : ['ok', 'In stock']);
@endphp

@section('content')
    <x-page-header title="Products">
        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#product-lookup"><iconify-icon icon="solar:tag-price-outline" class="align-middle me-1"></iconify-icon>Check price</button>
        <a href="{{ route('products.create') }}" class="btn btn-primary"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>Add product</a>
    </x-page-header>

    <div class="stock-summary mb-3">
        <div><span>Products</span><b>{{ $all->count() }}</b></div>
        <div><span>Pairs in stock</span><b>{{ qty($pairs) }}</b></div>
        <a href="{{ request()->fullUrlWithQuery(['show' => 'low']) }}" class="{{ $lowCount ? 'warn' : '' }}"><span>Running low</span><b>{{ $lowCount }}</b></a>
        <a href="{{ request()->fullUrlWithQuery(['show' => 'low']) }}" class="{{ $outCount ? 'bad' : '' }}"><span>Out of stock</span><b>{{ $outCount }}</b></a>
    </div>

    <form class="filter-bar mb-3" method="GET">
        <input type="hidden" name="view" value="{{ $view }}">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control filter-grow" placeholder="Search item no, name or colour...">
        <select name="brand" class="form-select" onchange="this.form.submit()">
            <option value="">All brands</option>
            @foreach ($brands as $b)<option value="{{ $b->id }}" @selected(request('brand') == $b->id)>{{ $b->name }}</option>@endforeach
        </select>
        <select name="category" class="form-select" onchange="this.form.submit()">
            <option value="">All types</option>
            @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>@endforeach
        </select>
        <select name="show" class="form-select" onchange="this.form.submit()">
            <option value="">In use</option>
            <option value="low" @selected(request('show') === 'low')>Low / out of stock</option>
            <option value="hidden" @selected(request('show') === 'hidden')>Hidden products</option>
        </select>
        <div class="view-toggle" role="group" aria-label="View">
            <a href="{{ request()->fullUrlWithQuery(['view' => null]) }}" class="{{ $view === 'list' ? 'active' : '' }}" title="List"><iconify-icon icon="solar:list-outline"></iconify-icon></a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" class="{{ $view === 'grid' ? 'active' : '' }}" title="Photo grid"><iconify-icon icon="solar:widget-4-outline"></iconify-icon></a>
        </div>
    </form>

    @if ($products->isEmpty())
        <div class="card"><div class="card-body text-center text-muted py-5">
            <iconify-icon icon="ph:sneaker" class="fs-48 d-block mx-auto mb-2"></iconify-icon>
            No products found. <a href="{{ route('products.create') }}">Add a product</a>.
        </div></div>
    @elseif ($view === 'grid')
        <div class="product-grid">
            @foreach ($products as $p)
                @php [$st, $stLabel] = $status($p); @endphp
                <a href="{{ route('products.show', $p) }}" class="card product-card mb-0">
                    <div class="product-img">
                        @if ($p->image_path)<img src="{{ asset($p->image_path) }}" alt="{{ $p->name }}" loading="lazy">@else<iconify-icon icon="ph:sneaker"></iconify-icon>@endif
                        <span class="stock-pill st-{{ $st }}">{{ $stLabel }}</span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <span class="item-no">{{ $p->code }}</span>
                            @if ($p->sizeLabel())<span class="size-pill">{{ $p->sizeLabel() }}</span>@endif
                        </div>
                        <div class="product-name mt-1">{{ $p->name }}</div>
                        <div class="colour-list my-2">
                            @foreach ($p->colours as $c)<x-colour-chip :name="$c->title" :qty="$c->qty" />@endforeach
                        </div>
                        <div class="d-flex justify-content-between align-items-end">
                            <span class="product-price">{{ money($p->selling_price) }}</span>
                            <span class="small fw-semibold st-text-{{ $st }}">{{ qty($p->totalStock()) }} pairs</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        {{-- Computer: table --}}
        <div class="card d-none d-md-block">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table erp-table table-hover mb-0 product-table">
                        <thead><tr><th>Product</th><th>Size</th><th>Stock by colour</th><th class="text-end">Price / pair</th><th class="text-end">In stock</th><th></th></tr></thead>
                        <tbody>
                            @foreach ($products as $p)
                                @php [$st, $stLabel] = $status($p); @endphp
                                <tr>
                                    <td class="td-title">
                                        <a href="{{ route('products.show', $p) }}" class="d-flex align-items-center gap-3 text-body product-link">
                                            <x-thumb :product="$p" :size="48" />
                                            <span class="min-w-0"><span class="item-no">{{ $p->code }}</span><span class="d-block fw-semibold text-truncate">{{ $p->name }}</span>
                                                <span class="small text-muted">{{ collect([$p->brand?->name, $p->category?->name])->filter()->implode(' · ') }}</span></span>
                                        </a>
                                    </td>
                                    <td class="text-nowrap">{{ $p->sizeLabel() ?: '-' }}</td>
                                    <td><div class="colour-list">@foreach ($p->colours as $c)<x-colour-chip :name="$c->title" :qty="$c->qty" />@endforeach</div></td>
                                    <td class="text-end text-nowrap fw-semibold">{{ money($p->selling_price) }}</td>
                                    <td class="text-end"><span class="stock-pill inline st-{{ $st }}">{{ qty($p->totalStock()) }} pairs</span></td>
                                    <td class="td-actions text-end"><a href="{{ route('invoices.create', ['product' => $p->id]) }}" class="btn btn-sm btn-light {{ $p->totalStock() <= 0 ? 'disabled' : '' }}">Sell</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Phone: clean list --}}
        <div class="plist d-md-none">
            @foreach ($products as $p)
                @php [$st, $stLabel] = $status($p); @endphp
                <a href="{{ route('products.show', $p) }}" class="plist-row">
                    <x-thumb :product="$p" :size="60" />
                    <div class="plist-main">
                        <div class="plist-top">
                            <span class="item-no">{{ $p->code }}</span>
                            <span class="stock-pill inline st-{{ $st }}">{{ qty($p->totalStock()) }} prs</span>
                        </div>
                        <div class="plist-name">{{ $p->name }}</div>
                        <div class="plist-meta">
                            <span>{{ collect([$p->sizeLabel() ? 'Size ' . $p->sizeLabel() : null, $p->brand?->name])->filter()->implode(' · ') }}</span>
                            <b>{{ money($p->selling_price) }}</b>
                        </div>
                        @if ($p->colours->count())
                            <div class="plist-colours">
                                @foreach ($p->colours as $c)<span class="{{ $c->qty <= 0 ? 'zero' : '' }}"><i style="background: {{ \App\Models\ProductColour::hex($c->colour) }}"></i>{{ $c->title }} <b>{{ $c->qty }}</b></span>@endforeach
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection

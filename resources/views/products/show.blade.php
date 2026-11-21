@extends('layouts.vertical', ['subtitle' => $product->name])

@php
    $stock = $product->totalStock();
    [$st, $stLabel] = $stock <= 0 ? ['out', 'Out of stock'] : ($product->isLow() ? ['low', 'Low stock'] : ['ok', 'In stock']);
    $maxColour = max(1, (int) $product->colours->max('qty'));
    $isManager = auth()->user()->isManager();
    $profit = $product->selling_price - $product->cost_price;
@endphp

@section('content')
    <x-page-header title="Product details" :crumbs="['Products' => route('products.index'), $product->code]">
            <a href="https://wa.me/" class="btn btn-success {{ $stock <= 0 ? 'disabled' : '' }}" data-share-product data-share-text="{{ $product->shareText() }}" data-image="{{ $product->imageUrl() }}" data-file-name="{{ \Illuminate\Support\Str::slug($product->code) }}.jpg" title="Send photo + details to a customer (no price)"><i class="bx bxl-whatsapp me-1"></i>Share</a>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-light"><iconify-icon icon="solar:pen-outline" class="align-middle me-1"></iconify-icon>Edit</a>
            <a href="{{ route('purchases.create', ['product' => $product->id]) }}" class="btn btn-light"><iconify-icon icon="solar:box-outline" class="align-middle me-1"></iconify-icon>Buy more</a>
            <a href="{{ route('invoices.create', ['product' => $product->id]) }}" class="btn btn-brand {{ $stock <= 0 ? 'disabled' : '' }}"><iconify-icon icon="solar:cart-large-2-outline" class="align-middle me-1"></iconify-icon>Sell</a>
    </x-page-header>

    <div class="row g-3">
        {{-- Photo --}}
        <div class="col-lg-5 col-xl-4">
            <div class="card product-hero-img">
                <div class="product-img">
                    @if ($product->image_path)<img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}">@else<iconify-icon icon="ph:sneaker"></iconify-icon>@endif
                    <span class="stock-pill st-{{ $st }}">{{ $stLabel }}</span>
                </div>
            </div>
        </div>

        {{-- Main details --}}
        <div class="col-lg-7 col-xl-8">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between gap-3">
                        <div class="min-w-0">
                            <span class="item-no lg">{{ $product->code }}</span>
                            <h2 class="product-title">{{ $product->name }}</h2>
                            <div class="text-muted">{{ collect([$product->brand?->name, $product->category?->name])->filter()->implode(' · ') ?: 'No brand / type' }}</div>
                        </div>
                        <div class="text-md-end">
                            <div class="small text-muted fw-semibold">Selling price</div>
                            <div class="hero-price">{{ money($product->selling_price) }}</div>
                            <div class="small text-muted">per pair</div>
                        </div>
                    </div>

                    <div class="fact-grid mt-4">
                        <div><span>Sizes</span><b>{{ $product->sizeLabel() ?: '-' }}</b></div>
                        <div><span>In stock</span><b class="st-text-{{ $st }}">{{ qty($stock) }} pairs</b></div>
                        <div><span>Colours</span><b>{{ $product->colours->count() }}</b></div>
                        <div><span>Sold (30 days)</span><b>{{ qty($sold30) }} pairs</b></div>
                        @if ($isManager)
                            <div><span>Cost (average)</span><b>{{ money($product->cost_price) }}</b></div>
                            <div><span>Profit / pair</span><b class="{{ $profit < 0 ? 'text-danger' : 'text-success' }}">{{ money($profit) }}@if ($product->marginPercent() !== null) <small class="text-muted fw-normal">{{ $product->marginPercent() }}%</small>@endif</b></div>
                            <div><span>Stock value</span><b>{{ money(max(0, $stock) * $product->cost_price) }}</b></div>
                        @endif
                        <div><span>Warn below</span><b>{{ $product->low_stock_level }} pairs</b></div>
                    </div>

                    <h6 class="section-label mt-4">Stock by colour</h6>
                    <div class="colour-stock">
                        @forelse ($product->colours as $c)
                            <div class="cs-row {{ $c->qty <= 0 ? 'empty' : '' }}">
                                <span class="cs-name"><span class="dot" style="background: {{ \App\Models\ProductColour::hex($c->colour) }}"></span>{{ $c->title }}</span>
                                <span class="cs-bar"><span style="width: {{ round(max(0, $c->qty) / $maxColour * 100) }}%"></span></span>
                                <b class="cs-qty">{{ qty($c->qty) }}</b>
                                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#correct-stock"
                                    data-colour-id="{{ $c->id }}" data-colour-name="{{ $c->title }}" data-colour-qty="{{ $c->qty }}" title="Correct stock">Correct</button>
                            </div>
                        @empty
                            <div class="text-muted small">No colours yet. <a href="{{ route('products.edit', $product) }}">Add colours</a> or record a purchase.</div>
                        @endforelse
                    </div>

                    <div class="last-info mt-4">
                        <div><iconify-icon icon="solar:cart-large-2-outline"></iconify-icon>
                            <span>Last sold: @if ($lastSale)<b>{{ $lastSale['invoice']->date->format('d M Y') }}</b> to <a href="{{ route('customers.show', $lastSale['invoice']->customer) }}">{{ $lastSale['invoice']->customer->name }}</a> ({{ $lastSale['pairs'] }} prs at {{ money($lastSale['price']) }})@else <b>not sold yet</b>@endif</span></div>
                        <div><iconify-icon icon="solar:box-outline"></iconify-icon>
                            <span>Last bought: @if ($lastPurchase)<b>{{ $lastPurchase['purchase']->date->format('d M Y') }}</b> from <a href="{{ route('suppliers.show', $lastPurchase['purchase']->supplier) }}">{{ $lastPurchase['purchase']->supplier->name }}</a>@if ($isManager) at {{ money($lastPurchase['price']) }} / pair @endif @else <b>not bought yet</b>@endif</span></div>
                    </div>

                    @if ($product->description)<p class="mt-3 mb-0">{{ $product->description }}</p>@endif
                    @if (! $product->is_active)<div class="alert alert-secondary small mt-3 mb-0">This product is hidden. Edit it and switch on "In use" to show it again.</div>@endif
                </div>
            </div>
        </div>

        {{-- History --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-pills nav-scroll" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-sales" type="button">Sales <span class="badge bg-light text-dark ms-1">{{ $sales->count() }}</span></button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-buys" type="button">Purchases <span class="badge bg-light text-dark ms-1">{{ $purchases->count() }}</span></button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-stock" type="button">Stock history</button></li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-sales">
                        <div class="card-body table-wrap p-0"><div class="table-responsive">
                            <table class="table erp-table stack mb-0">
                                <thead><tr><th>Date</th><th>Invoice</th><th>Customer</th><th>Colours</th><th class="text-end">Pairs</th><th class="text-end">Price</th></tr></thead>
                                <tbody>
                                    @forelse ($sales->take(30) as $r)
                                        <tr>
                                            <td class="td-title">{{ $r['invoice']->date->format('d M Y') }}</td>
                                            <td data-label="Invoice"><a href="{{ route('invoices.show', $r['invoice']) }}" class="doc-link">{{ $r['invoice']->number }}</a></td>
                                            <td data-label="Customer">{{ $r['invoice']->customer->name }}</td>
                                            <td data-label="Colours"><div class="colour-list justify-content-end justify-content-md-start">@foreach ($r['colours'] as $c)<x-colour-chip :name="$c['name']" :qty="$c['qty']" />@endforeach</div></td>
                                            <td data-label="Pairs" class="text-end fw-bold">{{ $r['pairs'] }}</td>
                                            <td data-label="Price" class="text-end money">{{ money($r['price']) }}</td>
                                        </tr>
                                    @empty
                                        <x-empty :cols="6" text="Not sold yet." />
                                    @endforelse
                                </tbody>
                            </table>
                        </div></div>
                    </div>
                    <div class="tab-pane fade" id="tab-buys">
                        <div class="card-body table-wrap p-0"><div class="table-responsive">
                            <table class="table erp-table stack mb-0">
                                <thead><tr><th>Date</th><th>Purchase</th><th>Supplier</th><th>Colours</th><th class="text-end">Pairs</th>@if ($isManager)<th class="text-end">Price</th>@endif</tr></thead>
                                <tbody>
                                    @forelse ($purchases->take(30) as $r)
                                        <tr>
                                            <td class="td-title">{{ $r['purchase']->date->format('d M Y') }}</td>
                                            <td data-label="Purchase"><a href="{{ route('purchases.show', $r['purchase']) }}" class="doc-link">{{ $r['purchase']->number }}</a></td>
                                            <td data-label="Supplier">{{ $r['purchase']->supplier->name }}</td>
                                            <td data-label="Colours"><div class="colour-list justify-content-end justify-content-md-start">@foreach ($r['colours'] as $c)<x-colour-chip :name="$c['name']" :qty="$c['qty']" />@endforeach</div></td>
                                            <td data-label="Pairs" class="text-end fw-bold">{{ $r['pairs'] }}</td>
                                            @if ($isManager)<td data-label="Price" class="text-end money">{{ money($r['price']) }}</td>@endif
                                        </tr>
                                    @empty
                                        <x-empty :cols="6" text="Not bought yet." />
                                    @endforelse
                                </tbody>
                            </table>
                        </div></div>
                    </div>
                    <div class="tab-pane fade" id="tab-stock">
                        <div class="card-body table-wrap p-0"><div class="table-responsive">
                            <table class="table erp-table stack mb-0">
                                <thead><tr><th>Date</th><th>Colour</th><th>What happened</th><th class="text-end">Pairs</th><th class="text-end">Stock after</th></tr></thead>
                                <tbody>
                                    @forelse ($movements as $m)
                                        <tr>
                                            <td class="td-title">{{ $m->date->format('d M Y') }}</td>
                                            <td data-label="Colour"><x-colour-chip :name="$m->colour->title" /></td>
                                            <td data-label="What">
                                                {{ $m->typeLabel() }}
                                                @if ($m->reference_no)
                                                    @php $url = match ($m->reference_type) { 'App\Models\Invoice' => route('invoices.show', $m->reference_id), 'App\Models\Purchase' => route('purchases.show', $m->reference_id), 'App\Models\GoodsReturn' => route('returns.show', $m->reference_id), default => null }; @endphp
                                                    &middot; @if ($url)<a href="{{ $url }}">{{ $m->reference_no }}</a>@else{{ $m->reference_no }}@endif
                                                @endif
                                                @if (in_array($m->type, ['correction', 'missing']) && $m->note)<span class="text-muted small">&middot; {{ $m->note }}</span>@endif
                                            </td>
                                            <td data-label="Pairs" class="text-end fw-bold {{ $m->qty < 0 ? 'text-danger' : 'text-success' }}">{{ $m->qty > 0 ? '+' : '' }}{{ $m->qty }}</td>
                                            <td data-label="Stock after" class="text-end">{{ $m->balance }}</td>
                                        </tr>
                                    @empty
                                        <x-empty :cols="5" text="No stock changes yet." />
                                    @endforelse
                                </tbody>
                            </table>
                        </div></div>
                    </div>
                </div>
            </div>

            @if ($soldPairs === 0 && $boughtPairs === 0)
                <form method="POST" action="{{ route('products.destroy', $product) }}" class="text-end" data-confirm="Delete this product? This cannot be undone.">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-link text-danger">Delete product</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Correct stock pop-up --}}
    <div class="modal fade" id="correct-stock" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('products.correct', $product) }}">
                @csrf
                <input type="hidden" name="product_colour_id" class="js-cs-id">
                <div class="modal-header"><h5 class="modal-title">Correct stock - <span class="js-cs-name"></span></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <p class="text-muted small">The system has <b class="js-cs-qty"></b> pairs. Enter what you actually have now.</p>
                    <label class="form-label">Pairs you have now</label>
                    <input type="number" min="0" name="counted" class="form-control form-control-lg mb-3 js-cs-count" required inputmode="numeric">
                    <label class="form-label">Reason</label>
                    <select name="reason" class="form-select" required>
                        <option>Damaged pairs</option><option>Counted the stock</option><option>Lost / missing</option><option>Found extra pairs</option><option>Given as free / sample</option><option>Other</option>
                    </select>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
            </form>
        </div>
    </div>
@endsection

@push('page-js')
    @include('partials.share-product')
@endpush

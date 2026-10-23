@extends('layouts.vertical', ['subtitle' => $product->code])

@section('content')
    <x-page-header :title="$product->code . ' - ' . $product->name" :crumbs="['Products' => route('products.index'), $product->code]">
        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary"><iconify-icon icon="solar:pen-outline" class="align-middle me-1"></iconify-icon>Edit</a>
    </x-page-header>

    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3"><x-kpi label="Stock on hand" :value="qty($product->totalStock()) . ' pairs'" icon="solar:box-outline" :sub="number_format($product->totalStock() / max(1, $product->pairs_per_carton), 1) . ' cartons'" /></div>
        <div class="col-6 col-xl-3"><x-kpi label="Landed cost" :value="money($product->cost_price)" icon="solar:globus-outline" color="info" sub="Weighted average per pair" /></div>
        <div class="col-6 col-xl-3"><x-kpi label="Wholesale price" :value="money($product->selling_price)" icon="solar:tag-price-outline" color="success" :sub="'Margin ' . number_format($product->marginPercent(), 1) . '%'" /></div>
        <div class="col-6 col-xl-3"><x-kpi label="Minimum price" :value="money($product->minimumPrice())" icon="solar:shield-warning-outline" color="warning" :sub="'Suggested ' . money($product->suggestedPrice())" /></div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Stock by size</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table erp-table text-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-start">Store</th>
                                    @foreach ($product->variants as $v)
                                        <th>{{ $v->size }}</th>
                                    @endforeach
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($warehouses as $w)
                                    @php $row = $product->variants->map(fn ($v) => (int) $v->stocks->firstWhere('warehouse_id', $w->id)?->qty); @endphp
                                    <tr>
                                        <td class="text-start fw-semibold">{{ $w->name }}</td>
                                        @foreach ($row as $q)
                                            <td class="{{ $q <= 0 ? 'text-danger' : '' }}">{{ $q }}</td>
                                        @endforeach
                                        <td class="fw-bold">{{ $row->sum() }}</td>
                                    </tr>
                                @endforeach
                                @php $dmg = $product->variants->map(fn ($v) => (int) $v->stocks->sum('damaged_qty')); @endphp
                                @if ($dmg->sum())
                                    <tr class="table-light">
                                        <td class="text-start text-danger">Damaged (not sellable)</td>
                                        @foreach ($dmg as $q)
                                            <td class="text-danger">{{ $q ?: '' }}</td>
                                        @endforeach
                                        <td class="fw-bold text-danger">{{ $dmg->sum() }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer small text-muted">SKUs: {{ $product->variants->pluck('sku')->implode(', ') }}</div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Recent stock movements</h5></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Date</th><th>Type</th><th>Size</th><th>Store</th><th class="text-end">Qty</th><th>Reference</th></tr></thead>
                            <tbody>
                                @forelse ($movements as $m)
                                    <tr>
                                        <td data-label="Date">{{ $m->date->format('d M Y') }}</td>
                                        <td data-label="Type">{{ ucwords(str_replace('_', ' ', $m->type)) }}</td>
                                        <td data-label="Size">{{ $m->variant->size }}</td>
                                        <td data-label="Store">{{ $m->warehouse->code }}</td>
                                        <td data-label="Qty" class="text-end fw-semibold {{ $m->qty < 0 ? 'text-danger' : 'text-success' }}">{{ $m->qty > 0 ? '+' : '' }}{{ $m->qty }}</td>
                                        <td data-label="Ref">{{ $m->reference_no }}</td>
                                    </tr>
                                @empty
                                    <x-empty :cols="6" text="No movements yet." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Details</h5></div>
                <div class="card-body info-list">
                    <div><div class="label">Brand</div><div class="value">{{ $product->brand?->name ?? '-' }}</div></div>
                    <div><div class="label">Category</div><div class="value">{{ $product->category?->name ?? '-' }}</div></div>
                    <div><div class="label">Colour</div><div class="value">{{ $product->colour ?? '-' }}</div></div>
                    <div><div class="label">Gender</div><div class="value">{{ $product->gender ?? '-' }}</div></div>
                    <div><div class="label">Pairs / carton</div><div class="value">{{ $product->pairs_per_carton }}</div></div>
                    <div><div class="label">Re-order level</div><div class="value">{{ $product->reorder_level }} pairs</div></div>
                    <div><div class="label">Status</div><div class="value">{!! status_badge($product->is_active ? 'active' : 'inactive') !!}</div></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Price by customer group</h5></div>
                <ul class="list-group list-group-flush">
                    @foreach ($groups as $g)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $g->name }}
                                @if ($product->priceListItems->firstWhere('customer_group_id', $g->id))
                                    <span class="badge bg-info-subtle text-info">price list</span>
                                @endif
                            </span>
                            <strong class="money">{{ money($product->priceForGroup($g)) }}</strong>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Import history</h5></div>
                <ul class="list-group list-group-flush">
                    @forelse ($purchases as $row)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('purchase-orders.show', $row->purchase_order_id) }}" class="fw-semibold">{{ $row->purchaseOrder->number }}</a>
                                <span class="small text-muted">{{ $row->purchaseOrder->order_date->format('d M Y') }}</span>
                            </div>
                            <div class="small text-muted">{{ $row->received }} / {{ $row->qty }} pairs &middot; {{ $row->purchaseOrder->currency }} {{ number_format($row->fx, 2) }} &rarr; landed {{ money($row->landed) }}</div>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">Not imported yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection

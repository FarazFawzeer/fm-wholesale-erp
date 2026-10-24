@extends('layouts.vertical', ['subtitle' => $product->exists ? 'Edit Product' : 'New Product'])

@section('content')
    <x-page-header :title="$product->exists ? 'Edit ' . $product->name : 'New Product'" :crumbs="['Products' => route('products.index'), $product->exists ? 'Edit' : 'New']" />

    <form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}">
        @csrf
        @if ($product->exists) @method('PUT') @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Product details</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">Product code</label>
                                <div class="input-group">
                                    <input name="code" id="code" class="form-control" value="{{ old('code', $product->code) }}" placeholder="Leave empty for automatic">
                                    <button type="button" class="btn btn-soft-primary" id="gen-code" title="Make a new code"><iconify-icon icon="solar:refresh-outline" class="align-middle"></iconify-icon> Auto</button>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label">Product name *</label>
                                <input name="name" class="form-control" value="{{ old('name', $product->name) }}" required placeholder="e.g. Canvas Loafer">
                            </div>

                            <div class="col-md-6">
                                @include('masters.products.partials.pick-or-add', ['field' => 'brand', 'label' => 'Brand', 'items' => $brands, 'value' => old('brand_id', $product->brand_id)])
                            </div>
                            <div class="col-md-6">
                                @include('masters.products.partials.pick-or-add', ['field' => 'category', 'label' => 'Category', 'items' => $categories, 'value' => old('category_id', $product->category_id)])
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Colour <span class="text-muted small">(optional)</span></label>
                                <input name="colour" class="form-control" list="colour-list" value="{{ old('colour', $product->colour) }}" placeholder="Pick or type">
                                <datalist id="colour-list">@foreach ($colours as $c)<option value="{{ $c }}">@endforeach</datalist>
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label">Size from <span class="text-muted small">(optional)</span></label>
                                <input name="size_from" class="form-control" list="size-list" inputmode="numeric" value="{{ old('size_from', $product->size_from) }}" placeholder="e.g. 28">
                            </div>
                            <div class="col-6 col-md-4">
                                <label class="form-label">Size to <span class="text-muted small">(optional)</span></label>
                                <input name="size_to" class="form-control" list="size-list" inputmode="numeric" value="{{ old('size_to', $product->size_to) }}" placeholder="e.g. 44">
                            </div>
                            <datalist id="size-list">@foreach (range(20, 48) as $s)<option value="{{ $s }}">@endforeach</datalist>

                            <div class="col-12">
                                <label class="form-label">Notes <span class="text-muted small">(optional)</span></label>
                                <textarea name="description" class="form-control" rows="2">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Price & stock</h5></div>
                    <div class="card-body">
                        @if ($product->exists && $product->cost_price > 0)
                            <div class="alert alert-light small mb-3">
                                Real cost (with shipping & duty): <strong>{{ money($product->cost_price) }}</strong> per pair<br>
                                Suggested selling price: <strong>{{ money($product->suggestedPrice()) }}</strong>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Selling price per pair (Rs) *</label>
                            <input type="number" step="0.01" name="selling_price" class="form-control" value="{{ old('selling_price', $product->selling_price) }}" required inputmode="decimal">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lowest price allowed (Rs)</label>
                            <input type="number" step="0.01" name="min_price" class="form-control" value="{{ old('min_price', $product->min_price) }}" placeholder="Automatic" inputmode="decimal">
                            <div class="form-text">Sales staff cannot sell below this price.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Warn me when stock is below (pairs)</label>
                            <input type="number" name="reorder_level" class="form-control" value="{{ old('reorder_level', $product->reorder_level) }}" inputmode="numeric">
                        </div>
                        @if ($product->exists)
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $product->is_active))>
                                <label class="form-check-label" for="is_active">Active (show in lists)</label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="{{ $product->exists ? route('products.show', $product) : route('products.index') }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">Save product</button>
        </div>
    </form>
@endsection

@push('page-js')
    <script>
        document.getElementById('gen-code').addEventListener('click', async () => {
            const r = await fetch('{{ route('products.next-code') }}', { headers: { Accept: 'application/json' } });
            document.getElementById('code').value = (await r.json()).code;
        });
    </script>
@endpush

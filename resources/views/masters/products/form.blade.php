@extends('layouts.vertical', ['subtitle' => $product->exists ? 'Edit Product' : 'New Product'])

@section('content')
    <x-page-header :title="$product->exists ? 'Edit ' . $product->code : 'New Product'" :crumbs="['Products' => route('products.index'), $product->exists ? 'Edit' : 'New']" />

    <form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}">
        @csrf
        @if ($product->exists) @method('PUT') @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Product details</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Product code / Article no *</label>
                                <input name="code" class="form-control" value="{{ old('code', $product->code) }}" required placeholder="e.g. WM-1021">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Model name *</label>
                                <input name="name" class="form-control" value="{{ old('name', $product->name) }}" required placeholder="e.g. Canvas Loafer">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Brand</label>
                                <select name="brand_id" class="form-select">
                                    <option value="">-</option>
                                    @foreach ($brands as $b)
                                        <option value="{{ $b->id }}" @selected(old('brand_id', $product->brand_id) == $b->id)>{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">-</option>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Colour</label>
                                <input name="colour" class="form-control" value="{{ old('colour', $product->colour) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    @foreach (['', 'Men', 'Ladies', 'Kids', 'Unisex'] as $g)
                                        <option value="{{ $g }}" @selected(old('gender', $product->gender) == $g)>{{ $g ?: '-' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Size run *</label>
                                <input name="sizes" id="sizes" class="form-control" value="{{ old('sizes', $product->sizes) }}" required placeholder="39-44 or 36,37,38,39">
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                    @foreach (['Men 39-44' => '39-44', 'Ladies 36-41' => '36-41', 'Kids 28-33' => '28-33', 'Sports 39-45' => '39-45'] as $label => $run)
                                        <button type="button" class="btn btn-sm btn-light" onclick="document.getElementById('sizes').value='{{ $run }}'">{{ $label }}</button>
                                    @endforeach
                                </div>
                                <div class="form-text">Each size gets its own SKU (e.g. {{ $product->code ?: 'WM-1021' }}-40) so stock is tracked per size.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pairs per carton *</label>
                                <input type="number" name="pairs_per_carton" class="form-control" value="{{ old('pairs_per_carton', $product->pairs_per_carton) }}" min="1" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Pricing & stock rules</h5></div>
                    <div class="card-body">
                        @if ($product->exists)
                            <div class="alert alert-light small mb-3">
                                Landed cost: <strong>{{ money($product->cost_price) }}</strong><br>
                                Markup rule: <strong>{{ $product->effectiveMarkup() }}%</strong> &rarr; suggested <strong>{{ money($product->suggestedPrice()) }}</strong>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Wholesale price per pair (Rs) *</label>
                            <input type="number" step="0.01" name="selling_price" class="form-control" value="{{ old('selling_price', $product->selling_price) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Minimum selling price (Rs)</label>
                            <input type="number" step="0.01" name="min_price" class="form-control" value="{{ old('min_price', $product->min_price) }}" placeholder="Auto: cost + {{ setting('min_margin_percent') }}%">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Markup override %</label>
                            <input type="number" step="0.01" name="markup_percent" class="form-control" value="{{ old('markup_percent', $product->markup_percent) }}" placeholder="Use category / default rule">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Re-order level (pairs) *</label>
                            <input type="number" name="reorder_level" class="form-control" value="{{ old('reorder_level', $product->reorder_level) }}" required>
                        </div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $product->is_active))>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
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

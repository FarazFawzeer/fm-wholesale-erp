@extends('layouts.vertical', ['subtitle' => $product->exists ? 'Edit product' : 'Add product'])

@section('content')
    <x-page-header :title="$product->exists ? 'Edit ' . $product->name : 'Add product'" :crumbs="['Products' => route('products.index'), $product->exists ? 'Edit' : 'Add']" />

    <form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($product->exists) @method('PUT') @endif

        <div class="row g-3">
            {{-- Photo --}}
            <div class="col-lg-4">
                <div class="card mb-0 h-100">
                    <div class="card-header"><h5 class="card-title mb-0"><iconify-icon icon="solar:camera-outline" class="align-middle me-1"></iconify-icon>Photo</h5></div>
                    <div class="card-body">
                        <label class="photo-drop" for="photo">
                            <img src="{{ $product->image_path ? asset($product->image_path) : '' }}" alt="" class="{{ $product->image_path ? '' : 'd-none' }}" id="photo-preview">
                            <span class="photo-hint {{ $product->image_path ? 'd-none' : '' }}">
                                <iconify-icon icon="solar:gallery-add-outline"></iconify-icon>
                                Tap to add a photo<br><small class="text-muted">or take one with the phone camera</small>
                            </span>
                        </label>
                        <input type="file" name="photo" id="photo" accept="image/*" class="d-none" data-photo-preview="#photo-preview">
                        @if ($product->image_path)
                            <div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="remove_photo"><label class="form-check-label" for="remove_photo">Remove photo</label></div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Details --}}
            <div class="col-lg-8">
                <div class="card mb-0 h-100">
                    <div class="card-header"><h5 class="card-title mb-0"><iconify-icon icon="ph:sneaker" class="align-middle me-1"></iconify-icon>Details</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8"><label class="form-label">Product name *</label>
                                <input name="name" value="{{ old('name', $product->name) }}" class="form-control form-control-lg" required placeholder="e.g. Ladies Sandal 2201" autofocus></div>
                            <div class="col-md-4"><label class="form-label">Item No *</label>
                                <div class="input-group"><input name="code" value="{{ old('code', $product->code) }}" class="form-control text-uppercase fw-semibold" required placeholder="e.g. ARD-1023">
                                    <button type="button" class="btn btn-soft-primary js-gen-code" data-url="{{ route('products.next-code') }}" title="Next number">Auto</button></div></div>

                            <div class="col-6"><label class="form-label">Size from</label>
                                <input name="size_from" value="{{ old('size_from', $product->size_from) }}" class="form-control" inputmode="numeric" placeholder="30"></div>
                            <div class="col-6"><label class="form-label">Size to</label>
                                <input name="size_to" value="{{ old('size_to', $product->size_to) }}" class="form-control" inputmode="numeric" placeholder="42"></div>

                            <div class="col-md-6 pick-or-add {{ old('new_brand') ? 'adding' : '' }}"><label class="form-label">Brand</label>
                                <div class="input-group js-pick">
                                    <select name="brand_id" class="form-select"><option value="">- None -</option>
                                        @foreach ($brands as $b)<option value="{{ $b->id }}" @selected(old('brand_id', $product->brand_id) == $b->id)>{{ $b->name }}</option>@endforeach</select>
                                    <button type="button" class="btn btn-soft-primary js-add-new">+ New</button></div>
                                <div class="input-group js-new"><input name="new_brand" value="{{ old('new_brand') }}" class="form-control" placeholder="New brand name"><button type="button" class="btn btn-light js-cancel-new">Cancel</button></div></div>
                            <div class="col-md-6 pick-or-add {{ old('new_category') ? 'adding' : '' }}"><label class="form-label">Type</label>
                                <div class="input-group js-pick">
                                    <select name="category_id" class="form-select"><option value="">- None -</option>
                                        @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>@endforeach</select>
                                    <button type="button" class="btn btn-soft-primary js-add-new">+ New</button></div>
                                <div class="input-group js-new"><input name="new_category" value="{{ old('new_category') }}" class="form-control" placeholder="e.g. Ladies Sandals"><button type="button" class="btn btn-light js-cancel-new">Cancel</button></div></div>

                            <div class="col-md-6"><label class="form-label">Selling price per pair (Rs) *</label>
                                <input type="number" step="0.01" min="0" name="selling_price" value="{{ old('selling_price', $product->selling_price ?: '') }}" class="form-control form-control-lg" required inputmode="decimal"></div>
                            <div class="col-md-6"><label class="form-label">Warn me when stock is below (pairs)</label>
                                <input type="number" min="0" name="low_stock_level" value="{{ old('low_stock_level', $product->low_stock_level) }}" class="form-control" inputmode="numeric"></div>
                            <div class="col-12"><label class="form-label">Notes</label>
                                <textarea name="description" rows="2" class="form-control" placeholder="Optional">{{ old('description', $product->description) }}</textarea></div>
                            @if ($product->exists)
                                <div class="col-12"><input type="hidden" name="is_active" value="0">
                                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $product->is_active))><label class="form-check-label" for="is_active">In use (untick to hide this product from buy / sell lists)</label></div></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colours --}}
            <div class="col-12">
                <div class="card mb-0" data-colour-rows>
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h5 class="card-title mb-0"><iconify-icon icon="solar:pallete-2-outline" class="align-middle me-1"></iconify-icon>Colours {{ $product->exists ? '' : '& opening stock' }}</h5>
                        <span class="small text-muted">Total: <strong class="js-colour-total">{{ $product->exists ? $product->totalStock() : 0 }}</strong> pairs</span>
                    </div>
                    <div class="card-body">
                        <div class="quick-colours mb-3">
                            <span class="small text-muted me-1">Tap to add:</span>
                            @foreach (\App\Models\ProductColour::COMMON as $c)
                                <button type="button" class="colour-chip btn-chip" data-add-colour="{{ $c }}"><i style="background: {{ \App\Models\ProductColour::hex($c) }}"></i>{{ $c }}</button>
                            @endforeach
                        </div>

                        <div class="colour-rows js-rows">
                            @foreach ($product->colours as $i => $c)
                                <div class="colour-row" data-existing>
                                    <input type="hidden" name="colours[{{ $i }}][id]" value="{{ $c->id }}">
                                    <span class="dot" style="background: {{ \App\Models\ProductColour::hex($c->colour) }}"></span>
                                    <input name="colours[{{ $i }}][name]" value="{{ $c->colour }}" class="form-control js-name" list="colour-list">
                                    <span class="stock-now" title="In stock now"><b class="js-qty" data-qty="{{ $c->qty }}">{{ $c->qty }}</b> pairs</span>
                                    @if ($c->qty == 0)
                                        <label class="btn btn-sm btn-soft-danger mb-0" title="Remove this colour"><input type="checkbox" name="colours[{{ $i }}][delete]" value="1" class="d-none js-del-check"><iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon></label>
                                    @else
                                        <span class="btn btn-sm btn-light disabled" title="Has stock - cannot remove"><iconify-icon icon="solar:lock-keyhole-outline"></iconify-icon></span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <template class="js-row-template">
                            <div class="colour-row">
                                <span class="dot"></span>
                                <input name="colours[__i][name]" class="form-control js-name" placeholder="Colour" list="colour-list">
                                <input type="number" min="0" name="colours[__i][qty]" class="form-control js-qty-input" placeholder="Pairs" inputmode="numeric">
                                <button type="button" class="btn btn-sm btn-soft-danger js-remove" title="Remove"><iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon></button>
                            </div>
                        </template>
                        <datalist id="colour-list">@foreach (\App\Models\ProductColour::COMMON as $c)<option value="{{ $c }}">@endforeach</datalist>

                        <button type="button" class="btn btn-soft-primary btn-sm mt-2 js-add-row"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>Add colour</button>

                        <div class="row g-2 mt-3 js-opening-cost {{ $product->exists && $product->cost_price > 0 ? 'd-none' : '' }}">
                            <div class="col-md-6"><label class="form-label">Cost per pair for this opening stock (Rs)</label>
                                <input type="number" step="0.01" min="0" name="opening_cost" value="{{ old('opening_cost') }}" class="form-control" inputmode="decimal" placeholder="What you paid per pair">
                                <div class="form-text">Used to work out profit. New stock you buy later is added from <b>Buy</b>.</div></div>
                        </div>
                        @if ($product->exists)
                            <div class="form-text mt-2">To change the pairs of a colour, use <b>Correct stock</b> on the product page, or record a purchase / sale.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="sticky-actions d-flex justify-content-end gap-2 my-3">
            <a href="{{ $product->exists ? route('products.show', $product) : route('products.index') }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary px-4"><iconify-icon icon="solar:diskette-outline" class="align-middle me-1"></iconify-icon>Save product</button>
        </div>
    </form>
@endsection

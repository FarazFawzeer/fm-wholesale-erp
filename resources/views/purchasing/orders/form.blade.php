@extends('layouts.vertical', ['subtitle' => $po->exists ? 'Edit ' . $po->number : 'New Purchase Order'])

@section('content')
    <x-page-header :title="$po->exists ? 'Edit ' . $po->number : 'New Purchase Order'" :crumbs="['Purchase Orders' => route('purchase-orders.index'), $po->exists ? 'Edit' : 'New']" />

    <form method="POST" action="{{ $po->exists ? route('purchase-orders.update', $po) : route('purchase-orders.store') }}">
        @csrf
        @if ($po->exists) @method('PUT') @endif

        <div class="card step-card">
            <div class="card-header"><span class="step-no">1</span><h5 class="card-title mb-0">Supplier & dates</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="form-label">Supplier *</label>
                        <div class="select-add">
                            <select name="supplier_id" id="supplier_id" class="form-select" data-choices required>
                                <option value="">Select supplier</option>
                                @foreach ($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id', $po->supplier_id) == $s->id)>{{ $s->name }}</option>@endforeach
                            </select>
                            <button type="button" class="btn btn-soft-primary" data-quick-add="supplier" data-target="#supplier_id">+ New</button>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3"><label class="form-label">Order date *</label><input type="date" name="order_date" class="form-control" value="{{ old('order_date', $po->order_date?->format('Y-m-d')) }}" required></div>
                    <div class="col-6 col-lg-3"><label class="form-label">Expected arrival</label><input type="date" name="expected_date" class="form-control" value="{{ old('expected_date', $po->expected_date?->format('Y-m-d')) }}"></div>
                    <div class="col-md-6"><label class="form-label">Container / shipment no <span class="text-muted small">(optional)</span></label><input name="shipment_ref" class="form-control" value="{{ old('shipment_ref', $po->shipment_ref) }}"></div>
                    <div class="col-md-6"><label class="form-label">Notes <span class="text-muted small">(optional)</span></label><input name="notes" class="form-control" value="{{ old('notes', $po->notes) }}"></div>
                </div>
            </div>
        </div>

        <div class="card step-card">
            <div class="card-header"><span class="step-no">2</span><h5 class="card-title mb-0">Products</h5></div>
            <div class="card-body" data-line-editor data-mode="purchase" data-price-label="Buying price per pair (Rs)"
                data-products='@json($catalog)' data-initial='@json($initial)'>
                <select class="product-picker"></select>
                <div class="lines mt-3"></div>
                <div class="empty-lines text-center text-muted py-4">Search a product to add it, or click <strong>New product</strong>.</div>
            </div>
            <div class="card-footer totals-box">
                <div class="row-total"><span>Total pairs</span><strong class="js-sum-pairs">0</strong></div>
                <div class="row-total grand"><span>Supplier total (Rs)</span><span class="js-sum-total money">0.00</span></div>
            </div>
        </div>

        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="{{ $po->exists ? route('purchase-orders.show', $po) : route('purchase-orders.index') }}" class="btn btn-light">Cancel</a>
            <button name="save_as" value="draft" class="btn btn-soft-secondary">Save draft</button>
            <button name="save_as" value="ordered" class="btn btn-primary">Save order</button>
        </div>
    </form>
@endsection

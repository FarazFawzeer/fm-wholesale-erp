@extends('layouts.vertical', ['subtitle' => $po->exists ? 'Edit ' . $po->number : 'New Purchase Order'])

@section('content')
    <x-page-header :title="$po->exists ? 'Edit ' . $po->number : 'New Purchase Order'" :crumbs="['Purchase Orders' => route('purchase-orders.index'), $po->exists ? 'Edit' : 'New']" />

    <form method="POST" action="{{ $po->exists ? route('purchase-orders.update', $po) : route('purchase-orders.store') }}">
        @csrf
        @if ($po->exists) @method('PUT') @endif
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Products (pairs per size)</h5></div>
                    <div class="card-body" data-line-editor data-mode="purchase" data-price-label="Unit price ({{ $po->currency ?: 'USD' }})"
                        data-products='@json($catalog)' data-initial='@json($initial)'>
                        <select class="product-picker"></select>
                        <div class="lines mt-3"></div>
                        <div class="empty-lines text-center text-muted py-4">Search and add the shoes you are ordering. Use <strong>Quick fill</strong> to spread cartons across the size run.</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 order-1 order-xl-2">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Order details</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Supplier *</label>
                            <select name="supplier_id" class="form-select" data-choices required>
                                <option value="">Select supplier</option>
                                @foreach ($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id', $po->supplier_id) == $s->id)>{{ $s->name }} ({{ $s->currency }})</option>@endforeach
                            </select>
                        </div>
                        <div class="row g-2">
                            <div class="col-6 mb-3"><label class="form-label">Order date *</label><input type="date" name="order_date" class="form-control" value="{{ old('order_date', $po->order_date?->format('Y-m-d')) }}" required></div>
                            <div class="col-6 mb-3"><label class="form-label">Expected arrival</label><input type="date" name="expected_date" class="form-control" value="{{ old('expected_date', $po->expected_date?->format('Y-m-d')) }}"></div>
                            <div class="col-5 mb-3"><label class="form-label">Currency *</label>
                                <select name="currency" class="form-select">@foreach (['USD', 'CNY', 'EUR', 'INR', 'THB', 'LKR'] as $c)<option @selected(old('currency', $po->currency) == $c)>{{ $c }}</option>@endforeach</select></div>
                            <div class="col-7 mb-3"><label class="form-label">Exchange rate (Rs) *</label><input type="number" step="0.0001" name="exchange_rate" class="form-control" value="{{ old('exchange_rate', $po->exchange_rate) }}" required></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Container / shipment ref</label><input name="shipment_ref" class="form-control" value="{{ old('shipment_ref', $po->shipment_ref) }}" placeholder="e.g. MSKU 7712093"></div>
                        <div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" rows="2" class="form-control">{{ old('notes', $po->notes) }}</textarea></div>
                        <div class="totals-box border-top pt-2">
                            <div class="row-total"><span>Total pairs</span><strong class="js-sum-pairs">0</strong></div>
                            <div class="row-total grand"><span>Supplier total ({{ $po->currency ?: 'USD' }})</span><span class="js-sum-total money">0.00</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="{{ $po->exists ? route('purchase-orders.show', $po) : route('purchase-orders.index') }}" class="btn btn-light">Cancel</a>
            <button name="save_as" value="draft" class="btn btn-soft-secondary">Save draft</button>
            <button name="save_as" value="ordered" class="btn btn-primary">Save &amp; mark ordered</button>
        </div>
    </form>
@endsection

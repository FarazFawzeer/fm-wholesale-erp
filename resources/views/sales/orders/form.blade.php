@php $label = $doc->label(); @endphp
@extends('layouts.vertical', ['subtitle' => ($doc->exists ? 'Edit ' : 'New ') . $label])

@section('content')
    <x-page-header :title="$doc->exists ? 'Edit ' . $doc->number : 'New ' . $label" :crumbs="[$label . 's' => route($doc->type === 'quotation' ? 'quotations.index' : 'pre-orders.index'), $doc->exists ? 'Edit' : 'New']" />

    <form method="POST" action="{{ $doc->exists ? route('sales-docs.update', $doc) : route('sales-docs.store') }}">
        @csrf
        @if ($doc->exists) @method('PUT') @endif
        <input type="hidden" name="type" value="{{ $doc->type }}">

        <div class="card step-card">
            <div class="card-header"><span class="step-no">1</span><h5 class="card-title mb-0">Customer</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-6"><label class="form-label">Customer *</label>@include('sales.partials.customer-select', ['selected' => old('customer_id', $doc->customer_id)])</div>
                    <div class="col-6 col-lg-3"><label class="form-label">Date *</label><input type="date" name="date" value="{{ old('date', $doc->date?->format('Y-m-d')) }}" class="form-control" required></div>
                    @if ($doc->type === 'quotation')
                        <div class="col-6 col-lg-3"><label class="form-label">Price valid until</label><input type="date" name="valid_until" value="{{ old('valid_until', $doc->valid_until?->format('Y-m-d')) }}" class="form-control"></div>
                    @endif
                    <div class="col-12"><label class="form-label">Notes</label><input name="notes" class="form-control" value="{{ old('notes', $doc->notes) }}" placeholder="Optional"></div>
                </div>
            </div>
        </div>

        <div class="card step-card">
            <div class="card-header"><span class="step-no">2</span><h5 class="card-title mb-0">Products</h5></div>
            <div class="card-body" data-line-editor data-mode="sales" data-check-stock="0"
                data-max-discount="{{ setting('max_discount_percent') }}" data-manager="{{ auth()->user()->isManager() ? 1 : 0 }}"
                data-products='@json($catalog)' data-initial='@json($initial)'>
                <select class="product-picker"></select>
                <div class="lines mt-3"></div>
                <div class="empty-lines text-center text-muted py-4">Add products. Stock is not taken until you make the invoice.</div>
            </div>
            <div class="card-footer totals-box">
                <div class="row-total"><span>Pairs</span><strong class="js-sum-pairs">0</strong></div>
                <div class="row-total text-danger"><span>Discount</span><span class="js-sum-discount money">0.00</span></div>
                <div class="row-total grand"><span>Total (Rs)</span><span class="js-sum-total money">0.00</span></div>
            </div>
        </div>

        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="{{ url()->previous() }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary px-4">Save {{ strtolower($label) }}</button>
        </div>
    </form>
@endsection

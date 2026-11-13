@extends('layouts.vertical', ['subtitle' => 'New purchase'])

@section('content')
    <x-page-header title="Buy from supplier" :crumbs="['Purchases' => route('purchases.index'), 'New']" />

    <form method="POST" action="{{ route('purchases.store') }}" class="doc-form">
        @csrf
        <div class="row g-3">
            <div class="col-xl-8">
                <div class="card step-card">
                    <div class="card-header"><span class="step-no">1</span><h5 class="card-title mb-0">Supplier</h5></div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="select-add">
                                    <select name="supplier_id" id="supplier_id" class="form-select" data-choices data-placeholder="Choose supplier..." required>
                                        <option value="">Choose supplier...</option>
                                        @foreach ($suppliers as $s)<option value="{{ $s->id }}" @selected($supplierId == $s->id)>{{ $s->label() }}</option>@endforeach
                                    </select>
                                    <button type="button" class="btn btn-soft-primary" data-quick-add="supplier" data-target="#supplier_id">+ New</button>
                                </div>
                            </div>
                            <div class="col-6"><label class="form-label small mb-1">Date</label><input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" class="form-control" required></div>
                            <div class="col-6"><label class="form-label small mb-1">Supplier bill no</label><input name="supplier_bill_no" value="{{ old('supplier_bill_no') }}" class="form-control" placeholder="Optional"></div>
                        </div>
                    </div>
                </div>

                @include('partials.item-editor', ['mode' => 'buy', 'products' => $products, 'start' => $startProduct])
            </div>

            <div class="col-xl-4">
                <div class="sticky-xl-top" style="top: 90px">
                    @include('partials.payment-box', ['step' => 3, 'startEmpty' => '1', 'title' => 'Payment to supplier', 'hint' => 'Pay now with one or more methods, or leave it to pay later.', 'balanceLabel' => 'We will owe'])
                    <div class="card">
                        <div class="card-body">
                            <label class="form-label small">Notes</label>
                            <textarea name="notes" rows="2" class="form-control mb-3" placeholder="Optional">{{ old('notes') }}</textarea>
                            <button class="btn btn-primary btn-lg w-100"><iconify-icon icon="solar:check-circle-outline" class="align-middle me-1"></iconify-icon>Save purchase & add to stock</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

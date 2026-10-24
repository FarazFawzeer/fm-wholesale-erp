@extends('layouts.vertical', ['subtitle' => 'New Invoice'])

@section('content')
    <x-page-header title="New Invoice" :crumbs="['Invoices' => route('invoices.index'), 'New']" />

    @if ($order)
        <div class="alert alert-info small">Making an invoice from {{ $order->label() }} <a href="{{ route('sales-docs.show', $order) }}">{{ $order->number }}</a>. Check the pairs against stock before saving.</div>
    @endif

    <form method="POST" action="{{ route('invoices.store') }}" id="invoice-form">
        @csrf
        <input type="hidden" name="sales_order_id" value="{{ $order?->id }}">

        {{-- Step 1: who and when --}}
        <div class="card step-card">
            <div class="card-header"><span class="step-no">1</span><h5 class="card-title mb-0">Customer</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="form-label">Customer *</label>
                        @include('sales.partials.customer-select', ['selected' => $customerId])
                    </div>
                    <div class="col-6 col-lg-3"><label class="form-label">Date *</label><input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" class="form-control" required></div>
                    <div class="col-6 col-lg-3"><label class="form-label">Store</label>
                        <select name="warehouse_id" id="warehouse_id" class="form-select">@foreach ($warehouses as $w)<option value="{{ $w->id }}" @selected(old('warehouse_id', $w->is_default ? $w->id : null) == $w->id)>{{ $w->name }}</option>@endforeach</select></div>
                </div>
            </div>
        </div>

        {{-- Step 2: what --}}
        <div class="card step-card">
            <div class="card-header"><span class="step-no">2</span><h5 class="card-title mb-0">Products</h5></div>
            <div class="card-body" data-line-editor data-mode="sales" data-check-stock="1"
                data-max-discount="{{ setting('max_discount_percent') }}" data-manager="{{ auth()->user()->isManager() ? 1 : 0 }}"
                data-products='@json($catalog)' data-initial='@json($initial)'>
                <select class="product-picker"></select>
                <div class="lines mt-3"></div>
                <div class="empty-lines text-center text-muted py-4">Search a product to add it. The price is filled in for this customer.</div>
            </div>
            <div class="card-footer totals-box">
                <div class="row-total"><span>Pairs</span><strong class="js-sum-pairs">0</strong></div>
                <div class="row-total text-danger"><span>Discount</span><span class="js-sum-discount money">0.00</span></div>
                <div class="row-total grand"><span>Total (Rs)</span><span class="js-sum-total money">0.00</span></div>
            </div>
        </div>

        {{-- Step 3: payment --}}
        <div class="card step-card">
            <div class="card-header"><span class="step-no">3</span><h5 class="card-title mb-0">Payment</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-5">
                        <label class="form-label d-block">How is the customer paying?</label>
                        <div class="btn-group w-100 mb-3" role="group">
                            <input type="radio" class="btn-check" name="sale_type" id="st-credit" value="credit" @checked(old('sale_type', 'credit') === 'credit')>
                            <label class="btn btn-outline-primary" for="st-credit">Pay later (credit)</label>
                            <input type="radio" class="btn-check" name="sale_type" id="st-cash" value="cash" @checked(old('sale_type') === 'cash')>
                            <label class="btn btn-outline-primary" for="st-cash">Pay now (cash)</label>
                        </div>
                        <div class="form-check form-switch mb-2" id="take-payment-wrap">
                            <input class="form-check-input" type="checkbox" name="take_payment" value="1" id="take_payment" @checked(old('take_payment'))>
                            <label class="form-check-label" for="take_payment">Customer is paying some money now</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="deliver_now" value="1" id="deliver_now" @checked(old('deliver_now', true))>
                            <label class="form-check-label" for="deliver_now">Goods are going out now</label>
                        </div>
                        @if (auth()->user()->isManager())
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="override_credit" value="1" id="override_credit" @checked(old('override_credit'))>
                                <label class="form-check-label" for="override_credit">Allow even if over credit limit (manager)</label>
                            </div>
                        @endif
                        <div class="mt-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea></div>
                    </div>
                    <div class="col-lg-7">
                        <div id="payment-box" data-payment-lines data-accounts='@json($accounts)' data-initial='@json(array_values(old('payments', [])))'>
                            <div class="pay-lines"></div>
                            <button type="button" class="btn btn-sm btn-light js-add-pay"><iconify-icon icon="solar:add-circle-outline" class="align-middle"></iconify-icon> Add another way to pay</button>
                            <div class="totals-box mt-2">
                                <div class="row-total"><span>Paid now</span><strong class="js-paid money">0.00</strong></div>
                                <div class="row-total"><span>Still to pay</span><strong class="js-balance money">0.00</strong></div>
                            </div>
                        </div>
                        <div id="payment-later" class="text-muted small py-3">No money now. The full amount is added to the customer's balance.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="{{ route('invoices.index') }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary px-4">Save invoice</button>
        </div>
    </form>
@endsection

@push('page-js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const box = document.getElementById('payment-box');
            const later = document.getElementById('payment-later');
            const take = document.getElementById('take_payment');
            const wrap = document.getElementById('take-payment-wrap');
            const sync = () => {
                const cash = document.getElementById('st-cash').checked;
                wrap.classList.toggle('d-none', cash);
                const on = cash || take.checked;
                box.classList.toggle('d-none', !on);
                later.classList.toggle('d-none', on);
                box.querySelectorAll('input, select').forEach((i) => (i.disabled = !on));
            };
            document.querySelectorAll('[name=sale_type]').forEach((r) => r.addEventListener('change', sync));
            take.addEventListener('change', sync);
            setTimeout(sync, 0);
        });
    </script>
@endpush

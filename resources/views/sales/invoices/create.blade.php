@extends('layouts.vertical', ['subtitle' => 'New Invoice'])

@section('content')
    <x-page-header title="New Invoice" :crumbs="['Invoices' => route('invoices.index'), 'New']" />

    @if ($order)
        <div class="alert alert-info small">Creating invoice from {{ $order->label() }} <a href="{{ route('sales-docs.show', $order) }}">{{ $order->number }}</a>. Check quantities against stock before saving.</div>
    @endif

    <form method="POST" action="{{ route('invoices.store') }}" id="invoice-form">
        @csrf
        <input type="hidden" name="sales_order_id" value="{{ $order?->id }}">
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Items (pairs per size)</h5></div>
                    <div class="card-body" data-line-editor data-mode="sales" data-check-stock="1"
                        data-max-discount="{{ setting('max_discount_percent') }}" data-manager="{{ auth()->user()->isManager() ? 1 : 0 }}"
                        data-products='@json($catalog)' data-initial='@json($initial)'>
                        <select class="product-picker"></select>
                        <div class="lines mt-3"></div>
                        <div class="empty-lines text-center text-muted py-4">Search a product to add it. Prices follow the customer's group automatically.</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 order-1 order-xl-2">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Customer *</label>
                            @include('sales.partials.customer-select', ['selected' => $customerId])
                        </div>
                        <div class="row g-2">
                            <div class="col-6 mb-3"><label class="form-label">Date *</label><input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" class="form-control" required></div>
                            <div class="col-6 mb-3"><label class="form-label">Store</label>
                                <select name="warehouse_id" id="warehouse_id" class="form-select">@foreach ($warehouses as $w)<option value="{{ $w->id }}" @selected(old('warehouse_id', $w->is_default ? $w->id : null) == $w->id)>{{ $w->name }}</option>@endforeach</select></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Sale type</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="sale_type" id="st-credit" value="credit" @checked(old('sale_type', 'credit') === 'credit')>
                                <label class="btn btn-outline-primary" for="st-credit">Credit sale</label>
                                <input type="radio" class="btn-check" name="sale_type" id="st-cash" value="cash" @checked(old('sale_type') === 'cash')>
                                <label class="btn btn-outline-primary" for="st-cash">Cash sale</label>
                            </div>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="deliver_now" value="1" id="deliver_now" @checked(old('deliver_now', true))>
                            <label class="form-check-label" for="deliver_now">Deliver all items now (create delivery note)</label>
                        </div>
                        @if (auth()->user()->isManager())
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="override_credit" value="1" id="override_credit" @checked(old('override_credit'))>
                                <label class="form-check-label" for="override_credit">Manager override: allow over credit limit / overdue</label>
                            </div>
                        @endif
                        <div class="mb-0"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea></div>
                        <div class="totals-box border-top mt-3 pt-2">
                            <div class="row-total"><span>Pairs</span><strong class="js-sum-pairs">0</strong></div>
                            <div class="row-total"><span>Gross</span><span class="js-sum-gross money">0.00</span></div>
                            <div class="row-total text-danger"><span>Discount</span><span class="js-sum-discount money">0.00</span></div>
                            <div class="row-total grand"><span>Total (Rs)</span><span class="js-sum-total money">0.00</span></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Payment</h5>
                        <div class="form-check form-switch mb-0" id="take-payment-wrap">
                            <input class="form-check-input" type="checkbox" name="take_payment" value="1" id="take_payment" @checked(old('take_payment'))>
                            <label class="form-check-label small" for="take_payment">Paid now</label>
                        </div>
                    </div>
                    <div class="card-body" id="payment-box" data-payment-lines data-accounts='@json($accounts)' data-initial='@json(array_values(old('payments', [])))'>
                        <div class="pay-lines"></div>
                        <button type="button" class="btn btn-sm btn-light js-add-pay"><iconify-icon icon="solar:add-circle-outline" class="align-middle"></iconify-icon> Add another method (split payment)</button>
                        <div class="totals-box mt-2 small">
                            <div class="row-total"><span>Paid</span><strong class="js-paid">0.00</strong></div>
                            <div class="row-total"><span>Balance on credit</span><strong class="js-balance">0.00</strong></div>
                        </div>
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
            const take = document.getElementById('take_payment');
            const wrap = document.getElementById('take-payment-wrap');
            const sync = () => {
                const cash = document.getElementById('st-cash').checked;
                wrap.classList.toggle('d-none', cash);
                const on = cash || take.checked;
                box.classList.toggle('d-none', !on);
                box.querySelectorAll('input, select').forEach((i) => (i.disabled = !on));
            };
            document.querySelectorAll('[name=sale_type]').forEach((r) => r.addEventListener('change', sync));
            take.addEventListener('change', sync);
            setTimeout(sync, 0);
        });
    </script>
@endpush

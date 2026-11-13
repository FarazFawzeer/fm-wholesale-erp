{{--
    Product lines for Buy (mode=buy: qty per colour, new colours allowed)
    and Sell (mode=sell: qty per colour, limited to stock).
--}}
@php $initial = old('items') ? array_values(old('items')) : []; @endphp
<div class="card step-card" data-items data-mode="{{ $mode }}" data-products='@json($products)' data-initial='@json($initial)' data-start="{{ $start ?? '' }}">
    <div class="card-header"><span class="step-no">2</span><h5 class="card-title mb-0">Products</h5></div>
    <div class="card-body">
        <div class="item-search">
            <iconify-icon icon="solar:magnifer-linear" class="search-icon"></iconify-icon>
            <input type="search" class="form-control form-control-lg js-search" placeholder="Search product name or code..." autocomplete="off">
            <button type="button" class="btn btn-soft-primary js-new-product" data-quick-add="product"><iconify-icon icon="solar:add-circle-outline"></iconify-icon><span class="d-none d-sm-inline ms-1">New product</span></button>
            <div class="item-results js-results"></div>
        </div>

        <div class="js-lines mt-3"></div>
        <div class="empty-lines text-center text-muted py-4">
            <iconify-icon icon="ph:sneaker" class="fs-36 d-block mx-auto mb-1"></iconify-icon>
            Search above and tap a product to add it.
        </div>

        <div class="totals-box mt-3 js-totals">
            <div class="row-total"><span>Pairs</span><strong class="js-pairs">0</strong></div>
            <div class="row-total"><span>Amount</span><strong class="js-subtotal money">0.00</strong></div>
            <div class="row-total align-items-center"><span>Discount (Rs)</span>
                <input type="number" step="0.01" min="0" name="discount" value="{{ old('discount') }}" class="form-control form-control-sm text-end js-discount" style="max-width: 140px" inputmode="decimal" placeholder="0"></div>
            @if ($mode === 'buy')
                <div class="row-total align-items-center"><span>Transport / other charges (Rs)</span>
                    <input type="number" step="0.01" min="0" name="other_costs" value="{{ old('other_costs') }}" class="form-control form-control-sm text-end js-other" style="max-width: 140px" inputmode="decimal" placeholder="0"></div>
            @endif
            <div class="row-total grand"><span>Total</span><span class="money">Rs <span class="js-total">0.00</span></span></div>
        </div>
        <datalist id="colour-names">@foreach (\App\Models\ProductColour::COMMON as $c)<option value="{{ $c }}">@endforeach</datalist>
    </div>
</div>

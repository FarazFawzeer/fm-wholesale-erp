@extends('layouts.vertical', ['subtitle' => 'New sale'])

@php $initial = old('items') ? array_values(old('items')) : []; @endphp

@section('content')
    <form method="POST" action="{{ route('invoices.store') }}" class="doc-form pos" data-pos
        data-products='@json($products)' data-initial='@json($initial)' data-start="{{ $startProduct }}">
        @csrf

        <div class="pos-top">
            <div>
                <h4 class="mb-0">New sale</h4>
                <div class="small text-muted">Search or tap a product, choose pairs per colour, then take payment.</div>
            </div>
            <a href="{{ route('invoices.index') }}" class="btn btn-light btn-sm d-none d-md-inline-flex">All sales</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        @endif

        <div class="pos-wrap">
            {{-- ===== Products ===== --}}
            <section class="pos-main">
                <div class="pos-search">
                    <iconify-icon icon="solar:magnifer-linear" class="pos-search-icon"></iconify-icon>
                    <input type="search" class="form-control js-pos-search" placeholder="Search item no, name, brand or colour..." autocomplete="off" autofocus>
                    <kbd class="d-none d-lg-inline">/</kbd>
                    <div class="pos-suggest js-suggest"></div>
                </div>

                <div class="pos-cats js-cats">
                    <button type="button" class="pos-cat active" data-cat="">All</button>
                    @foreach ($categories as $c)<button type="button" class="pos-cat" data-cat="{{ $c->id }}">{{ $c->name }}</button>@endforeach
                    <button type="button" class="pos-cat" data-cat="instock">In stock</button>
                </div>

                <div class="pos-grid js-grid"></div>
                <div class="pos-empty js-empty d-none">
                    <iconify-icon icon="ph:sneaker"></iconify-icon>
                    <div>No product matches "<b class="js-empty-q"></b>"</div>
                    <button type="button" class="btn btn-sm btn-light mt-2" data-quick-add="product">+ Add as new product</button>
                </div>
            </section>

            {{-- ===== Bill ===== --}}
            <aside class="pos-cart">
                <div class="pos-cart-head">
                    <div class="d-flex align-items-center gap-2">
                        <span class="pos-cart-icon"><iconify-icon icon="solar:bill-list-bold-duotone"></iconify-icon></span>
                        <div><div class="fw-bold">Current bill</div><div class="small text-muted"><span class="js-cart-count">0</span> items &middot; <span class="js-cart-pairs">0</span> pairs</div></div>
                    </div>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-light js-clear" title="Clear bill"><iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon></button>
                        <button type="button" class="btn btn-sm btn-light d-lg-none js-close-cart" title="Close"><iconify-icon icon="solar:close-circle-outline"></iconify-icon></button>
                    </div>
                </div>

                <div class="pos-cart-body">
                    <div class="pos-customer">
                        <div class="select-add">
                            <select name="customer_id" id="customer_id" class="form-select" data-choices data-placeholder="Choose customer..." data-due-warning="#due-warning" required>
                                <option value="">Choose customer...</option>
                                @foreach ($customers as $c)<option value="{{ $c->id }}" data-due="{{ round(max(0, $c->due), 2) }}" @selected($customerId == $c->id)>{{ $c->label() }}</option>@endforeach
                            </select>
                            <button type="button" class="btn btn-light" data-quick-add="customer" data-target="#customer_id" title="New customer"><iconify-icon icon="solar:user-plus-outline"></iconify-icon></button>
                        </div>
                        <div id="due-warning" class="pos-due d-none">
                            <iconify-icon icon="solar:bell-bing-bold"></iconify-icon>
                            <span>Already owes <b class="js-due"></b> from earlier bills</span>
                        </div>
                    </div>

                    <div class="pos-lines js-cart"></div>
                    <div class="pos-cart-empty js-cart-empty">
                        <iconify-icon icon="solar:cart-large-minimalistic-outline"></iconify-icon>
                        <div>No items yet</div>
                        <small>Tap a product to add it</small>
                    </div>

                    <div class="pos-totals">
                        <div><span>Amount</span><span class="money js-subtotal">Rs 0.00</span></div>
                        <div class="align-items-center"><span>Discount</span>
                            <div class="input-group input-group-sm" style="max-width: 150px"><span class="input-group-text">Rs</span>
                                <input type="number" step="0.01" min="0" name="discount" value="{{ old('discount') }}" class="form-control text-end js-discount" inputmode="decimal" placeholder="0"></div></div>
                        <div class="pos-grand"><span>Total</span><span class="money">Rs <span class="js-total">0.00</span></span></div>
                    </div>

                    @include('partials.payment-box', ['startEmpty' => '1', 'payLater' => true, 'title' => 'Payment', 'balanceLabel' => 'Customer will owe'])

                    <details class="pos-more">
                        <summary>Date & note</summary>
                        <div class="row g-2 mt-1">
                            <div class="col-5"><input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" class="form-control form-control-sm" required></div>
                            <div class="col-7"><input name="notes" value="{{ old('notes') }}" class="form-control form-control-sm" placeholder="Note on invoice"></div>
                        </div>
                    </details>
                </div>

                <div class="pos-cart-foot">
                    <button class="btn btn-brand btn-lg w-100 js-save" disabled>
                        <iconify-icon icon="solar:check-circle-bold" class="align-middle me-1"></iconify-icon>Save sale &middot; Rs <span class="js-total">0.00</span>
                    </button>
                </div>
            </aside>
        </div>

        {{-- Phone: bill bar --}}
        <button type="button" class="pos-bar js-open-cart">
            <span class="pos-bar-count js-cart-count">0</span>
            <span class="flex-grow-1 text-start"><span class="d-block small opacity-75">View bill</span><b>Rs <span class="js-total">0.00</span></b></span>
            <iconify-icon icon="solar:alt-arrow-up-linear" class="fs-22"></iconify-icon>
        </button>
        <div class="pos-backdrop js-close-cart"></div>
    </form>

    {{-- Choose pairs per colour --}}
    <div class="modal fade" id="pos-pick" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="pick-head">
                    <div class="pick-img js-pick-img"></div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="small text-muted js-pick-code"></div>
                        <h5 class="mb-1 js-pick-name"></h5>
                        <div class="small text-muted js-pick-meta"></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="pick-label">Pairs per colour</div>
                    <div class="pick-colours js-pick-colours"></div>
                    <div class="pick-price">
                        <label class="form-label mb-1">Price per pair</label>
                        <div class="input-group"><span class="input-group-text">Rs</span><input type="number" step="0.01" min="0" class="form-control fw-bold js-pick-price" inputmode="decimal"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <div><div class="small text-muted"><span class="js-pick-pairs">0</span> pairs</div><div class="fw-bold fs-5">Rs <span class="js-pick-total">0.00</span></div></div>
                    <button type="button" class="btn btn-primary btn-lg px-4 js-pick-add">Add to bill</button>
                </div>
            </div>
        </div>
    </div>
@endsection

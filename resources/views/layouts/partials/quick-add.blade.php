{{-- Quick-add pop-ups, opened by any button with data-quick-add="product|customer|supplier" --}}
<div class="modal fade" id="qa-product" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content js-quick-form" action="{{ route('products.quick') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-header"><h5 class="modal-title">New product</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="alert alert-danger small d-none js-errors"></div>
                <div class="row g-2">
                    <div class="col-12"><label class="form-label">Product name *</label><input name="name" class="form-control" required></div>
                    <div class="col-12"><label class="form-label">Item No</label>
                        <div class="input-group"><input name="code" class="form-control text-uppercase" placeholder="Empty = automatic ({{ setting('item_prefix', 'ARD') }}-...)"><button type="button" class="btn btn-soft-primary js-gen-code" data-url="{{ route('products.next-code') }}">Auto</button></div></div>
                    <div class="col-6"><label class="form-label">Size from</label><input name="size_from" class="form-control" inputmode="numeric" placeholder="30"></div>
                    <div class="col-6"><label class="form-label">Size to</label><input name="size_to" class="form-control" inputmode="numeric" placeholder="42"></div>
                    <div class="col-12"><label class="form-label">Colours</label><input name="colour_names" class="form-control" placeholder="Black, White, Blue">
                        <div class="form-text">Separate with commas. You can add more colours later.</div></div>
                    <div class="col-6"><label class="form-label">Selling price / pair</label><input type="number" step="0.01" min="0" name="selling_price" class="form-control" inputmode="decimal"></div>
                    <div class="col-6"><label class="form-label">Brand</label><input name="brand_name" class="form-control" list="qa-brands" placeholder="Optional">
                        <datalist id="qa-brands">@foreach (\App\Models\Brand::orderBy('name')->pluck('name') as $b)<option value="{{ $b }}">@endforeach</datalist></div>
                    <div class="col-12"><label class="form-label">Photo</label><input type="file" name="photo" accept="image/*" class="form-control"></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save & add</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="qa-customer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content js-quick-form" action="{{ route('customers.quick') }}" method="POST">
            @csrf
            <div class="modal-header"><h5 class="modal-title">New customer</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="alert alert-danger small d-none js-errors"></div>
                <div class="row g-2">
                    <div class="col-12"><label class="form-label">Customer name *</label><input name="name" class="form-control" required></div>
                    <div class="col-12"><label class="form-label">Shop name</label><input name="shop_name" class="form-control"></div>
                    <div class="col-6"><label class="form-label">Phone / WhatsApp</label><input name="phone" class="form-control" inputmode="tel" placeholder="077..."></div>
                    <div class="col-6"><label class="form-label">City</label><input name="city" class="form-control"></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save customer</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="qa-supplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content js-quick-form" action="{{ route('suppliers.quick') }}" method="POST">
            @csrf
            <div class="modal-header"><h5 class="modal-title">New supplier</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="alert alert-danger small d-none js-errors"></div>
                <div class="row g-2">
                    <div class="col-12"><label class="form-label">Supplier name *</label><input name="name" class="form-control" required></div>
                    <div class="col-6"><label class="form-label">Phone</label><input name="phone" class="form-control" inputmode="tel"></div>
                    <div class="col-6"><label class="form-label">Contact person</label><input name="contact_person" class="form-control"></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save supplier</button></div>
        </form>
    </div>
</div>

{{-- Check price & stock: opened from the top bar on any page --}}
<div class="modal fade" id="product-lookup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable lookup-dialog">
        <div class="modal-content" data-lookup data-url="{{ route('products.lookup') }}">
            <div class="lookup-head">
                <iconify-icon icon="solar:magnifer-linear" class="lookup-icon"></iconify-icon>
                <input type="search" class="form-control js-lookup-q" placeholder="Type item no, name, brand or colour..." autocomplete="off">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
            <div class="modal-body js-lookup-results">
                <div class="lookup-hint"><iconify-icon icon="solar:tag-price-outline"></iconify-icon><div>Customer asking for a price?<br><small>Search a product to see its price, sizes and stock in every colour.</small></div></div>
            </div>
        </div>
    </div>
</div>

{{-- iPhone / iPad: Safari has no install button, so show the steps --}}
<div class="modal fade" id="ios-install" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <img src="{{ asset('images/app/icon-192.png') }}" alt="" width="72" height="72" class="rounded-4 shadow-sm mb-3">
                <h5 class="fw-bold">Install on iPhone</h5>
                <ol class="ios-steps text-start">
                    <li>Open this page in <b>Safari</b>.</li>
                    <li>Tap the <b>Share</b> button <iconify-icon icon="solar:share-outline" class="align-middle"></iconify-icon> at the bottom.</li>
                    <li>Tap <b>Add to Home Screen</b>, then <b>Add</b>.</li>
                </ol>
                <button type="button" class="btn btn-dark w-100" data-bs-dismiss="modal">Got it</button>
            </div>
        </div>
    </div>
</div>

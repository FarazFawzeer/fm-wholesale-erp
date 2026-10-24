{{-- Quick-add pop-ups, opened by any button with data-quick-add="product|customer|supplier" --}}
@php
    $qaBrands = \App\Models\Brand::orderBy('name')->get(['id', 'name']);
    $qaCategories = \App\Models\Category::orderBy('name')->get(['id', 'name']);
    $qaGroups = \App\Models\CustomerGroup::orderBy('id')->get(['id', 'name']);
@endphp

<div class="modal fade" id="qa-product" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content js-quick-form" action="{{ route('products.quick') }}" method="POST">
            @csrf
            <div class="modal-header"><h5 class="modal-title">New product</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="alert alert-danger small d-none js-errors"></div>
                <div class="row g-2">
                    <div class="col-12"><label class="form-label">Product name *</label><input name="name" class="form-control" required></div>
                    <div class="col-12"><label class="form-label">Product code</label>
                        <div class="input-group"><input name="code" class="form-control" placeholder="Automatic"><button type="button" class="btn btn-soft-primary js-gen-code" data-url="{{ route('products.next-code') }}">Auto</button></div></div>
                    <div class="col-6"><label class="form-label">Brand</label>
                        <select name="brand_id" class="form-select"><option value="">-</option>@foreach ($qaBrands as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select>
                        <input name="new_brand" class="form-control form-control-sm mt-1" placeholder="or type a new brand"></div>
                    <div class="col-6"><label class="form-label">Category</label>
                        <select name="category_id" class="form-select"><option value="">-</option>@foreach ($qaCategories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
                        <input name="new_category" class="form-control form-control-sm mt-1" placeholder="or type a new category"></div>
                    <div class="col-12"><label class="form-label">Colour</label><input name="colour" class="form-control" placeholder="Optional"></div>
                    <div class="col-6"><label class="form-label">Size from</label><input name="size_from" class="form-control" inputmode="numeric" placeholder="e.g. 28"></div>
                    <div class="col-6"><label class="form-label">Size to</label><input name="size_to" class="form-control" inputmode="numeric" placeholder="e.g. 44"></div>
                    <div class="col-12"><label class="form-label">Selling price per pair (Rs) *</label><input type="number" step="0.01" name="selling_price" class="form-control" required inputmode="decimal"></div>
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
                    <div class="col-12"><label class="form-label">Shop / customer name *</label><input name="name" class="form-control" required></div>
                    <div class="col-6"><label class="form-label">Phone</label><input name="phone" class="form-control" inputmode="tel"></div>
                    <div class="col-6"><label class="form-label">City</label><input name="city" class="form-control"></div>
                    <div class="col-6"><label class="form-label">Price group</label>
                        <select name="customer_group_id" class="form-select">@foreach ($qaGroups as $g)<option value="{{ $g->id }}" @selected($loop->index === 1)>{{ $g->name }}</option>@endforeach</select></div>
                    <div class="col-6"><label class="form-label">Credit limit (Rs)</label><input type="number" name="credit_limit" class="form-control" placeholder="0 = no limit" inputmode="decimal"></div>
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
                    <div class="col-6"><label class="form-label">Country</label><input name="country" class="form-control"></div>
                    <div class="col-6"><label class="form-label">Phone</label><input name="phone" class="form-control" inputmode="tel"></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save supplier</button></div>
        </form>
    </div>
</div>

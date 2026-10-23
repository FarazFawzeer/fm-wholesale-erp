@extends('layouts.vertical', ['subtitle' => $customer->exists ? 'Edit Customer' : 'New Customer'])

@section('content')
    <x-page-header :title="$customer->exists ? 'Edit ' . $customer->name : 'New Customer'" :crumbs="['Customers' => route('customers.index'), $customer->exists ? 'Edit' : 'New']" />

    <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
        @csrf
        @if ($customer->exists) @method('PUT') @endif
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Customer details</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3"><label class="form-label">Code *</label><input name="code" class="form-control" value="{{ old('code', $customer->code) }}" required></div>
                            <div class="col-md-5"><label class="form-label">Name *</label><input name="name" class="form-control" value="{{ old('name', $customer->name) }}" required></div>
                            <div class="col-md-4"><label class="form-label">Business name</label><input name="business_name" class="form-control" value="{{ old('business_name', $customer->business_name) }}"></div>
                            <div class="col-md-4"><label class="form-label">Phone</label><input name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}" inputmode="tel"></div>
                            <div class="col-md-4"><label class="form-label">WhatsApp</label><input name="whatsapp" class="form-control" value="{{ old('whatsapp', $customer->whatsapp) }}" inputmode="tel"></div>
                            <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}"></div>
                            <div class="col-md-8"><label class="form-label">Address</label><input name="address" class="form-control" value="{{ old('address', $customer->address) }}"></div>
                            <div class="col-md-4"><label class="form-label">City</label><input name="city" class="form-control" value="{{ old('city', $customer->city) }}"></div>
                            <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $customer->notes) }}</textarea></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Pricing & credit</h5></div>
                    <div class="card-body">
                        <div class="mb-3"><label class="form-label">Customer group (price tier)</label>
                            <select name="customer_group_id" class="form-select">
                                <option value="">-</option>
                                @foreach ($groups as $g)<option value="{{ $g->id }}" @selected(old('customer_group_id', $customer->customer_group_id) == $g->id)>{{ $g->name }} ({{ $g->discount_percent > 0 ? '-' : '+' }}{{ abs($g->discount_percent) }}%)</option>@endforeach
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">Credit limit (Rs) *</label><input type="number" step="0.01" name="credit_limit" class="form-control" value="{{ old('credit_limit', $customer->credit_limit) }}" required><div class="form-text">0 = no credit limit check</div></div>
                        <div class="mb-3"><label class="form-label">Payment terms (days) *</label><input type="number" name="payment_terms_days" class="form-control" value="{{ old('payment_terms_days', $customer->payment_terms_days) }}" required></div>
                        <div class="mb-0"><label class="form-label">Opening balance (Rs)</label><input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ old('opening_balance', $customer->opening_balance) }}"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="{{ route('customers.index') }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">Save customer</button>
        </div>
    </form>
@endsection

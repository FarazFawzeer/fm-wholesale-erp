@extends('layouts.vertical', ['subtitle' => $customer->exists ? 'Edit customer' : 'Add customer'])

@section('content')
    <x-page-header :title="$customer->exists ? 'Edit ' . $customer->name : 'Add customer'" :crumbs="['Customers' => route('customers.index'), $customer->exists ? 'Edit' : 'Add']" />

    <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
        @csrf
        @if ($customer->exists) @method('PUT') @endif
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Customer name *</label><input name="name" value="{{ old('name', $customer->name) }}" class="form-control form-control-lg" required autofocus></div>
                    <div class="col-md-6"><label class="form-label">Shop name</label><input name="shop_name" value="{{ old('shop_name', $customer->shop_name) }}" class="form-control form-control-lg"></div>
                    <div class="col-6 col-md-3"><label class="form-label">Phone</label><input name="phone" value="{{ old('phone', $customer->phone) }}" class="form-control" inputmode="tel" placeholder="077..."></div>
                    <div class="col-6 col-md-3"><label class="form-label">WhatsApp</label><input name="whatsapp" value="{{ old('whatsapp', $customer->whatsapp) }}" class="form-control" inputmode="tel" placeholder="Same as phone"></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email', $customer->email) }}" class="form-control"></div>
                    <div class="col-md-8"><label class="form-label">Address</label><input name="address" value="{{ old('address', $customer->address) }}" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">City / town</label><input name="city" value="{{ old('city', $customer->city) }}" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Old balance - owed before using this system (Rs)</label>
                        <input type="number" step="0.01" min="0" name="opening_balance" value="{{ old('opening_balance', $customer->opening_balance ?: '') }}" class="form-control" inputmode="decimal" placeholder="0"></div>
                    <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" rows="2" class="form-control">{{ old('notes', $customer->notes) }}</textarea></div>
                    @if ($customer->exists)
                        <div class="col-12"><input type="hidden" name="is_active" value="0">
                            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked($customer->is_active)><label class="form-check-label" for="is_active">Active</label></div></div>
                    @endif
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex justify-content-end gap-2 mb-3">
            <a href="{{ $customer->exists ? route('customers.show', $customer) : route('customers.index') }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary px-4">Save customer</button>
        </div>
    </form>
@endsection

@extends('layouts.vertical', ['subtitle' => $supplier->exists ? 'Edit Supplier' : 'New Supplier'])

@section('content')
    <x-page-header :title="$supplier->exists ? 'Edit ' . $supplier->name : 'New Supplier'" :crumbs="['Suppliers' => route('suppliers.index'), $supplier->exists ? 'Edit' : 'New']" />

    <form method="POST" action="{{ $supplier->exists ? route('suppliers.update', $supplier) : route('suppliers.store') }}">
        @csrf
        @if ($supplier->exists) @method('PUT') @endif
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3"><label class="form-label">Code *</label><input name="code" class="form-control" value="{{ old('code', $supplier->code) }}" required></div>
                    <div class="col-md-9"><label class="form-label">Supplier name *</label><input name="name" class="form-control" value="{{ old('name', $supplier->name) }}" required></div>
                    <div class="col-md-4"><label class="form-label">Country</label><input name="country" class="form-control" value="{{ old('country', $supplier->country) }}"></div>
                    <div class="col-md-4"><label class="form-label">Purchase currency *</label>
                        <select name="currency" class="form-select">
                            @foreach (['USD', 'CNY', 'EUR', 'INR', 'THB', 'LKR'] as $c)<option @selected(old('currency', $supplier->currency) == $c)>{{ $c }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-4"><label class="form-label">Payment terms (days) *</label><input type="number" name="payment_terms_days" class="form-control" value="{{ old('payment_terms_days', $supplier->payment_terms_days) }}" required></div>
                    <div class="col-md-4"><label class="form-label">Contact person</label><input name="contact_person" class="form-control" value="{{ old('contact_person', $supplier->contact_person) }}"></div>
                    <div class="col-md-4"><label class="form-label">Phone</label><input name="phone" class="form-control" value="{{ old('phone', $supplier->phone) }}"></div>
                    <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email) }}"></div>
                    <div class="col-md-8"><label class="form-label">Address</label><input name="address" class="form-control" value="{{ old('address', $supplier->address) }}"></div>
                    <div class="col-md-4"><label class="form-label">Opening balance (Rs, we owe)</label><input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ old('opening_balance', $supplier->opening_balance) }}"></div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="{{ route('suppliers.index') }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">Save supplier</button>
        </div>
    </form>
@endsection

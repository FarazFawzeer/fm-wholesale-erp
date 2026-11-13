@extends('layouts.vertical', ['subtitle' => $supplier->exists ? 'Edit supplier' : 'Add supplier'])

@section('content')
    <x-page-header :title="$supplier->exists ? 'Edit ' . $supplier->name : 'Add supplier'" :crumbs="['Suppliers' => route('suppliers.index'), $supplier->exists ? 'Edit' : 'Add']" />

    <form method="POST" action="{{ $supplier->exists ? route('suppliers.update', $supplier) : route('suppliers.store') }}">
        @csrf
        @if ($supplier->exists) @method('PUT') @endif
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Supplier / company name *</label><input name="name" value="{{ old('name', $supplier->name) }}" class="form-control form-control-lg" required autofocus></div>
                    <div class="col-md-6"><label class="form-label">Contact person</label><input name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" class="form-control form-control-lg"></div>
                    <div class="col-md-6"><label class="form-label">Phone</label><input name="phone" value="{{ old('phone', $supplier->phone) }}" class="form-control" inputmode="tel"></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="form-control"></div>
                    <div class="col-12"><label class="form-label">Address</label><input name="address" value="{{ old('address', $supplier->address) }}" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Old balance - we owed before using this system (Rs)</label>
                        <input type="number" step="0.01" min="0" name="opening_balance" value="{{ old('opening_balance', $supplier->opening_balance ?: '') }}" class="form-control" inputmode="decimal" placeholder="0"></div>
                    <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" rows="2" class="form-control">{{ old('notes', $supplier->notes) }}</textarea></div>
                    @if ($supplier->exists)
                        <div class="col-12"><input type="hidden" name="is_active" value="0">
                            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked($supplier->is_active)><label class="form-check-label" for="is_active">Active</label></div></div>
                    @endif
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex justify-content-end gap-2 mb-3">
            <a href="{{ $supplier->exists ? route('suppliers.show', $supplier) : route('suppliers.index') }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary px-4">Save supplier</button>
        </div>
    </form>
@endsection

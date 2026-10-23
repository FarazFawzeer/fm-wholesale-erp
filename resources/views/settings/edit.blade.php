@extends('layouts.vertical', ['subtitle' => 'Settings'])

@section('content')
    <x-page-header title="Company & Pricing Rules" :crumbs="['Settings']" />

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Company (shown on invoices)</h5></div>
                    <div class="card-body">
                        <div class="mb-3"><label class="form-label">Company name</label><input name="company_name" value="{{ $s['company_name'] }}" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Tagline</label><input name="company_tagline" value="{{ $s['company_tagline'] }}" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Address</label><input name="company_address" value="{{ $s['company_address'] }}" class="form-control"></div>
                        <div><label class="form-label">Phone</label><input name="company_phone" value="{{ $s['company_phone'] }}" class="form-control"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Pricing, discount & credit rules</h5></div>
                    <div class="card-body">
                        <div class="mb-3"><label class="form-label">Default markup on landed cost (%)</label><input type="number" step="0.01" name="default_markup_percent" value="{{ $s['default_markup_percent'] }}" class="form-control">
                            <div class="form-text">Used for suggested prices when the product and its category have no markup rule.</div></div>
                        <div class="mb-3"><label class="form-label">Minimum margin over cost (%)</label><input type="number" step="0.01" name="min_margin_percent" value="{{ $s['min_margin_percent'] }}" class="form-control">
                            <div class="form-text">Sets the minimum selling price when a product has no fixed minimum. Sales staff can't sell below it.</div></div>
                        <div class="mb-3"><label class="form-label">Maximum discount for sales staff (%)</label><input type="number" step="0.01" name="max_discount_percent" value="{{ $s['max_discount_percent'] }}" class="form-control">
                            <div class="form-text">Managers can go above this.</div></div>
                        <div class="row g-2">
                            <div class="col-6"><label class="form-label">Default credit terms (days)</label><input type="number" name="default_payment_terms" value="{{ $s['default_payment_terms'] }}" class="form-control"></div>
                            <div class="col-6"><label class="form-label">Remind before due (days)</label><input type="number" name="reminder_days_before_due" value="{{ $s['reminder_days_before_due'] }}" class="form-control"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex justify-content-end mb-4"><button class="btn btn-primary">Save settings</button></div>
    </form>
@endsection

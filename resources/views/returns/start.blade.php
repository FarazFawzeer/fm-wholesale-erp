@extends('layouts.vertical', ['subtitle' => 'New return'])

@section('content')
    <x-page-header title="New return" :crumbs="['Returns' => route('returns.index'), 'New']" />

    <ul class="nav nav-pills nav-scroll mb-3" role="tablist">
        <li class="nav-item"><button type="button" class="nav-link {{ $tab === 'customer' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#start-customer"><iconify-icon icon="solar:box-minimalistic-outline" class="align-middle me-1"></iconify-icon>Customer returned goods</button></li>
        <li class="nav-item"><button type="button" class="nav-link {{ $tab === 'supplier' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#start-supplier"><iconify-icon icon="solar:delivery-outline" class="align-middle me-1"></iconify-icon>Send back to supplier</button></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade {{ $tab === 'customer' ? 'show active' : '' }}" id="start-customer">
            <form method="POST" action="{{ route('returns.pick') }}" class="card">
                @csrf
                <div class="card-body">
                    <div class="start-step"><span>1</span>Which sale are the goods from?</div>
                    <select name="invoice_id" class="form-select" data-choices data-placeholder="Search invoice no or customer..." required>
                        <option value="">Search invoice no or customer...</option>
                        @foreach ($invoices as $inv)
                            <option value="{{ $inv->id }}">{{ $inv->number }} · {{ $inv->customer->name }} · {{ $inv->date->format('d M Y') }} · {{ money($inv->total - $inv->returned_amount) }}</option>
                        @endforeach
                    </select>
                    <div class="small text-muted mt-2">Tip: you can also open the sale and tap <b>Customer returned goods</b>.</div>
                    <div class="start-help">
                        <div><iconify-icon icon="solar:box-outline"></iconify-icon>Pairs go back into stock</div>
                        <div><iconify-icon icon="solar:bill-cross-outline"></iconify-icon>Amount is taken off the bill</div>
                        <div><iconify-icon icon="solar:wallet-outline"></iconify-icon>Already paid? It becomes credit for the next bill</div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end"><button class="btn btn-primary px-4">Next <iconify-icon icon="solar:arrow-right-outline" class="align-middle ms-1"></iconify-icon></button></div>
            </form>
        </div>
        <div class="tab-pane fade {{ $tab === 'supplier' ? 'show active' : '' }}" id="start-supplier">
            <form method="POST" action="{{ route('returns.pick') }}" class="card">
                @csrf
                <div class="card-body">
                    <div class="start-step"><span>1</span>Which purchase are you sending goods back from?</div>
                    <select name="purchase_id" class="form-select" data-choices data-placeholder="Search purchase no or supplier..." required>
                        <option value="">Search purchase no or supplier...</option>
                        @foreach ($purchases as $pu)
                            <option value="{{ $pu->id }}">{{ $pu->number }}{{ $pu->supplier_bill_no ? ' (' . $pu->supplier_bill_no . ')' : '' }} · {{ $pu->supplier->name }} · {{ $pu->date->format('d M Y') }} · {{ money($pu->total - $pu->returned_amount) }}</option>
                        @endforeach
                    </select>
                    <div class="small text-muted mt-2">Tip: you can also open the purchase and tap <b>Return to supplier</b>.</div>
                    <div class="start-help">
                        <div><iconify-icon icon="solar:box-outline"></iconify-icon>Pairs are taken out of stock</div>
                        <div><iconify-icon icon="solar:bill-cross-outline"></iconify-icon>Amount is taken off what you owe</div>
                        <div><iconify-icon icon="solar:wallet-outline"></iconify-icon>Already paid? It becomes credit with that supplier</div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end"><button class="btn btn-primary px-4">Next <iconify-icon icon="solar:arrow-right-outline" class="align-middle ms-1"></iconify-icon></button></div>
            </form>
        </div>
    </div>
@endsection

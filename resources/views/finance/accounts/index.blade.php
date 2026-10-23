@extends('layouts.vertical', ['subtitle' => 'Accounts & Balances'])

@section('content')
    <x-page-header title="Cash, Bank & Card Accounts" :crumbs="['Cash & Bank', 'Accounts']">
        <button class="btn btn-soft-primary" data-bs-toggle="modal" data-bs-target="#transferModal">Transfer / deposit</button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#accountModal">New account</button>
    </x-page-header>

    <div class="row g-3">
        @foreach ($accounts as $a)
            <div class="col-sm-6 col-xl-3">
                <a href="{{ route('accounts.show', $a) }}" class="card h-100 text-reset mb-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <span class="badge bg-{{ $a->type === 'cash' ? 'success' : ($a->type === 'card' ? 'info' : 'primary') }}-subtle text-{{ $a->type === 'cash' ? 'success' : ($a->type === 'card' ? 'info' : 'primary') }}">{{ ucfirst($a->type) }}</span>
                            <iconify-icon icon="{{ $a->type === 'cash' ? 'solar:wallet-outline' : ($a->type === 'card' ? 'solar:card-outline' : 'solar:buildings-outline') }}" class="fs-24 text-muted"></iconify-icon>
                        </div>
                        <div class="fw-semibold mt-2">{{ $a->name }}</div>
                        <div class="small text-muted">{{ $a->bank_name }} {{ $a->account_no }}</div>
                        <div class="fs-4 fw-bold mt-2 money">{{ money($a->balance()) }}</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="modal fade" id="transferModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('accounts.transfer') }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Transfer between accounts</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-2"><label class="form-label">From</label><select name="from_account_id" class="form-select">@foreach ($accounts as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select></div>
                    <div class="mb-2"><label class="form-label">To</label><select name="to_account_id" class="form-select">@foreach ($accounts as $a)<option value="{{ $a->id }}" @selected($loop->index === 1)>{{ $a->name }}</option>@endforeach</select></div>
                    <div class="row g-2"><div class="col-6"><label class="form-label">Amount</label><input type="number" step="0.01" name="amount" class="form-control" required></div><div class="col-6"><label class="form-label">Date</label><input type="date" name="date" value="{{ now()->format('Y-m-d') }}" class="form-control"></div></div>
                    <div class="mt-2"><label class="form-label">Description</label><input name="description" class="form-control" placeholder="e.g. Cash deposit"></div>
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Save transfer</button></div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="accountModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('accounts.store') }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title">New account</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
                    <div class="mb-2"><label class="form-label">Type</label><select name="type" class="form-select"><option value="bank">Bank</option><option value="cash">Cash</option><option value="card">Card</option></select></div>
                    <div class="row g-2"><div class="col-6"><label class="form-label">Bank</label><input name="bank_name" class="form-control"></div><div class="col-6"><label class="form-label">Account no</label><input name="account_no" class="form-control"></div></div>
                    <div class="mt-2"><label class="form-label">Opening balance</label><input type="number" step="0.01" name="opening_balance" value="0" class="form-control"></div>
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Add account</button></div>
            </form>
        </div>
    </div>
@endsection

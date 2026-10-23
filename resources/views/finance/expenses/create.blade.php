@extends('layouts.vertical', ['subtitle' => 'New Expense'])

@section('content')
    <x-page-header title="New Expense" :crumbs="['Expenses' => route('expenses.index'), 'New']" />
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <form method="POST" action="{{ route('expenses.store') }}" class="card">
                @csrf
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6"><label class="form-label">Date</label><input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" class="form-control" required></div>
                        <div class="col-6"><label class="form-label">Amount (Rs)</label><input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="form-control" required inputmode="decimal"></div>
                        <div class="col-md-6"><label class="form-label">Category</label><select name="expense_category_id" class="form-select">@foreach ($categories as $c)<option value="{{ $c->id }}" @selected(old('expense_category_id') == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
                        <div class="col-md-6"><label class="form-label">Paid from</label><select name="account_id" class="form-select">@foreach ($accounts as $a)<option value="{{ $a->id }}" @selected(old('account_id') == $a->id)>{{ $a->name }}</option>@endforeach</select></div>
                        <div class="col-12"><label class="form-label">Paid to</label><input name="payee" value="{{ old('payee') }}" class="form-control"></div>
                        <div class="col-12"><label class="form-label">Description</label><input name="description" value="{{ old('description') }}" class="form-control"></div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2 sticky-actions">
                    <a href="{{ route('expenses.index') }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Save expense</button>
                </div>
            </form>
        </div>
    </div>
@endsection

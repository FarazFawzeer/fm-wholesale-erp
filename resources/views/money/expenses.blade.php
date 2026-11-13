@extends('layouts.vertical', ['subtitle' => 'Expenses'])

@section('content')
    <x-page-header title="Expenses" />

    <div class="row g-3">
        <div class="col-lg-4">
            <form method="POST" action="{{ route('expenses.store') }}" class="card">
                @csrf
                <div class="card-header"><h5 class="card-title mb-0"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>Add expense</h5></div>
                <div class="card-body">
                    <div class="mb-2"><label class="form-label small">Amount (Rs) *</label><input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" class="form-control form-control-lg" required inputmode="decimal"></div>
                    <div class="mb-2 pick-or-add {{ old('new_category') ? 'adding' : '' }}"><label class="form-label small">For *</label>
                        <div class="input-group js-pick">
                            <select name="expense_category_id" class="form-select"><option value="">Choose...</option>
                                @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(old('expense_category_id') == $c->id)>{{ $c->name }}</option>@endforeach</select>
                            <button type="button" class="btn btn-soft-primary js-add-new">+ New</button></div>
                        <div class="input-group js-new"><input name="new_category" value="{{ old('new_category') }}" class="form-control" placeholder="e.g. Rent"><button type="button" class="btn btn-light js-cancel-new">Cancel</button></div></div>
                    <div class="mb-2"><label class="form-label small">Paid by</label>
                        <div class="method-picker method-radio">
                            @foreach (\App\Models\Payment::METHODS as $key => $label)
                                <label class="method-btn"><input type="radio" name="method" value="{{ $key }}" @checked(old('method', 'cash') === $key)><iconify-icon icon="{{ \App\Models\Payment::ICONS[$key] }}"></iconify-icon><span>{{ $label }}</span></label>
                            @endforeach
                        </div></div>
                    <div class="row g-2 mb-2">
                        <div class="col-6"><label class="form-label small">Date</label><input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" class="form-control" required></div>
                        <div class="col-6"><label class="form-label small">Paid to</label><input name="paid_to" value="{{ old('paid_to') }}" class="form-control" placeholder="Optional"></div>
                    </div>
                    <div class="mb-3"><label class="form-label small">Note</label><input name="note" value="{{ old('note') }}" class="form-control" placeholder="Optional"></div>
                    <button class="btn btn-primary w-100">Save expense</button>
                </div>
            </form>
        </div>

        <div class="col-lg-8">
            <form class="filter-bar mb-3" method="GET">
                <input type="date" name="from" value="{{ $from }}" class="form-control" title="From">
                <input type="date" name="to" value="{{ $to }}" class="form-control" title="To">
                <select name="category" class="form-select"><option value="">All types</option>@foreach ($categories as $c)<option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>@endforeach</select>
                <button class="btn btn-soft-primary">Show</button>
            </form>

            <div class="card">
                <div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0">Total</h5><strong class="fs-5 money">{{ money($total) }}</strong></div>
                @if ($byCategory->count() > 1)
                    <div class="card-body py-2 d-flex flex-wrap gap-2">
                        @foreach ($byCategory as $row)<span class="badge bg-light text-dark border fw-normal py-2">{{ $row->category?->name }}: <b>{{ money($row->total) }}</b></span>@endforeach
                    </div>
                @endif
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Date</th><th>For</th><th>Paid to / note</th><th>By</th><th class="text-end">Amount</th><th></th></tr></thead>
                            <tbody>
                                @forelse ($expenses as $e)
                                    <tr>
                                        <td class="td-title">{{ $e->date->format('d M Y') }} &middot; {{ $e->category->name }}</td>
                                        <td data-label="For" class="d-none d-md-table-cell">{{ $e->category->name }}</td>
                                        <td data-label="Note">{{ trim($e->paid_to . ' ' . $e->note) ?: '-' }}</td>
                                        <td data-label="By">{{ \App\Models\Payment::METHODS[$e->method] ?? $e->method }}</td>
                                        <td data-label="Amount" class="text-end money fw-semibold">{{ money($e->amount) }}</td>
                                        <td class="td-actions text-end">
                                            @if (auth()->user()->isManager() || $e->user_id === auth()->id())
                                                <form method="POST" action="{{ route('expenses.destroy', $e) }}" data-confirm="Delete this expense?">@csrf @method('DELETE')<button class="btn btn-sm btn-link text-danger p-0" title="Delete"><iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon></button></form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-empty :cols="6" text="No expenses in this period." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{ $expenses->links() }}
        </div>
    </div>
@endsection

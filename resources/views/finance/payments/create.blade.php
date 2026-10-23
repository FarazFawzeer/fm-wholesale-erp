@php $in = $direction === 'in'; $label = $in ? 'Receive Payment' : 'Pay Supplier'; $param = $in ? 'customer_id' : 'supplier_id'; @endphp
@extends('layouts.vertical', ['subtitle' => $label])

@section('content')
    <x-page-header :title="$label" :crumbs="[($in ? 'Receipts' : 'Supplier Payments') => route($in ? 'receipts.index' : 'supplier-payments.index'), 'New']" />

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">{{ $in ? 'Customer' : 'Supplier' }}</label>
                    <select name="{{ $param }}" class="form-select" onchange="this.form.submit()">
                        <option value="">Select {{ $in ? 'customer' : 'supplier' }}</option>
                        @foreach ($parties as $p)<option value="{{ $p->id }}" @selected($party?->id == $p->id)>{{ $p->name }}</option>@endforeach
                    </select>
                </div>
                @if ($party)
                    <div class="col-md-6 small">
                        <span class="text-muted">{{ $in ? 'Outstanding' : 'We owe' }}:</span> <strong class="money">{{ money($party->balance()) }}</strong>
                        @if ($in && $party->overdueAmount() > 0) &middot; <span class="text-danger">overdue {{ money($party->overdueAmount()) }}</span>@endif
                    </div>
                @endif
            </form>
        </div>
    </div>

    @if ($party)
        <form method="POST" action="{{ route('payments.store') }}">
            @csrf
            <input type="hidden" name="direction" value="{{ $direction }}">
            <input type="hidden" name="party_id" value="{{ $party->id }}">
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Payment methods</h5></div>
                        <div class="card-body" data-payment-lines data-accounts='@json($accounts)' data-start-empty="0" data-target="{{ $focusInvoice ? $documents->firstWhere('id', $focusInvoice)?->balance() : '' }}">
                            <div class="row g-2 mb-3">
                                <div class="col-6"><label class="form-label">Date</label><input type="date" name="date" value="{{ now()->format('Y-m-d') }}" class="form-control"></div>
                            </div>
                            <div class="pay-lines"></div>
                            <button type="button" class="btn btn-sm btn-light js-add-pay"><iconify-icon icon="solar:add-circle-outline" class="align-middle"></iconify-icon> Add another method (split)</button>
                            <div class="totals-box mt-3"><div class="row-total grand"><span>Total {{ $in ? 'received' : 'paid' }}</span><span class="js-paid money">0.00</span></div></div>
                            <div class="mt-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card" data-allocations>
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Apply to {{ $in ? 'invoices' : 'purchase orders' }}</h5>
                            <div class="form-check form-switch mb-0"><input class="form-check-input js-auto" type="checkbox" id="auto" {{ $focusInvoice ? '' : 'checked' }}><label class="form-check-label small" for="auto">Oldest first</label></div>
                        </div>
                        <div class="card-body table-wrap p-0">
                            <div class="table-responsive">
                                <table class="table erp-table stack mb-0">
                                    <thead><tr><th>Document</th><th>Due</th><th class="text-end">Balance</th><th class="text-end">Apply</th></tr></thead>
                                    <tbody>
                                        @forelse ($documents as $d)
                                            @php $bal = $in ? $d->balance() : $d->balance(); $overdue = $d->due_date && $d->due_date->isPast(); @endphp
                                            <tr>
                                                <td class="td-title"><strong>{{ $d->number }}</strong> <span class="small text-muted">{{ ($in ? $d->date : $d->order_date)->format('d M Y') }}</span></td>
                                                <td data-label="Due" class="{{ $overdue ? 'text-danger fw-semibold' : '' }}">{{ $d->due_date?->format('d M Y') ?? '-' }}</td>
                                                <td data-label="Balance" class="text-end money">{{ money($bal) }}</td>
                                                <td data-label="Apply" class="text-end"><input type="number" step="0.01" min="0" max="{{ $bal }}" name="alloc[{{ $d->id }}]" data-due="{{ $bal }}" value="{{ $focusInvoice == $d->id ? $bal : '' }}" class="form-control form-control-sm text-end js-alloc ms-auto" style="max-width: 140px"></td>
                                            </tr>
                                        @empty
                                            <x-empty :cols="4" text="Nothing outstanding. The amount will be kept as an advance." />
                                        @endforelse
                                    </tbody>
                                    <tfoot class="table-light"><tr><td colspan="3" class="text-end">Applied</td><td class="text-end money fw-semibold js-alloc-total">0.00</td></tr></tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer small text-muted">Any amount not applied stays on the account as an advance.</div>
                    </div>
                </div>
            </div>
            <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
                <a href="{{ url()->previous() }}" class="btn btn-light">Cancel</a>
                <button class="btn btn-success px-4">{{ $in ? 'Save receipt' : 'Save payment' }}</button>
            </div>
        </form>
    @endif
@endsection

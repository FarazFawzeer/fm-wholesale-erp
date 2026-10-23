@extends('layouts.vertical', ['subtitle' => 'Supplier Obligations'])

@section('content')
    <x-page-header title="Supplier Payments & Obligations" :crumbs="['Credit Control', 'Supplier obligations']">
        <a href="{{ route('supplier-payments.create') }}" class="btn btn-primary">Pay Supplier</a>
    </x-page-header>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-4"><x-kpi label="Owed to suppliers" :value="money($orders->sum(fn ($o) => $o->balance()))" icon="solar:wallet-money-outline" color="danger" /></div>
        <div class="col-6 col-lg-4"><x-kpi label="Issued cheques pending" :value="money($cheques->sum('amount'))" icon="solar:card-send-outline" color="warning" :sub="$cheques->count() . ' cheques'" /></div>
        <div class="col-12 col-lg-4"><x-kpi label="Shipments not yet received" :value="money($inTransit->sum('landed_total'))" icon="solar:globus-outline" color="info" :sub="$inTransit->count() . ' orders (landed estimate)'" /></div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Supplier balances by due date</h5></div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>PO</th><th>Supplier</th><th>Due</th><th class="text-end">Payable</th><th class="text-end">Paid</th><th class="text-end">Balance</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($orders as $o)
                            <tr>
                                <td class="td-title"><a href="{{ route('purchase-orders.show', $o) }}" class="doc-link">{{ $o->number }}</a></td>
                                <td data-label="Supplier">{{ $o->supplier->name }}</td>
                                <td data-label="Due" class="{{ $o->due_date?->isPast() ? 'text-danger fw-semibold' : ($o->due_date?->lte(now()->addDays(7)) ? 'text-warning fw-semibold' : '') }}">{{ $o->due_date?->format('d M Y') ?? '-' }}</td>
                                <td data-label="Payable" class="text-end money">{{ money($o->payable_amount) }}</td>
                                <td data-label="Paid" class="text-end money">{{ money($o->paid_amount) }}</td>
                                <td data-label="Balance" class="text-end money fw-semibold">{{ money($o->balance()) }}</td>
                                <td class="td-actions text-end"><a href="{{ route('supplier-payments.create', ['supplier_id' => $o->supplier_id]) }}" class="btn btn-sm btn-soft-primary">Pay</a></td>
                            </tr>
                        @empty
                            <x-empty :cols="7" text="Nothing owed." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Issued cheques - keep funds ready</h5></div>
        <ul class="list-group list-group-flush">
            @forelse ($cheques as $ch)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>#{{ $ch->cheque_no }} &middot; {{ $ch->party()?->name }}<br><span class="small {{ $ch->cheque_date->lte(now()->addDays(3)) ? 'text-danger' : 'text-muted' }}">{{ $ch->cheque_date->format('d M Y') }} ({{ $ch->cheque_date->diffForHumans() }})</span></span>
                    <strong class="money">{{ money($ch->amount) }}</strong>
                </li>
            @empty
                <li class="list-group-item text-muted small">No issued cheques pending.</li>
            @endforelse
        </ul>
    </div>
@endsection

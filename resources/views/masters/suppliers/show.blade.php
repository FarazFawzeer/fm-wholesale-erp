@extends('layouts.vertical', ['subtitle' => $supplier->name])

@section('content')
    <x-page-header :title="$supplier->name" :crumbs="['Suppliers' => route('suppliers.index'), $supplier->code]">
        <a href="{{ route('supplier-payments.create', ['supplier_id' => $supplier->id]) }}" class="btn btn-soft-success">Pay Supplier</a>
        <a href="{{ route('purchase-orders.create', ['supplier_id' => $supplier->id]) }}" class="btn btn-soft-primary">New PO</a>
        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-light">Edit</a>
    </x-page-header>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><x-kpi label="We owe" :value="money($supplier->balance())" icon="solar:wallet-money-outline" color="danger" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Pending cheques" :value="money($cheques->whereIn('status', ['pending'])->sum('amount'))" icon="solar:card-send-outline" color="warning" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Purchase orders" :value="$orders->count()" icon="solar:globus-outline" color="info" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Terms" :value="$supplier->payment_terms_days . ' days'" icon="solar:calendar-outline" color="secondary" :sub="$supplier->currency . ' &middot; ' . $supplier->country" /></div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Purchase orders</h5></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>PO</th><th>Date</th><th>Status</th><th class="text-end">Landed total</th><th class="text-end">Payable</th><th class="text-end">Balance</th></tr></thead>
                            <tbody>
                                @forelse ($orders as $po)
                                    <tr>
                                        <td class="td-title"><a href="{{ route('purchase-orders.show', $po) }}" class="doc-link">{{ $po->number }}</a></td>
                                        <td data-label="Date">{{ $po->order_date->format('d M Y') }}</td>
                                        <td data-label="Status">{!! status_badge($po->status) !!}</td>
                                        <td data-label="Landed" class="text-end money">{{ money($po->landed_total) }}</td>
                                        <td data-label="Payable" class="text-end money">{{ money($po->payable_amount) }}</td>
                                        <td data-label="Balance" class="text-end money fw-semibold">{{ money($po->balance()) }}</td>
                                    </tr>
                                @empty
                                    <x-empty :cols="6" text="No orders yet." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Contact</h5></div>
                <div class="card-body info-list">
                    <div><div class="label">Contact</div><div class="value">{{ $supplier->contact_person ?: '-' }}</div></div>
                    <div><div class="label">Phone</div><div class="value">{{ $supplier->phone ?: '-' }}</div></div>
                    <div><div class="label">Email</div><div class="value text-break">{{ $supplier->email ?: '-' }}</div></div>
                    <div><div class="label">Address</div><div class="value">{{ $supplier->address ?: '-' }}</div></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Payments</h5></div>
                <ul class="list-group list-group-flush">
                    @forelse ($payments as $p)
                        <li class="list-group-item d-flex justify-content-between">
                            <span><a href="{{ route('payments.show', $p) }}">{{ $p->number }}</a><br><span class="small text-muted">{{ $p->date->format('d M Y') }} &middot; {{ $p->methodSummary() }}</span></span>
                            <strong class="money">{{ money($p->total) }}</strong>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">No payments yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection

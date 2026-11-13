@extends('layouts.vertical', ['subtitle' => $supplier->name])

@section('content')
    <x-page-header :title="$supplier->name" :crumbs="['Suppliers' => route('suppliers.index'), $supplier->name]">
        <a href="{{ route('purchases.create', ['supplier' => $supplier->id]) }}" class="btn btn-primary"><iconify-icon icon="solar:cart-plus-outline" class="align-middle me-1"></iconify-icon>New purchase</a>
        @if ($due > 0.009)
            <a href="{{ route('payments.pay', ['supplier' => $supplier->id]) }}" class="btn btn-warning"><iconify-icon icon="solar:card-send-outline" class="align-middle me-1"></iconify-icon>Pay supplier</a>
        @endif
        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-soft-secondary"><iconify-icon icon="solar:pen-outline" class="align-middle"></iconify-icon></a>
    </x-page-header>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="info-list">
                        <div><div class="label">Contact</div><div class="value">{{ $supplier->contact_person ?: '-' }}</div></div>
                        <div><div class="label">Phone</div><div class="value">@if ($supplier->phone)<a href="tel:{{ $supplier->phone }}">{{ $supplier->phone }}</a>@else-@endif</div></div>
                        <div><div class="label">We owe now</div><div class="value money {{ $due > 0.009 ? 'text-warning' : 'text-success' }}">{{ money(max(0, $due)) }}</div></div>
                        @if ($openingDue > 0)<div><div class="label">Old balance unpaid</div><div class="value money">{{ money($openingDue) }}</div></div>@endif
                    </div>
                    @if ($supplier->address)<div class="small text-muted mt-3">{{ $supplier->address }}</div>@endif
                    @if ($supplier->notes)<div class="small mt-2">{{ $supplier->notes }}</div>@endif
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Purchases</h5></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>No</th><th>Date</th><th>Supplier bill</th><th class="text-end">Total</th><th class="text-end">Balance</th></tr></thead>
                            <tbody>
                                @forelse ($purchases as $pu)
                                    <tr>
                                        <td class="td-title"><a href="{{ route('purchases.show', $pu) }}" class="doc-link">{{ $pu->number }}</a></td>
                                        <td data-label="Date">{{ $pu->date->format('d M Y') }}</td>
                                        <td data-label="Supplier bill">{{ $pu->supplier_bill_no ?: '-' }}</td>
                                        <td data-label="Total" class="text-end money">{{ money($pu->total - $pu->returned_amount) }}</td>
                                        <td data-label="Balance" class="text-end money {{ $pu->balance() > 0 ? 'text-warning fw-semibold' : '' }}">{{ money($pu->balance()) }}</td>
                                    </tr>
                                @empty
                                    <x-empty :cols="5" text="No purchases yet." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Payments made</h5></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>No</th><th>Date</th><th>Method</th><th class="text-end">Amount</th></tr></thead>
                            <tbody>
                                @forelse ($payments as $p)
                                    <tr>
                                        <td class="td-title"><a href="{{ route('payments.show', $p) }}" class="doc-link">{{ $p->number }}</a></td>
                                        <td data-label="Date">{{ $p->date->format('d M Y') }}</td>
                                        <td data-label="Method">{{ $p->methodSummary() }}</td>
                                        <td data-label="Amount" class="text-end money fw-semibold">{{ money($p->total) }}</td>
                                    </tr>
                                @empty
                                    <x-empty :cols="4" text="No payments yet." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

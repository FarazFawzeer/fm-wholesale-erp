@extends('layouts.vertical', ['subtitle' => 'Purchases'])

@section('content')
    <x-page-header title="Purchases">
        <a href="{{ route('purchases.create') }}" class="btn btn-primary"><iconify-icon icon="solar:cart-plus-outline" class="align-middle me-1"></iconify-icon>New purchase</a>
    </x-page-header>

    <form class="filter-bar mb-3" method="GET">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control filter-grow" placeholder="Search supplier or bill no...">
        <input type="date" name="from" value="{{ request('from') }}" class="form-control" title="From">
        <input type="date" name="to" value="{{ request('to') }}" class="form-control" title="To">
        <select name="show" class="form-select">
            <option value="">All</option>
            <option value="unpaid" @selected(request('show') === 'unpaid')>Not fully paid</option>
        </select>
        <button class="btn btn-soft-primary">Show</button>
    </form>

    <div class="row g-3 mb-3">
        <div class="col-6 col-md-4"><x-kpi label="Bought (this list)" :value="money($sum->total ?? 0)" icon="solar:cart-large-2-outline" color="primary" /></div>
        <div class="col-6 col-md-4"><x-kpi label="Still to pay" :value="money($sum->due ?? 0)" icon="solar:card-send-outline" color="warning" /></div>
    </div>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack table-hover mb-0">
                    <thead><tr><th>No</th><th>Date</th><th>Supplier</th><th class="text-end">Total</th><th class="text-end">Balance</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($purchases as $pu)
                            <tr>
                                <td class="td-title"><a href="{{ route('purchases.show', $pu) }}" class="doc-link">{{ $pu->number }}</a>@if ($pu->supplier_bill_no) <small class="text-muted">({{ $pu->supplier_bill_no }})</small>@endif</td>
                                <td data-label="Date">{{ $pu->date->format('d M Y') }}</td>
                                <td data-label="Supplier">{{ $pu->supplier->name }}</td>
                                <td data-label="Total" class="text-end money">{{ money($pu->total - $pu->returned_amount) }}</td>
                                <td data-label="Balance" class="text-end money {{ $pu->balance() > 0 ? 'text-warning fw-semibold' : '' }}">{{ money($pu->balance()) }}</td>
                                <td data-label="Status">{!! status_badge($pu->status()) !!}</td>
                            </tr>
                        @empty
                            <x-empty :cols="6" text="No purchases yet." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{ $purchases->links() }}
@endsection

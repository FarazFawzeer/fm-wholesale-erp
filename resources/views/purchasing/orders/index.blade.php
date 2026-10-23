@extends('layouts.vertical', ['subtitle' => 'Purchase Orders'])

@section('content')
    <x-page-header title="Purchase Orders & Imports" :crumbs="['Import & Purchasing', 'Purchase Orders']">
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>New Purchase Order</a>
    </x-page-header>

    <ul class="nav nav-pills nav-tabs-scroll mb-3 gap-1">
        @foreach (['' => 'All', 'draft' => 'Draft', 'ordered' => 'Ordered', 'costed' => 'Costed / in transit', 'partial' => 'Partly received', 'received' => 'Received'] as $key => $label)
            <li class="nav-item"><a class="nav-link {{ request('status', '') === $key ? 'active' : '' }}" href="{{ route('purchase-orders.index', array_filter(['status' => $key])) }}">{{ $label }}
                @if ($key && ($counts[$key] ?? 0))<span class="badge bg-light text-dark ms-1">{{ $counts[$key] }}</span>@endif</a></li>
        @endforeach
    </ul>

    <div class="card">
        <div class="card-body border-bottom">
            <form class="filter-bar">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control filter-grow" placeholder="PO number or container / shipment ref">
                <select name="supplier_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All suppliers</option>
                    @foreach ($suppliers as $s)<option value="{{ $s->id }}" @selected(request('supplier_id') == $s->id)>{{ $s->name }}</option>@endforeach
                </select>
                <button class="btn btn-light">Search</button>
            </form>
        </div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack mb-0">
                    <thead><tr><th>PO</th><th>Supplier</th><th>Order date</th><th>Shipment</th><th class="text-end">Pairs</th><th class="text-end">Supplier value</th><th class="text-end">Landed total</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($orders as $po)
                            <tr>
                                <td class="td-title"><a href="{{ route('purchase-orders.show', $po) }}" class="doc-link">{{ $po->number }}</a></td>
                                <td data-label="Supplier">{{ $po->supplier->name }}</td>
                                <td data-label="Order date">{{ $po->order_date->format('d M Y') }}</td>
                                <td data-label="Shipment">{{ $po->shipment_ref ?: '-' }}</td>
                                <td data-label="Pairs" class="text-end">{{ qty($po->received) }} / {{ qty($po->pairs) }}</td>
                                <td data-label="Supplier value" class="text-end money">{{ $po->currency }} {{ number_format($po->subtotal_fx, 2) }}</td>
                                <td data-label="Landed" class="text-end money fw-semibold">{{ money($po->landed_total) }}</td>
                                <td data-label="Status">{!! status_badge($po->status) !!}</td>
                            </tr>
                        @empty
                            <x-empty :cols="8" text="No purchase orders found." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($orders->hasPages())<div class="card-footer">{{ $orders->links() }}</div>@endif
    </div>
@endsection

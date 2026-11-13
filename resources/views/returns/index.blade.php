@extends('layouts.vertical', ['subtitle' => 'Returns'])

@section('content')
    <x-page-header title="Returns">
        <a href="{{ route('returns.create') }}" class="btn btn-primary"><iconify-icon icon="solar:undo-left-round-outline" class="align-middle me-1"></iconify-icon>Customer return</a>
        <a href="{{ route('returns.create', ['type' => 'supplier']) }}" class="btn btn-light"><iconify-icon icon="solar:delivery-outline" class="align-middle me-1"></iconify-icon>Return to supplier</a>
    </x-page-header>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <a href="{{ route('returns.index', ['type' => 'customer']) }}" class="card ret-sum mb-0 {{ $type === 'customer' ? 'active' : '' }}">
                <span class="ret-icon in"><iconify-icon icon="solar:box-minimalistic-outline"></iconify-icon></span>
                <div class="flex-grow-1 min-w-0">
                    <div class="ret-label">Back from customers</div>
                    <div class="ret-value">{{ money($fromCustomers->amount) }}</div>
                    <div class="small text-muted">{{ $fromCustomers->count }} {{ Str::plural('return', $fromCustomers->count) }} &middot; {{ $fromCustomers->pairs }} pairs back in stock</div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('returns.index', ['type' => 'supplier']) }}" class="card ret-sum mb-0 {{ $type === 'supplier' ? 'active' : '' }}">
                <span class="ret-icon out"><iconify-icon icon="solar:delivery-outline"></iconify-icon></span>
                <div class="flex-grow-1 min-w-0">
                    <div class="ret-label">Sent back to suppliers</div>
                    <div class="ret-value">{{ money($toSuppliers->amount) }}</div>
                    <div class="small text-muted">{{ $toSuppliers->count }} {{ Str::plural('return', $toSuppliers->count) }} &middot; {{ $toSuppliers->pairs }} pairs out of stock</div>
                </div>
            </a>
        </div>
    </div>

    <ul class="nav nav-pills nav-scroll mb-3">
        <li class="nav-item"><a class="nav-link {{ ! $type ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['type' => null, 'page' => null]) }}">All</a></li>
        <li class="nav-item"><a class="nav-link {{ $type === 'customer' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['type' => 'customer', 'page' => null]) }}">From customers</a></li>
        <li class="nav-item"><a class="nav-link {{ $type === 'supplier' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['type' => 'supplier', 'page' => null]) }}">To suppliers</a></li>
    </ul>

    <form class="filter-bar mb-3" method="GET">
        @if ($type)<input type="hidden" name="type" value="{{ $type }}">@endif
        <input type="search" name="q" value="{{ request('q') }}" class="form-control filter-grow" placeholder="Search return no, bill no, name or reason...">
        <input type="date" name="from" value="{{ request('from') }}" class="form-control" title="From">
        <input type="date" name="to" value="{{ request('to') }}" class="form-control" title="To">
        <button class="btn btn-soft-primary">Show</button>
    </form>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack table-hover mb-0 returns-table">
                    <thead><tr><th>Return</th><th>Date</th><th>Customer / Supplier</th><th>Bill</th><th>Reason</th><th class="text-end">Pairs</th><th class="text-end">Amount</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($returns as $r)
                            @php
                                $in = $r->type === 'customer';
                                $doc = $in ? $r->invoice : $r->purchase;
                            @endphp
                            <tr>
                                <td class="td-title">
                                    <a href="{{ route('returns.show', $r) }}" class="doc-link">{{ $r->number }}</a>
                                    <span class="ret-type {{ $in ? 'in' : 'out' }}">{{ $in ? 'From customer' : 'To supplier' }}</span>
                                </td>
                                <td data-label="Date" class="text-nowrap">{{ $r->date->format('d M Y') }}</td>
                                <td data-label="{{ $in ? 'Customer' : 'Supplier' }}" class="fw-semibold">{{ $r->partyName() }}</td>
                                <td data-label="Bill" class="text-nowrap"><a href="{{ $in ? route('invoices.show', $doc) : route('purchases.show', $doc) }}" class="mono small">{{ $doc?->number }}</a></td>
                                <td data-label="Reason" class="text-muted">{{ $r->reason ?: '-' }}</td>
                                <td data-label="Pairs" class="text-end">{{ (int) $r->pairs }}</td>
                                <td data-label="Amount" class="text-end money fw-semibold">{{ money($r->total) }}</td>
                                <td class="td-actions text-end">
                                    <div class="row-actions">
                                        <a href="{{ route('returns.show', $r) }}" class="btn btn-sm btn-light"><iconify-icon icon="solar:eye-outline"></iconify-icon><span>Details</span></a>
                                        <a href="{{ route('returns.print', $r) }}" class="btn btn-sm btn-dark"><iconify-icon icon="solar:document-text-outline"></iconify-icon><span>Note</span></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-empty :cols="8" text="No returns yet." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{ $returns->links() }}
@endsection

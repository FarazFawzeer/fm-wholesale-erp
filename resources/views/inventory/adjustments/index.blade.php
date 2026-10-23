@extends('layouts.vertical', ['subtitle' => 'Stock Adjustments'])

@section('content')
    <x-page-header title="Adjustments, Damages & Returns to Supplier" :crumbs="['Inventory', 'Adjustments']">
        <a href="{{ route('stock-adjustments.create') }}" class="btn btn-primary"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>New Adjustment</a>
    </x-page-header>
    <ul class="nav nav-pills nav-tabs-scroll mb-3 gap-1">
        <li class="nav-item"><a class="nav-link {{ ! request('reason') ? 'active' : '' }}" href="{{ route('stock-adjustments.index') }}">All</a></li>
        @foreach (\App\Models\StockAdjustment::REASONS as $k => $l)
            <li class="nav-item"><a class="nav-link {{ request('reason') === $k ? 'active' : '' }}" href="{{ route('stock-adjustments.index', ['reason' => $k]) }}">{{ $l }}</a></li>
        @endforeach
    </ul>
    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack mb-0">
                    <thead><tr><th>Number</th><th>Date</th><th>Reason</th><th>Store</th><th class="text-end">Pairs</th><th class="text-end">Value</th><th>Notes</th></tr></thead>
                    <tbody>
                        @forelse ($adjustments as $a)
                            <tr>
                                <td class="td-title"><a href="{{ route('stock-adjustments.show', $a) }}" class="doc-link">{{ $a->number }}</a></td>
                                <td data-label="Date">{{ $a->date->format('d M Y') }}</td>
                                <td data-label="Reason">{{ \App\Models\StockAdjustment::REASONS[$a->reason] ?? $a->reason }}</td>
                                <td data-label="Store">{{ $a->warehouse->code }}</td>
                                <td data-label="Pairs" class="text-end fw-semibold {{ $a->pairs < 0 ? 'text-danger' : 'text-success' }}">{{ $a->pairs }}</td>
                                <td data-label="Value" class="text-end money">{{ money($a->value) }}</td>
                                <td data-label="Notes">{{ $a->notes }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="7" text="No adjustments yet." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($adjustments->hasPages())<div class="card-footer">{{ $adjustments->links() }}</div>@endif
    </div>
@endsection

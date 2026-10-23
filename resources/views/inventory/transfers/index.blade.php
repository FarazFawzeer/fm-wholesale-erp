@extends('layouts.vertical', ['subtitle' => 'Stock Transfers'])

@section('content')
    <x-page-header title="Stock Transfers" :crumbs="['Inventory', 'Transfers']">
        <a href="{{ route('stock-transfers.create') }}" class="btn btn-primary"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>New Transfer</a>
    </x-page-header>
    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack mb-0">
                    <thead><tr><th>Transfer</th><th>Date</th><th>From</th><th>To</th><th class="text-end">Pairs</th><th>By</th></tr></thead>
                    <tbody>
                        @forelse ($transfers as $t)
                            <tr>
                                <td class="td-title"><a href="{{ route('stock-transfers.show', $t) }}" class="doc-link">{{ $t->number }}</a></td>
                                <td data-label="Date">{{ $t->date->format('d M Y') }}</td>
                                <td data-label="From">{{ $t->from->name }}</td>
                                <td data-label="To">{{ $t->to->name }}</td>
                                <td data-label="Pairs" class="text-end fw-semibold">{{ qty($t->pairs) }}</td>
                                <td data-label="By">{{ $t->user?->name }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="6" text="No transfers yet." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($transfers->hasPages())<div class="card-footer">{{ $transfers->links() }}</div>@endif
    </div>
@endsection

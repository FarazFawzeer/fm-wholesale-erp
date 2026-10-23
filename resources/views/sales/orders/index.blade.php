@php $label = $type === 'quotation' ? 'Quotations' : 'Sales Orders'; $route = $type === 'quotation' ? 'quotations' : 'sales-orders'; @endphp
@extends('layouts.vertical', ['subtitle' => $label])

@section('content')
    <x-page-header :title="$label" :crumbs="['Sales', $label]">
        <a href="{{ route($route . '.create') }}" class="btn btn-primary"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>New {{ $type === 'quotation' ? 'Quotation' : 'Sales Order' }}</a>
    </x-page-header>

    <ul class="nav nav-pills nav-tabs-scroll mb-3 gap-1">
        @foreach (['' => 'All', 'open' => 'Open', 'accepted' => 'Accepted', 'converted' => 'Converted', 'invoiced' => 'Invoiced', 'rejected' => 'Rejected'] as $k => $l)
            <li class="nav-item"><a class="nav-link {{ request('status', '') === $k ? 'active' : '' }}" href="{{ route($route . '.index', array_filter(['status' => $k])) }}">{{ $l }}</a></li>
        @endforeach
    </ul>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack mb-0">
                    <thead><tr><th>Number</th><th>Customer</th><th>Date</th>@if ($type === 'quotation')<th>Valid until</th>@endif<th>Status</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                        @forelse ($docs as $d)
                            <tr>
                                <td class="td-title"><a href="{{ route('sales-docs.show', $d) }}" class="doc-link">{{ $d->number }}</a></td>
                                <td data-label="Customer">{{ $d->customer->name }}</td>
                                <td data-label="Date">{{ $d->date->format('d M Y') }}</td>
                                @if ($type === 'quotation')<td data-label="Valid until" class="{{ $d->valid_until?->isPast() && $d->status === 'open' ? 'text-danger' : '' }}">{{ $d->valid_until?->format('d M Y') }}</td>@endif
                                <td data-label="Status">{!! status_badge($d->status) !!}</td>
                                <td data-label="Total" class="text-end money fw-semibold">{{ money($d->total) }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="6" text="Nothing here yet." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($docs->hasPages())<div class="card-footer">{{ $docs->links() }}</div>@endif
    </div>
@endsection

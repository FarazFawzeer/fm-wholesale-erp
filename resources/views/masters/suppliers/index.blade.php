@extends('layouts.vertical', ['subtitle' => 'Suppliers'])

@section('content')
    <x-page-header title="Suppliers" :crumbs="['Import & Purchasing', 'Suppliers']">
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>New Supplier</a>
    </x-page-header>

    <div class="card">
        <div class="card-body border-bottom">
            <form class="filter-bar"><input type="search" name="q" value="{{ request('q') }}" class="form-control filter-grow" placeholder="Search supplier, code or country"><button class="btn btn-light">Search</button></form>
        </div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack">
                    <thead><tr><th>Supplier</th><th>Country</th><th>Contact</th><th>Currency</th><th>Terms</th><th class="text-end">POs</th><th class="text-end">We owe</th></tr></thead>
                    <tbody>
                        @forelse ($suppliers as $s)
                            <tr>
                                <td class="td-title"><a href="{{ route('suppliers.show', $s) }}" class="doc-link">{{ $s->name }}</a><div class="small text-muted">{{ $s->code }}</div></td>
                                <td data-label="Country">{{ $s->country }}</td>
                                <td data-label="Contact">{{ $s->contact_person }}<div class="small text-muted">{{ $s->phone }}</div></td>
                                <td data-label="Currency">{{ $s->currency }}</td>
                                <td data-label="Terms">{{ $s->payment_terms_days }} days</td>
                                <td data-label="POs" class="text-end">{{ $s->purchase_orders_count }}</td>
                                <td data-label="We owe" class="text-end money fw-semibold">{{ money($s->balance()) }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="7" text="No suppliers yet." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($suppliers->hasPages())<div class="card-footer">{{ $suppliers->links() }}</div>@endif
    </div>
@endsection

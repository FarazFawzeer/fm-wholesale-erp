@extends('layouts.vertical', ['subtitle' => 'Suppliers'])

@section('content')
    <x-page-header title="Suppliers">
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary"><iconify-icon icon="solar:user-plus-outline" class="align-middle me-1"></iconify-icon>Add supplier</a>
    </x-page-header>

    <div class="row g-3 mb-3">
        <div class="col-6 col-md-4"><x-kpi label="We owe suppliers" :value="money($totalDue)" icon="solar:card-send-outline" color="warning" :href="route('suppliers.index', ['show' => 'owe'])" /></div>
    </div>

    <form class="filter-bar mb-3" method="GET">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control filter-grow" placeholder="Search name or phone...">
        <select name="show" class="form-select" onchange="this.form.submit()">
            <option value="">All suppliers</option>
            <option value="owe" @selected(request('show') === 'owe')>Only who we owe</option>
        </select>
    </form>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack table-hover mb-0">
                    <thead><tr><th>Supplier</th><th>Contact</th><th>Phone</th><th class="text-end">We owe</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($suppliers as $s)
                            <tr>
                                <td class="td-title"><a href="{{ route('suppliers.show', $s) }}" class="fw-semibold">{{ $s->name }}</a></td>
                                <td data-label="Contact">{{ $s->contact_person ?: '-' }}</td>
                                <td data-label="Phone">@if ($s->phone)<a href="tel:{{ $s->phone }}">{{ $s->phone }}</a>@else-@endif</td>
                                <td data-label="We owe" class="text-end money fw-semibold {{ $s->due > 0.009 ? 'text-warning' : 'text-muted' }}">{{ $s->due > 0.009 ? money($s->due) : 'Nothing' }}</td>
                                <td class="td-actions text-end">
                                    <a href="{{ route('purchases.create', ['supplier' => $s->id]) }}" class="btn btn-sm btn-soft-primary">Buy</a>
                                    @if ($s->due > 0.009)<a href="{{ route('payments.pay', ['supplier' => $s->id]) }}" class="btn btn-sm btn-soft-warning">Pay</a>@endif
                                </td>
                            </tr>
                        @empty
                            <x-empty :cols="5" text="No suppliers found." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{ $suppliers->links() }}
@endsection

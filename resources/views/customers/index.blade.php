@extends('layouts.vertical', ['subtitle' => 'Customers'])

@section('content')
    <x-page-header title="Customers">
        <a href="{{ route('customers.create') }}" class="btn btn-primary"><iconify-icon icon="solar:user-plus-outline" class="align-middle me-1"></iconify-icon>Add customer</a>
    </x-page-header>

    <div class="row g-3 mb-3">
        <div class="col-6 col-md-4"><x-kpi label="Customers owe us" :value="money($totalDue)" icon="solar:hand-money-outline" color="danger" :href="route('customers.index', ['show' => 'owes'])" /></div>
    </div>

    <form class="filter-bar mb-3" method="GET">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control filter-grow" placeholder="Search name, shop, city or phone...">
        <select name="show" class="form-select" onchange="this.form.submit()">
            <option value="">All customers</option>
            <option value="owes" @selected(request('show') === 'owes')>Only who owe money</option>
        </select>
    </form>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack table-hover mb-0">
                    <thead><tr><th>Customer</th><th>City</th><th>Phone</th><th class="text-end">Owes us</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($customers as $c)
                            <tr>
                                <td class="td-title"><a href="{{ route('customers.show', $c) }}" class="fw-semibold">{{ $c->name }}</a>@if ($c->shop_name)<div class="small text-muted">{{ $c->shop_name }}</div>@endif</td>
                                <td data-label="City">{{ $c->city ?: '-' }}</td>
                                <td data-label="Phone">@if ($c->phone)<a href="tel:{{ $c->phone }}">{{ $c->phone }}</a>@else-@endif</td>
                                <td data-label="Owes us" class="text-end money fw-semibold {{ $c->due > 0.009 ? 'text-danger' : 'text-muted' }}">{{ $c->due > 0.009 ? money($c->due) : ($c->due < -0.009 ? 'Credit ' . money(-$c->due) : 'Nothing') }}</td>
                                <td class="td-actions text-end">
                                    <a href="{{ route('invoices.create', ['customer' => $c->id]) }}" class="btn btn-sm btn-soft-primary">Sell</a>
                                    @if ($c->due > 0.009)<a href="{{ route('payments.receive', ['customer' => $c->id]) }}" class="btn btn-sm btn-soft-success">Get paid</a>@endif
                                </td>
                            </tr>
                        @empty
                            <x-empty :cols="5" text="No customers found." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{ $customers->links() }}
@endsection

@extends('layouts.vertical', ['subtitle' => 'Customers'])

@section('content')
    <x-page-header title="Customers" :crumbs="['Sales', 'Customers']">
        <a href="{{ route('customers.create') }}" class="btn btn-primary"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>New Customer</a>
    </x-page-header>

    <div class="card">
        <div class="card-body border-bottom">
            <form class="filter-bar">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control filter-grow" placeholder="Search name, code, city or phone">
                <select name="group" class="form-select" onchange="this.form.submit()">
                    <option value="">All groups</option>
                    @foreach ($groups as $g)<option value="{{ $g->id }}" @selected(request('group') == $g->id)>{{ $g->name }}</option>@endforeach
                </select>
                <button class="btn btn-light">Search</button>
            </form>
        </div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack">
                    <thead><tr><th>Customer</th><th>Group</th><th>City</th><th>Phone</th><th class="text-end">Credit limit</th><th class="text-end">Outstanding</th><th class="text-end">Overdue</th></tr></thead>
                    <tbody>
                        @forelse ($customers as $c)
                            @php $bal = $c->balance(); $over = $c->overdueAmount(); @endphp
                            <tr>
                                <td class="td-title"><a href="{{ route('customers.show', $c) }}" class="doc-link">{{ $c->name }}</a><div class="small text-muted">{{ $c->code }} {{ $c->business_name && $c->business_name != $c->name ? '· ' . $c->business_name : '' }}</div></td>
                                <td data-label="Group">{{ $c->group?->name }}</td>
                                <td data-label="City">{{ $c->city }}</td>
                                <td data-label="Phone">{{ $c->phone }}</td>
                                <td data-label="Credit limit" class="text-end money">{{ money($c->credit_limit) }}</td>
                                <td data-label="Outstanding" class="text-end money fw-semibold {{ $c->credit_limit > 0 && $bal > $c->credit_limit ? 'text-danger' : '' }}">{{ money($bal) }}</td>
                                <td data-label="Overdue" class="text-end money {{ $over > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">{{ money($over) }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="7" text="No customers found." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($customers->hasPages())<div class="card-footer">{{ $customers->links() }}</div>@endif
    </div>
@endsection

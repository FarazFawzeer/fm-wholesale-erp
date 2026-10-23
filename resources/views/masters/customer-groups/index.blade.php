@extends('layouts.vertical', ['subtitle' => 'Customer Groups'])

@section('content')
    <x-page-header title="Customer Groups & Price Lists" :crumbs="['Pricing', 'Customer Groups']" />

    <div class="alert alert-info small">
        Each customer belongs to a group. Their price is the group's <strong>special price list</strong> for that product if one is set,
        otherwise the wholesale price with the group's discount (a negative discount adds a premium).
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Group</th><th class="text-end">Discount</th><th class="text-end">Customers</th><th class="text-end">Special prices</th><th></th></tr></thead>
                            <tbody>
                                @foreach ($groups as $g)
                                    <tr>
                                        <td class="td-title">
                                            <form method="POST" action="{{ route('customer-groups.update', $g) }}" class="d-flex flex-wrap gap-2 align-items-center" id="g{{ $g->id }}">
                                                @csrf @method('PUT')
                                                <input name="name" value="{{ $g->name }}" class="form-control form-control-sm" style="max-width: 220px">
                                                <input name="description" value="{{ $g->description }}" class="form-control form-control-sm" style="max-width: 260px" placeholder="Description">
                                            </form>
                                        </td>
                                        <td data-label="Discount %" class="text-end"><input form="g{{ $g->id }}" type="number" step="0.01" name="discount_percent" value="{{ $g->discount_percent }}" class="form-control form-control-sm text-end ms-auto" style="max-width: 100px"></td>
                                        <td data-label="Customers" class="text-end">{{ $g->customers_count }}</td>
                                        <td data-label="Special prices" class="text-end">{{ $g->price_list_items_count }}</td>
                                        <td class="td-actions text-end text-nowrap">
                                            <button form="g{{ $g->id }}" class="btn btn-sm btn-light">Save</button>
                                            <a href="{{ route('customer-groups.prices', $g) }}" class="btn btn-sm btn-soft-primary">Price list</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Add group</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('customer-groups.store') }}">
                        @csrf
                        <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
                        <div class="mb-2"><label class="form-label">Discount % off wholesale</label><input type="number" step="0.01" name="discount_percent" value="0" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Description</label><input name="description" class="form-control"></div>
                        <button class="btn btn-primary w-100">Add group</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

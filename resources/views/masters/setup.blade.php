@extends('layouts.vertical', ['subtitle' => 'Setup'])

@section('content')
    <x-page-header title="Brands, Categories & Stores" :crumbs="['Settings', 'Setup']" />

    @php
        $tab = request('tab', 'brands');
        $tabs = [
            'brands' => ['Brands', $brands, ['name' => 'Name', 'country' => 'Country']],
            'categories' => ['Categories & markup', $categories, ['name' => 'Name', 'markup_percent' => 'Markup %']],
            'warehouses' => ['Stores / warehouses', $warehouses, ['code' => 'Code', 'name' => 'Name', 'address' => 'Address']],
            'expense-categories' => ['Expense categories', $expenseCategories, ['name' => 'Name']],
        ];
    @endphp

    <ul class="nav nav-tabs nav-tabs-scroll mb-3">
        @foreach ($tabs as $key => [$label])
            <li class="nav-item"><a class="nav-link {{ $tab === $key ? 'active' : '' }}" href="?tab={{ $key }}">{{ $label }}</a></li>
        @endforeach
    </ul>

    @php [$label, $rows, $fields] = $tabs[$tab]; @endphp
    <div class="card">
        @if ($tab === 'categories')
            <div class="card-header small text-muted">Category markup is used for suggested selling prices (landed cost + markup). Empty = company default {{ setting('default_markup_percent') }}%.</div>
        @endif
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr>@foreach ($fields as $f => $l)<th>{{ $l }}</th>@endforeach<th></th></tr></thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                @foreach ($fields as $f => $l)
                                    <td data-label="{{ $l }}"><input form="r{{ $row->id }}" name="{{ $f }}" value="{{ $row->$f }}" class="form-control form-control-sm"></td>
                                @endforeach
                                <td class="td-actions text-end text-nowrap">
                                    <form method="POST" action="{{ route('setup.update', [$tab, $row->id]) }}" id="r{{ $row->id }}" class="d-inline">@csrf @method('PUT')<button class="btn btn-sm btn-light">Save</button></form>
                                    <form method="POST" action="{{ route('setup.destroy', [$tab, $row->id]) }}" class="d-inline" data-confirm="Delete this item?">@csrf @method('DELETE')<button class="btn btn-sm btn-soft-danger">Delete</button></form>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="table-light">
                            @foreach ($fields as $f => $l)
                                <td data-label="{{ $l }}"><input form="new-row" name="{{ $f }}" class="form-control form-control-sm" placeholder="New {{ strtolower($l) }}"></td>
                            @endforeach
                            <td class="td-actions text-end"><form method="POST" action="{{ route('setup.store', $tab) }}" id="new-row">@csrf<button class="btn btn-sm btn-primary">Add</button></form></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

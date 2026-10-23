@extends('layouts.vertical', ['subtitle' => 'Expenses'])

@section('content')
    <x-page-header title="Expenses" :crumbs="['Payments', 'Expenses']">
        <a href="{{ route('expenses.create') }}" class="btn btn-primary"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>New Expense</a>
    </x-page-header>

    <div class="card">
        <div class="card-body">
            <form class="filter-bar">
                <input type="date" name="from" value="{{ $from }}" class="form-control">
                <input type="date" name="to" value="{{ $to }}" class="form-control">
                <select name="category_id" class="form-select"><option value="">All categories</option>@foreach ($categories as $c)<option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>@endforeach</select>
                <button class="btn btn-light">Filter</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Expense</th><th>Date</th><th>Category</th><th>Paid from</th><th class="text-end">Amount</th></tr></thead>
                            <tbody>
                                @forelse ($expenses as $e)
                                    <tr>
                                        <td class="td-title"><strong>{{ $e->number }}</strong> <span class="small text-muted">{{ $e->description }} {{ $e->payee ? '· ' . $e->payee : '' }}</span></td>
                                        <td data-label="Date">{{ $e->date->format('d M Y') }}</td>
                                        <td data-label="Category">{{ $e->category->name }}</td>
                                        <td data-label="Paid from">{{ $e->account->name }}</td>
                                        <td data-label="Amount" class="text-end money fw-semibold">{{ money($e->amount) }}</td>
                                    </tr>
                                @empty
                                    <x-empty :cols="5" text="No expenses in this period." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($expenses->hasPages())<div class="card-footer">{{ $expenses->links() }}</div>@endif
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0">By category</h5><strong class="money">{{ money($total) }}</strong></div>
                <ul class="list-group list-group-flush">
                    @foreach ($byCategory as $row)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between"><span>{{ $row->category->name }}</span><strong class="money">{{ money($row->total) }}</strong></div>
                            <div class="progress mt-1" style="height: 4px"><div class="progress-bar" style="width: {{ $total > 0 ? $row->total / $total * 100 : 0 }}%"></div></div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection

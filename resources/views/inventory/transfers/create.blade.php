@extends('layouts.vertical', ['subtitle' => 'New Stock Transfer'])

@section('content')
    <x-page-header title="New Stock Transfer" :crumbs="['Transfers' => route('stock-transfers.index'), 'New']" />

    <form method="POST" action="{{ route('stock-transfers.store') }}">
        @csrf
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                <div class="card">
                    <div class="card-body" data-line-editor data-mode="stock" data-check-stock="1" data-products='@json($catalog)' data-initial='@json($initial)'>
                        <select class="product-picker"></select>
                        <div class="lines mt-3"></div>
                        <div class="empty-lines text-center text-muted py-4">Add products and enter pairs per size to move.</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 order-1 order-xl-2">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3"><label class="form-label">From store</label>
                            <select name="from_warehouse_id" id="warehouse_id" class="form-select">@foreach ($warehouses as $w)<option value="{{ $w->id }}" @selected($w->is_default)>{{ $w->name }}</option>@endforeach</select></div>
                        <div class="mb-3"><label class="form-label">To store</label>
                            <select name="to_warehouse_id" class="form-select">@foreach ($warehouses as $w)<option value="{{ $w->id }}" @selected(! $w->is_default)>{{ $w->name }}</option>@endforeach</select></div>
                        <div class="mb-3"><label class="form-label">Date</label><input type="date" name="date" value="{{ now()->format('Y-m-d') }}" class="form-control"></div>
                        <div><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                        <div class="totals-box border-top mt-3 pt-2"><div class="row-total grand"><span>Pairs</span><span class="js-sum-pairs">0</span></div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="{{ route('stock-transfers.index') }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">Transfer stock</button>
        </div>
    </form>
@endsection

@extends('layouts.vertical', ['subtitle' => 'Bulk Price Update'])

@section('content')
    <x-page-header title="Bulk Price Update" :crumbs="['Pricing', 'Bulk Price Update']" />

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label small">Brand</label>
                    <select name="brand_id" class="form-select">
                        <option value="">All</option>
                        @foreach ($brands as $b)<option value="{{ $b->id }}" @selected(request('brand_id') == $b->id)>{{ $b->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All</option>
                        @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small">Update method</label>
                    <select name="action" class="form-select" required>
                        <option value="markup" @selected(request('action') == 'markup')>Landed cost + markup %</option>
                        <option value="percent" @selected(request('action') == 'percent')>Change current price by %</option>
                        <option value="amount" @selected(request('action') == 'amount')>Change current price by Rs</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small">Value</label>
                    <input type="number" step="0.01" name="value" value="{{ request('value') }}" class="form-control" placeholder="Rule default">
                </div>
                <div class="col-6 col-md-1">
                    <label class="form-label small">Round to</label>
                    <select name="round" class="form-select">
                        @foreach ([1, 10, 50, 100] as $r)<option value="{{ $r }}" @selected(request('round', 10) == $r)>{{ $r }}</option>@endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2"><button class="btn btn-light w-100">Preview</button></div>
            </form>
            <div class="form-text mt-2">Markup with an empty value uses each product's rule (product override &rarr; category &rarr; company default {{ setting('default_markup_percent') }}%).</div>
        </div>
    </div>

    @if ($preview)
        <form method="POST" action="{{ route('products.bulk-price.store') }}" data-confirm="Update the selling price of the selected products?">
            @csrf
            @foreach (['action', 'value', 'round'] as $f)<input type="hidden" name="{{ $f }}" value="{{ request($f) }}">@endforeach
            <div class="card">
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th><input type="checkbox" class="form-check-input" checked onclick="document.querySelectorAll('.js-pick').forEach(c => c.checked = this.checked)"></th><th>Product</th><th class="text-end">Landed cost</th><th class="text-end">Current price</th><th class="text-end">New price</th><th class="text-end">New margin</th></tr></thead>
                            <tbody>
                                @forelse ($preview as $row)
                                    @php $p = $row['product']; $margin = $row['new'] > 0 ? ($row['new'] - $p->cost_price) / $row['new'] * 100 : 0; @endphp
                                    <tr>
                                        <td class="td-actions"><input type="checkbox" class="form-check-input js-pick" name="products[]" value="{{ $p->id }}" checked></td>
                                        <td class="td-title"><strong>{{ $p->code }}</strong> <span class="text-muted small">{{ $p->name }} {{ $p->colour }}</span></td>
                                        <td data-label="Cost" class="text-end money">{{ money($p->cost_price) }}</td>
                                        <td data-label="Current" class="text-end money">{{ money($p->selling_price) }}</td>
                                        <td data-label="New" class="text-end money fw-bold {{ $row['new'] > $p->selling_price ? 'text-success' : ($row['new'] < $p->selling_price ? 'text-danger' : '') }}">{{ money($row['new']) }}</td>
                                        <td data-label="Margin" class="text-end"><span class="badge {{ $margin < setting('min_margin_percent') ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">{{ number_format($margin, 1) }}%</span></td>
                                    </tr>
                                @empty
                                    <x-empty :cols="6" text="No products match the filter." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end sticky-actions"><button class="btn btn-primary">Apply new prices</button></div>
            </div>
        </form>
    @endif
@endsection

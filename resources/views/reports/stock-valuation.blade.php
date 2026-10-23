@extends('layouts.vertical', ['subtitle' => 'Stock Valuation'])

@section('content')
    <x-page-header title="Stock Valuation" :crumbs="['Reports' => route('reports.index'), 'Stock valuation']">
        <form><select name="warehouse_id" class="form-select" onchange="this.form.submit()"><option value="">All stores</option>@foreach ($warehouses as $w)<option value="{{ $w->id }}" @selected(request('warehouse_id') == $w->id)>{{ $w->name }}</option>@endforeach</select></form>
        <button onclick="window.print()" class="btn btn-light">Print</button>
    </x-page-header>

    @php
        $cost = $products->sum(fn ($p) => $p->on_hand * $p->cost_price);
        $retail = $products->sum(fn ($p) => $p->on_hand * $p->selling_price);
        $dmg = $products->sum(fn ($p) => $p->damaged * $p->cost_price);
    @endphp
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><x-kpi label="Pairs" :value="qty($products->sum('on_hand'))" icon="solar:box-outline" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Value at landed cost" :value="money($cost)" icon="solar:wallet-money-outline" color="info" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Value at wholesale" :value="money($retail)" icon="solar:tag-price-outline" color="success" :sub="'Potential profit ' . money($retail - $cost)" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Damaged stock" :value="money($dmg)" icon="solar:danger-triangle-outline" color="danger" :sub="qty($products->sum('damaged')) . ' pairs'" /></div>
    </div>

    @foreach ($byCategory as $cat => $items)
        <div class="card">
            <div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0">{{ $cat }}</h5><strong class="money">{{ money($items->sum(fn ($p) => $p->on_hand * $p->cost_price)) }}</strong></div>
            <div class="card-body table-wrap p-0">
                <div class="table-responsive">
                    <table class="table erp-table stack mb-0">
                        <thead><tr><th>Product</th><th class="text-end">Pairs</th><th class="text-end">Landed cost</th><th class="text-end">Stock value</th><th class="text-end">Wholesale value</th><th class="text-end">Damaged</th></tr></thead>
                        <tbody>
                            @foreach ($items as $p)
                                <tr>
                                    <td class="td-title"><a href="{{ route('products.show', $p) }}" class="doc-link">{{ $p->code }}</a> <span class="small text-muted">{{ $p->brand?->name }} {{ $p->name }} {{ $p->colour }}</span></td>
                                    <td data-label="Pairs" class="text-end">{{ qty($p->on_hand) }}</td>
                                    <td data-label="Cost" class="text-end money">{{ money($p->cost_price) }}</td>
                                    <td data-label="Value" class="text-end money fw-semibold">{{ money($p->on_hand * $p->cost_price) }}</td>
                                    <td data-label="Wholesale" class="text-end money">{{ money($p->on_hand * $p->selling_price) }}</td>
                                    <td data-label="Damaged" class="text-end {{ $p->damaged ? 'text-danger' : 'text-muted' }}">{{ $p->damaged ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
@endsection

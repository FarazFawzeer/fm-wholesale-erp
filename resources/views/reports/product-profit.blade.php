@extends('layouts.vertical', ['subtitle' => 'Product Profit'])

@section('content')
    <x-page-header title="Product Profit" :crumbs="['Reports' => route('reports.index'), 'Product profit']" />

    @component('reports.partials.range', ['from' => $from, 'to' => $to])
        <div><label class="form-label small mb-1">Category</label><select name="category_id" class="form-select"><option value="">All</option>@foreach ($categories as $c)<option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
    @endcomponent

    @php $s = $rows->sum('sales'); $c = $rows->sum('cost'); @endphp
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><x-kpi label="Pairs sold" :value="qty($rows->sum('pairs'))" icon="solar:box-outline" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Net sales" :value="money($s)" icon="solar:cart-large-2-outline" color="primary" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Landed cost of sales" :value="money($c)" icon="solar:globus-outline" color="secondary" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Gross profit" :value="money($s - $c)" icon="solar:graph-up-outline" color="success" :sub="($s > 0 ? number_format(($s - $c) / $s * 100, 1) : 0) . '% margin'" /></div>
    </div>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Product</th><th class="text-end">Pairs</th><th class="text-end">Sales</th><th class="text-end">Cost</th><th class="text-end">Profit</th><th class="text-end">Margin</th></tr></thead>
                    <tbody>
                        @forelse ($rows as $r)
                            <tr>
                                <td class="td-title"><a href="{{ route('products.show', $r->id) }}" class="doc-link">{{ $r->code }}</a> <span class="small text-muted">{{ $r->brand }} {{ $r->name }} {{ $r->colour }}</span></td>
                                <td data-label="Pairs" class="text-end">{{ qty($r->pairs) }}</td>
                                <td data-label="Sales" class="text-end money">{{ money($r->sales) }}</td>
                                <td data-label="Cost" class="text-end money">{{ money($r->cost) }}</td>
                                <td data-label="Profit" class="text-end money fw-semibold text-success">{{ money($r->profit) }}</td>
                                <td data-label="Margin" class="text-end"><span class="badge {{ $r->margin < setting('min_margin_percent') ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">{{ number_format($r->margin, 1) }}%</span></td>
                            </tr>
                        @empty
                            <x-empty :cols="6" text="No sales in this period." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

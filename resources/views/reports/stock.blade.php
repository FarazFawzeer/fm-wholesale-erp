@extends('layouts.vertical', ['subtitle' => 'Stock report'])

@section('content')
    <x-page-header title="Reports">
        <button type="button" class="btn btn-soft-secondary" onclick="window.print()"><iconify-icon icon="solar:printer-outline" class="align-middle me-1"></iconify-icon>Print</button>
    </x-page-header>
    @include('reports.tabs')

    <form class="filter-bar mb-3 no-print" method="GET">
        <select name="show" class="form-select" onchange="this.form.submit()" style="max-width: 240px">
            <option value="">All products</option>
            <option value="low" @selected(request('show') === 'low')>Low stock only (to buy)</option>
        </select>
    </form>

    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-4"><x-kpi label="Pairs in stock" :value="qty($pairs)" icon="solar:box-outline" color="primary" /></div>
        <div class="col-6 col-xl-4"><x-kpi label="Stock value (cost)" :value="money($costValue)" icon="solar:wallet-outline" color="info" /></div>
        <div class="col-12 col-xl-4"><x-kpi label="Value at selling price" :value="money($saleValue)" icon="solar:tag-price-outline" color="success" /></div>
    </div>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Product</th><th>Colours (pairs)</th><th class="text-end">Pairs</th><th class="text-end">Cost value</th></tr></thead>
                    <tbody>
                        @forelse ($products as $p)
                            <tr class="{{ $p->isLow() ? 'table-danger-subtle' : '' }}">
                                <td class="td-title"><a href="{{ route('products.show', $p) }}" class="d-flex align-items-center gap-2"><x-thumb :product="$p" :size="36" />
                                    <span><span class="fw-semibold">{{ $p->name }}</span> <small class="text-muted">{{ $p->code }} {{ $p->sizeLabel() ? '· ' . $p->sizeLabel() : '' }}</small></span></a></td>
                                <td data-label="Colours"><div class="colour-list justify-content-end justify-content-md-start">@foreach ($p->colours as $c)<x-colour-chip :name="$c->title" :qty="$c->qty" />@endforeach</div></td>
                                <td data-label="Pairs" class="text-end fw-bold {{ $p->isLow() ? 'text-danger' : '' }}">{{ qty($p->totalStock()) }}</td>
                                <td data-label="Cost value" class="text-end money">{{ money(max(0, $p->totalStock()) * $p->cost_price, false) }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="4" text="No products." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

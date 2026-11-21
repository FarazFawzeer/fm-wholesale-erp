@extends('layouts.vertical', ['subtitle' => 'Stock report'])

@php
    // colour of the age badge: under 3 months fine, 3-6 months watch, 6 months+ old stock
    $ageClass = fn ($d) => $d === null ? 'bg-light text-muted' : ($d >= 182 ? 'bg-danger-subtle text-danger' : ($d >= 91 ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success'));
    $ages = ['' => 'Any age', '1' => '1 month or more', '3' => '3 months or more', '6' => '6 months or more', '12' => '1 year or more'];
@endphp

@section('content')
    <x-page-header title="Reports">
        <button type="button" class="btn btn-soft-secondary" onclick="window.print()"><iconify-icon icon="solar:printer-outline" class="align-middle me-1"></iconify-icon>Print</button>
    </x-page-header>
    @include('reports.tabs')

    <form class="stock-filter card card-body mb-3 no-print" method="GET">
        <div class="sf-ages">
            <span class="sf-label">In stock for</span>
            @foreach ($ages as $v => $label)
                <a href="{{ request()->fullUrlWithQuery(['age' => $v ?: null]) }}" class="sf-chip {{ (string) request('age', '') === (string) $v ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>
        <div class="sf-row">
            <label class="sf-field"><span>Came in from</span><input type="date" name="from" value="{{ request('from') }}" class="form-control"></label>
            <label class="sf-field"><span>to</span><input type="date" name="to" value="{{ request('to') }}" class="form-control"></label>
            <label class="sf-field"><span>Show</span>
                <select name="show" class="form-select"><option value="">All products</option><option value="low" @selected(request('show') === 'low')>Low stock only</option></select></label>
            <label class="sf-field"><span>Sort</span>
                <select name="sort" class="form-select">
                    <option value="">By name</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Oldest stock first</option>
                    <option value="pairs" @selected(request('sort') === 'pairs')>Most pairs first</option>
                </select></label>
            <input type="hidden" name="age" value="{{ request('age') }}">
            <div class="sf-actions">
                <button class="btn btn-primary">Show</button>
                @if (request()->hasAny(['age', 'from', 'to', 'show', 'sort']))<a href="{{ route('reports.stock') }}" class="btn btn-light">Clear</a>@endif
            </div>
        </div>
    </form>

    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3"><x-kpi label="Pairs in stock" :value="qty($pairs)" icon="solar:box-outline" color="primary" /></div>
        <div class="col-6 col-xl-3"><x-kpi label="Stock value (cost)" :value="money($costValue)" icon="solar:wallet-outline" color="info" /></div>
        <div class="col-6 col-xl-3"><x-kpi label="Value at selling price" :value="money($saleValue)" icon="solar:tag-price-outline" color="success" /></div>
        <div class="col-6 col-xl-3"><x-kpi label="Older than 6 months" :value="qty($oldPairs) . ' pairs'" :sub="money($oldValue)" icon="solar:hourglass-line-outline" color="danger" /></div>
    </div>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Product</th><th>Colours (pairs)</th><th class="text-end">Pairs</th><th>In stock since</th><th>Last sold</th><th class="text-end">Cost value</th></tr></thead>
                    <tbody>
                        @forelse ($products as $p)
                            <tr class="{{ $p->isLow() ? 'table-danger-subtle' : '' }}">
                                <td class="td-title"><a href="{{ route('products.show', $p) }}" class="d-flex align-items-center gap-2"><x-thumb :product="$p" :size="36" />
                                    <span><span class="fw-semibold">{{ $p->name }}</span> <small class="text-muted">{{ $p->code }}</small></span></a></td>
                                <td data-label="Colours"><div class="colour-list justify-content-end justify-content-md-start"><x-colour-chips :lines="$p->colours" /></div></td>
                                <td data-label="Pairs" class="text-end fw-bold {{ $p->isLow() ? 'text-danger' : '' }}">{{ qty($p->totalStock()) }}</td>
                                <td data-label="In stock since">
                                    @if ($p->stock_since)
                                        <span class="badge {{ $ageClass($p->age_days) }} fw-semibold">{{ age_label($p->age_days) }}</span>
                                        <small class="text-muted d-md-block ms-1 ms-md-0">{{ $p->stock_since->format('d M Y') }}</small>
                                    @else
                                        <span class="text-muted small">No stock</span>
                                    @endif
                                </td>
                                <td data-label="Last sold" class="small">{{ $p->last_sold ? $p->last_sold->format('d M Y') : 'Never' }}</td>
                                <td data-label="Cost value" class="text-end money">{{ money(max(0, $p->totalStock()) * $p->cost_price, false) }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="6" text="No products match." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="small text-muted no-print">"In stock since" is the date the oldest pairs still in stock came in. <span class="badge bg-success-subtle text-success">under 3 months</span> <span class="badge bg-warning-subtle text-warning">3-6 months</span> <span class="badge bg-danger-subtle text-danger">6 months +</span></div>
@endsection

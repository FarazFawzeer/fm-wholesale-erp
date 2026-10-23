@extends('layouts.vertical', ['subtitle' => 'Invoice Profit'])

@section('content')
    <x-page-header title="Invoice Profit" :crumbs="['Reports' => route('reports.index'), 'Invoice profit']" />

    @include('reports.partials.range', ['from' => $from, 'to' => $to])

    @php $net = $invoices->sum('net'); $gp = $invoices->sum('gp'); @endphp
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><x-kpi label="Net sales" :value="money($net)" icon="solar:cart-large-2-outline" :sub="$invoices->count() . ' invoices'" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Gross profit" :value="money($gp)" icon="solar:graph-up-outline" color="success" :sub="($net > 0 ? number_format($gp / $net * 100, 1) : 0) . '% margin'" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Expenses" :value="money($expenses)" icon="solar:bill-cross-outline" color="danger" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Net profit" :value="money($gp - $expenses)" icon="solar:wallet-money-outline" :color="$gp - $expenses >= 0 ? 'success' : 'danger'" sub="Gross profit - expenses" /></div>
    </div>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Invoice</th><th>Customer</th><th>Date</th><th class="text-end">Net sale</th><th class="text-end">Cost</th><th class="text-end">Profit</th><th class="text-end">Margin</th></tr></thead>
                    <tbody>
                        @forelse ($invoices as $i)
                            @php $m = $i->net > 0 ? $i->gp / $i->net * 100 : 0; @endphp
                            <tr>
                                <td class="td-title"><a href="{{ route('invoices.show', $i) }}" class="doc-link">{{ $i->number }}</a> {!! status_badge($i->sale_type) !!}</td>
                                <td data-label="Customer">{{ $i->customer->name }}</td>
                                <td data-label="Date">{{ $i->date->format('d M Y') }}</td>
                                <td data-label="Net sale" class="text-end money">{{ money($i->net) }}</td>
                                <td data-label="Cost" class="text-end money">{{ money($i->cost_total) }}</td>
                                <td data-label="Profit" class="text-end money fw-semibold text-success">{{ money($i->gp) }}</td>
                                <td data-label="Margin" class="text-end"><span class="badge {{ $m < setting('min_margin_percent') ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">{{ number_format($m, 1) }}%</span></td>
                            </tr>
                        @empty
                            <x-empty :cols="7" text="No invoices in this period." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

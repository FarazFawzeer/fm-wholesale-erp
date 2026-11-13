@extends('layouts.vertical', ['subtitle' => 'Reports'])

@php
    $net = $gross - $expenses;
    $methods = \App\Models\Payment::METHODS;
@endphp

@section('content')
    <x-page-header title="Reports">
        <button type="button" class="btn btn-soft-secondary" onclick="window.print()"><iconify-icon icon="solar:printer-outline" class="align-middle me-1"></iconify-icon>Print</button>
    </x-page-header>
    @include('reports.tabs')
    @include('reports.partials.range')

    <div class="text-muted small mb-2">{{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</div>

    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3"><x-kpi label="Sales" :value="money($sales->amount)" icon="solar:cart-large-2-outline" color="primary" :sub="$sales->bills . ' bills · ' . qty($pairs) . ' pairs'" /></div>
        <div class="col-6 col-xl-3"><x-kpi label="Gross profit" :value="money($gross)" icon="solar:graph-up-outline" color="success" :sub="($sales->amount > 0 ? number_format($gross / $sales->amount * 100, 1) : 0) . '% of sales'" /></div>
        <div class="col-6 col-xl-3"><x-kpi label="Expenses" :value="money($expenses)" icon="solar:bill-list-outline" color="warning" /></div>
        <div class="col-6 col-xl-3"><x-kpi label="Net profit" :value="money($net)" icon="solar:wallet-money-outline" :color="$net < 0 ? 'danger' : 'success'" sub="Gross profit - expenses" /></div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Profit</h5></div>
                <div class="card-body totals-box">
                    <div class="row-total"><span>Sales (after returns)</span><span class="money">{{ money($sales->amount) }}</span></div>
                    <div class="row-total"><span class="text-muted">- Cost of the pairs sold</span><span class="money">{{ money($sales->cost) }}</span></div>
                    <div class="row-total fw-semibold"><span>= Gross profit</span><span class="money text-success">{{ money($gross) }}</span></div>
                    <div class="row-total"><span class="text-muted">- Expenses</span><span class="money">{{ money($expenses) }}</span></div>
                    <div class="row-total grand"><span>= Net profit</span><span class="money {{ $net < 0 ? 'text-danger' : 'text-success' }}">{{ money($net) }}</span></div>
                    @if ($sales->discount > 0)<div class="small text-muted mt-2">Discounts given: {{ money($sales->discount) }}</div>@endif
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Expenses by type</h5></div>
                <ul class="list-group list-group-flush">
                    @forelse ($expensesByType as $row)
                        <li class="list-group-item d-flex justify-content-between"><span>{{ $row->category?->name }}</span><strong class="money">{{ money($row->total) }}</strong></li>
                    @empty
                        <li class="list-group-item text-muted small">No expenses.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Money in & out by method</h5></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table mb-0">
                            <thead><tr><th>Method</th><th class="text-end">From customers</th><th class="text-end">To suppliers</th><th class="text-end">Expenses</th></tr></thead>
                            <tbody>
                                @foreach ($methods as $key => $label)
                                    <tr>
                                        <td><iconify-icon icon="{{ \App\Models\Payment::ICONS[$key] }}" class="align-middle me-1"></iconify-icon>{{ $label }}</td>
                                        <td class="text-end money text-success">{{ money($moneyIn[$key] ?? 0, false) }}</td>
                                        <td class="text-end money">{{ money($moneyOut[$key] ?? 0, false) }}</td>
                                        <td class="text-end money">{{ money($expensesByMethod[$key] ?? 0, false) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td class="text-end money text-success">{{ money($moneyIn->sum(), false) }}</td>
                                    <td class="text-end money">{{ money($moneyOut->sum(), false) }}</td>
                                    <td class="text-end money">{{ money($expenses, false) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @php $cashLeft = ($moneyIn['cash'] ?? 0) - ($moneyOut['cash'] ?? 0) - ($expensesByMethod['cash'] ?? 0); @endphp
                <div class="card-footer d-flex justify-content-between"><span>Cash in hand change (cash in - cash out)</span><strong class="money {{ $cashLeft < 0 ? 'text-danger' : 'text-success' }}">{{ money($cashLeft) }}</strong></div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="info-list">
                        <div><div class="label">Bought from suppliers</div><div class="value money">{{ money($bought->amount) }}</div><div class="small text-muted">{{ $bought->bills }} purchases</div></div>
                        <div><div class="label">Customers owe us (now)</div><div class="value money text-danger"><a href="{{ route('dues') }}" class="text-danger">{{ money($customerDue) }}</a></div></div>
                        <div><div class="label">We owe suppliers (now)</div><div class="value money text-warning"><a href="{{ route('dues', ['tab' => 'suppliers']) }}" class="text-warning">{{ money($supplierDue) }}</a></div></div>
                    </div>
                </div>
            </div>
        </div>

        @if ($daily->count() > 1)
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Sales by day</h5></div>
                    <div class="card-body"><div id="sales-chart" data-chart='@json($daily->map(fn ($d) => ['d' => \Carbon\Carbon::parse($d->date)->format('d M'), 'sales' => round($d->amount), 'profit' => round($d->profit)]))'></div></div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/pages/sales-chart.js'])
@endsection

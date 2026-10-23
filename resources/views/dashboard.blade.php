@extends('layouts.vertical', ['subtitle' => 'Dashboard'])

@section('content')
    <x-page-header :title="'Good ' . (now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')) . ', ' . explode(' ', auth()->user()->name)[0]">
        <span class="text-muted small align-self-center">{{ now()->format('l, d M Y') }}</span>
    </x-page-header>

    <div class="row g-2 mb-3 quick-actions d-md-none">
        @foreach ([['New Invoice', 'invoices.create', 'solar:bill-list-outline', 'primary'], ['Receive', 'receipts.create', 'solar:wallet-money-outline', 'success'], ['New PO', 'purchase-orders.create', 'solar:globus-outline', 'info'], ['Stock', 'stock.index', 'solar:box-outline', 'secondary']] as [$l, $r, $i, $c])
            <div class="col-3"><a href="{{ route($r) }}" class="btn btn-soft-{{ $c }} w-100"><iconify-icon icon="{{ $i }}"></iconify-icon>{{ $l }}</a></div>
        @endforeach
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3"><x-kpi label="Sales today" :value="money($todaySales)" icon="solar:cart-large-2-outline" color="primary" :href="route('invoices.index', ['from' => now()->toDateString()])" :sub="'Month: ' . money($month->total)" /></div>
        <div class="col-6 col-xl-3"><x-kpi label="Gross profit (month)" :value="money($month->total - $month->cost)" icon="solar:graph-up-outline" color="success" :href="route('reports.invoice-profit')" :sub="($month->total > 0 ? number_format(($month->total - $month->cost) / $month->total * 100, 1) : 0) . '% margin &middot; expenses ' . money($monthExpenses)" /></div>
        <div class="col-6 col-xl-3"><x-kpi label="Receivable" :value="money($receivable)" icon="solar:hand-money-outline" color="warning" :href="route('reports.outstanding')" :sub="'<span class=\'text-danger fw-semibold\'>' . money($overdue->sum(fn ($i) => $i->balance())) . ' overdue</span>'" /></div>
        <div class="col-6 col-xl-3"><x-kpi label="Stock value" :value="money($stockValue)" icon="solar:box-outline" color="info" :href="route('reports.stock-valuation')" :sub="qty($stockPairs) . ' pairs at landed cost'" /></div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title mb-0">Sales & gross profit - last 30 days</h5><a href="{{ route('reports.index') }}" class="small">Reports</a></div>
                <div class="card-body"><div id="sales-chart" data-chart='@json($chart)' style="min-height: 280px"></div></div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title mb-0">Cash & bank</h5><a href="{{ route('cashbook') }}" class="small">Cashbook</a></div>
                <ul class="list-group list-group-flush">
                    @foreach ($accounts as $a)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><iconify-icon icon="{{ $a['type'] === 'cash' ? 'solar:wallet-outline' : ($a['type'] === 'card' ? 'solar:card-outline' : 'solar:buildings-outline') }}" class="align-middle me-1 text-muted"></iconify-icon>{{ $a['name'] }}</span>
                            <strong class="money">{{ money($a['balance']) }}</strong>
                        </li>
                    @endforeach
                    <li class="list-group-item d-flex justify-content-between bg-light"><span class="fw-semibold">Total</span><strong class="money">{{ money($accounts->sum('balance')) }}</strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Owed to suppliers</span><strong class="money text-danger">{{ money($payable) }}</strong></li>
                </ul>
            </div>
            @if ($alerts->count())
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Needs attention</h5></div>
                    <div class="list-group list-group-flush">
                        @foreach ($alerts as $a)
                            <a href="{{ $a['url'] }}" class="list-group-item list-group-item-action d-flex gap-2 align-items-center">
                                <iconify-icon icon="{{ $a['icon'] }}" class="fs-20 text-{{ $a['color'] }}"></iconify-icon>
                                <span><span class="fw-semibold">{{ $a['title'] }}</span><br><span class="small text-muted">{{ $a['text'] }}</span></span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-xl-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0">Overdue customers</h5><a href="{{ route('credit-control.index') }}" class="small">Credit control</a></div>
                <ul class="list-group list-group-flush">
                    @forelse ($overdueCustomers as $row)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><a href="{{ route('customers.show', $row['customer']) }}" class="fw-semibold">{{ $row['customer']->name }}</a><br><span class="small text-danger">{{ $row['days'] }} days overdue</span></span>
                            <strong class="money text-danger">{{ money($row['amount']) }}</strong>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">No overdue invoices.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="col-lg-6 col-xl-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0">Cheques due (14 days)</h5><a href="{{ route('cheques.index') }}" class="small">All cheques</a></div>
                <ul class="list-group list-group-flush">
                    @forelse ($chequesDue as $ch)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <span class="badge {{ $ch->direction === 'received' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $ch->direction === 'received' ? 'IN' : 'OUT' }}</span>
                                #{{ $ch->cheque_no }} <span class="small text-muted">{{ $ch->party()?->name }}</span><br>
                                <span class="small {{ $ch->cheque_date->isPast() ? 'text-danger' : 'text-muted' }}">{{ $ch->cheque_date->format('d M Y') }} &middot; {{ $ch->bank }}</span>
                            </span>
                            <strong class="money">{{ money($ch->amount) }}</strong>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">No cheques due.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="col-lg-6 col-xl-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0">Shipments in progress</h5><a href="{{ route('purchase-orders.index') }}" class="small">Imports</a></div>
                <ul class="list-group list-group-flush">
                    @forelse ($inTransit as $po)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><a href="{{ route('purchase-orders.show', $po) }}" class="fw-semibold">{{ $po->number }}</a> {!! status_badge($po->status) !!}<br><span class="small text-muted">{{ $po->supplier->name }} &middot; ETA {{ $po->expected_date?->format('d M') }}</span></span>
                            <strong class="money">{{ money($po->landed_total) }}</strong>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">No open shipments.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0">Recent invoices</h5><a href="{{ route('invoices.index') }}" class="small">All invoices</a></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Payment</th><th>Delivery</th><th class="text-end">Total</th></tr></thead>
                            <tbody>
                                @foreach ($recent as $inv)
                                    <tr>
                                        <td class="td-title"><a href="{{ route('invoices.show', $inv) }}" class="doc-link">{{ $inv->number }}</a></td>
                                        <td data-label="Customer">{{ $inv->customer->name }}</td>
                                        <td data-label="Date">{{ $inv->date->format('d M') }}</td>
                                        <td data-label="Payment">{!! status_badge($inv->payment_status) !!}</td>
                                        <td data-label="Delivery">{!! status_badge($inv->delivery_status) !!}</td>
                                        <td data-label="Total" class="text-end money fw-semibold">{{ money($inv->total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Top products (30 days)</h5></div>
                <ul class="list-group list-group-flush">
                    @forelse ($topProducts as $tp)
                        <li class="list-group-item d-flex justify-content-between">
                            <span><a href="{{ route('products.show', $tp->id) }}" class="fw-semibold">{{ $tp->code }}</a> <span class="small text-muted">{{ $tp->name }} {{ $tp->colour }}</span><br><span class="small text-muted">{{ qty($tp->pairs) }} pairs</span></span>
                            <strong class="money">{{ money($tp->sales) }}</strong>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">No sales yet.</li>
                    @endforelse
                </ul>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0">Low stock</h5><a href="{{ route('reports.low-stock') }}" class="small">View all</a></div>
                <ul class="list-group list-group-flush">
                    @forelse ($lowStock as $p)
                        <li class="list-group-item d-flex justify-content-between"><span><a href="{{ route('products.show', $p) }}">{{ $p->code }}</a> <span class="small text-muted">{{ $p->name }} {{ $p->colour }}</span></span><span class="text-danger fw-semibold">{{ qty($p->on_hand) }} / {{ $p->reorder_level }}</span></li>
                    @empty
                        <li class="list-group-item text-muted small">Stock levels are healthy.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/pages/erp-dashboard.js'])
@endsection

@extends('layouts.vertical', ['subtitle' => 'Home'])

@php
    $hour = now()->hour;
    $greet = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $trendChip = function ($t, $label) {
        if ($t === null) return '<span class="trend flat">' . e($label) . '</span>';
        $up = $t >= 0;
        return '<span class="trend ' . ($up ? 'up' : 'down') . '">' . ($up ? '▲ ' : '▼ ') . abs($t) . '%</span> <span class="text-muted">' . e($label) . '</span>';
    };
    $maxTop = max(1, (int) $top->max('pairs'));
@endphp

@section('content')
    <div class="install-card d-none" data-install-card>
        <img src="{{ asset('images/app/icon-192.png') }}" alt="">
        <div class="flex-grow-1 min-w-0">
            <div class="fw-bold">Install the {{ setting('company_name', 'FM Shoes') }} app</div>
            <div class="small text-muted">Open it from your home screen like any other app.</div>
        </div>
        <button type="button" class="btn btn-dark btn-sm" data-install-app>Install</button>
        <button type="button" class="btn-close" data-install-dismiss aria-label="Not now"></button>
    </div>

    <div class="dash-head">
        <div>
            <div class="text-muted small fw-semibold">{{ now()->format('l, d F Y') }}</div>
            <h3 class="mb-0">{{ $greet }}, {{ explode(' ', auth()->user()->name)[0] }}</h3>
        </div>
        <div class="dash-actions">
            <a href="{{ route('invoices.create') }}" class="btn btn-brand"><iconify-icon icon="solar:cart-large-2-bold" class="align-middle me-1"></iconify-icon>New sale</a>
            <a href="{{ route('purchases.create') }}" class="btn btn-light"><iconify-icon icon="solar:box-outline" class="align-middle me-1"></iconify-icon>Buy</a>
            <a href="{{ route('payments.receive') }}" class="btn btn-light"><iconify-icon icon="solar:wallet-money-outline" class="align-middle me-1"></iconify-icon>Get paid</a>
            <a href="{{ route('products.create') }}" class="btn btn-light d-none d-xl-inline-flex"><iconify-icon icon="ph:sneaker" class="align-middle me-1"></iconify-icon>Add product</a>
        </div>
    </div>

    {{-- Key figures --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3">
            <a href="{{ route('invoices.index', ['from' => now()->toDateString()]) }}" class="card stat-card">
                <div class="stat-top"><span class="stat-icon bg-dark-subtle"><iconify-icon icon="solar:cart-large-2-bold-duotone"></iconify-icon></span><span class="stat-label">Sold today</span></div>
                <div class="stat-value">{{ money($todaySales->amount) }}</div>
                <div class="stat-sub">{!! $trendChip($todayTrend, 'vs yesterday') !!}</div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <a href="{{ route('reports.index') }}" class="card stat-card">
                <div class="stat-top"><span class="stat-icon bg-success-subtle text-success"><iconify-icon icon="solar:graph-up-bold-duotone"></iconify-icon></span><span class="stat-label">This month</span></div>
                <div class="stat-value">{{ money($month->amount) }}</div>
                <div class="stat-sub">{!! $trendChip($monthTrend, 'vs last month') !!}</div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <a href="{{ route('dues') }}" class="card stat-card">
                <div class="stat-top"><span class="stat-icon bg-danger-subtle text-danger"><iconify-icon icon="solar:hand-money-bold-duotone"></iconify-icon></span><span class="stat-label">Customers owe</span></div>
                <div class="stat-value text-danger">{{ money($customerDue) }}</div>
                <div class="stat-sub text-muted">{{ $unpaid->count() }} customers not paid</div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <a href="{{ route('reports.stock') }}" class="card stat-card">
                <div class="stat-top"><span class="stat-icon bg-warning-subtle text-warning"><iconify-icon icon="solar:box-bold-duotone"></iconify-icon></span><span class="stat-label">Stock</span></div>
                <div class="stat-value">{{ qty($stockPairs) }} <small>pairs</small></div>
                <div class="stat-sub text-muted">Worth {{ money($stockValue) }}</div>
            </a>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-3 dash-row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div><h5 class="card-title mb-0">Sales & profit</h5><div class="small text-muted">Last 30 days</div></div>
                    <div class="chart-legend">
                        <span><i style="background:#111827"></i>Sales <b>{{ money($chart30['sales']) }}</b></span>
                        <span><i style="background:#22c55e"></i>Profit <b>{{ money($chart30['profit']) }}</b></span>
                    </div>
                </div>
                <div class="card-body pt-2"><div id="sales-area" data-chart='@json($chart)'></div></div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">How customers paid</h5><div class="small text-muted">This month</div></div>
                <div class="card-body d-flex flex-column">
                    <div id="methods-donut" data-chart='@json($methods)'></div>
                    <ul class="method-legend mt-auto">
                        @foreach ($methods as $i => $m)
                            <li><span><i class="m{{ $i }}"></i>{{ $m['label'] }}</span><b class="money">{{ money($m['value']) }}</b></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent sales + best sellers --}}
    <div class="row g-3 dash-row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title mb-0">Recent sales</h5><a href="{{ route('invoices.index') }}" class="link-more">All sales</a></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack table-hover mb-0">
                            <thead><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Status</th><th class="text-end">Amount</th></tr></thead>
                            <tbody>
                                @forelse ($recent as $inv)
                                    <tr>
                                        <td class="td-title"><a href="{{ route('invoices.show', $inv) }}" class="doc-link">{{ $inv->number }}</a></td>
                                        <td data-label="Customer"><span class="avatar-initial">{{ strtoupper(substr($inv->customer->name, 0, 1)) }}</span>{{ $inv->customer->name }}</td>
                                        <td data-label="Date" class="text-muted">{{ $inv->date->format('d M') }}</td>
                                        <td data-label="Status">{!! status_badge($inv->payment_status) !!}</td>
                                        <td data-label="Amount" class="text-end money fw-bold">{{ money($inv->total - $inv->returned_amount) }}</td>
                                    </tr>
                                @empty
                                    <x-empty :cols="5" text="No sales yet." />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center"><div><h5 class="card-title mb-0">Best sellers</h5><div class="small text-muted">Last 30 days</div></div><a href="{{ route('reports.products') }}" class="link-more">More</a></div>
                <ul class="list-group list-group-flush">
                    @forelse ($top as $t)
                        <li class="list-group-item">
                            <a href="{{ route('products.show', $t->id) }}" class="d-flex align-items-center gap-3">
                                @if ($t->image_path)<img src="{{ asset($t->image_path) }}" class="thumb" style="width: 46px; height: 46px" alt="">@else<span class="thumb thumb-empty" style="width: 46px; height: 46px"><iconify-icon icon="ph:sneaker"></iconify-icon></span>@endif
                                <span class="flex-grow-1 min-w-0">
                                    <span class="d-flex justify-content-between"><b class="text-truncate">{{ $t->name }}</b><b>{{ qty($t->pairs) }} <small class="text-muted fw-normal">prs</small></b></span>
                                    <span class="progress mt-2" style="height: 6px"><span class="progress-bar bg-dark" style="width: {{ round($t->pairs / $maxTop * 100) }}%"></span></span>
                                </span>
                            </a>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">No sales in the last 30 days.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- Not paid + low stock --}}
    <div class="row g-3 dash-row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><span class="pulse-dot"></span>Not paid yet</h5>
                    <a href="{{ route('dues') }}" class="link-more">All dues</a>
                </div>
                <ul class="list-group list-group-flush dash-list">
                    @forelse ($unpaid->take(6) as $row)
                        @php $c = $row['customer']; $level = $row['days'] > 60 ? 'danger' : ($row['days'] > 30 ? 'warning' : 'secondary'); @endphp
                        <li class="list-group-item d-flex justify-content-between align-items-center gap-2">
                            <span class="d-flex align-items-center gap-2 min-w-0">
                                <span class="avatar-initial lg">{{ strtoupper(substr($c->name, 0, 1)) }}</span>
                                <span class="min-w-0"><a href="{{ route('customers.show', $c) }}" class="fw-bold d-block text-truncate">{{ $c->name }}</a>
                                    <span class="badge bg-{{ $level }}-subtle text-{{ $level }}">{{ $row['days'] === 0 ? 'Today' : $row['days'] . ' days' }}</span> <span class="small text-muted">{{ $c->city }}</span></span>
                            </span>
                            <span class="text-end flex-shrink-0">
                                <b class="money text-danger d-block">{{ money($row['due']) }}</b>
                                <a href="{{ route('payments.receive', ['customer' => $c->id]) }}" class="small fw-semibold">Get paid →</a>
                            </span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4"><iconify-icon icon="solar:check-circle-outline" class="fs-32 text-success d-block mx-auto mb-1"></iconify-icon>Everyone has paid.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title mb-0">Running low</h5><a href="{{ route('reports.stock', ['show' => 'low']) }}" class="link-more">Stock</a></div>
                <ul class="list-group list-group-flush dash-list">
                    @forelse ($lowStock as $p)
                        <li class="list-group-item">
                            <a href="{{ route('products.show', $p) }}" class="d-flex align-items-center gap-3">
                                <x-thumb :product="$p" :size="46" />
                                <span class="flex-grow-1 min-w-0"><b class="d-block text-truncate">{{ $p->name }}</b><span class="small text-muted">{{ $p->code }}</span></span>
                                <span class="badge {{ $p->totalStock() <= 0 ? 'bg-danger text-white' : 'bg-danger-subtle text-danger' }}">{{ $p->totalStock() <= 0 ? 'Out of stock' : $p->totalStock() . ' pairs' }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">Stock is fine.</li>
                    @endforelse
                    @foreach ($chequesDue as $ch)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><span class="badge {{ $ch->direction === 'received' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">Cheque {{ $ch->direction === 'received' ? 'in' : 'out' }}</span> #{{ $ch->cheque_no }} <small class="text-muted">{{ $ch->cheque_date->format('d M') }}</small></span>
                            <b class="money">{{ money($ch->amount) }}</b>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/pages/dashboard-charts.js'])
@endsection

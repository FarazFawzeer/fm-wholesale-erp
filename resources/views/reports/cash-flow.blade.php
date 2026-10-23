@extends('layouts.vertical', ['subtitle' => 'Cash Flow'])

@section('content')
    <x-page-header title="Cash Flow" :crumbs="['Reports' => route('reports.index'), 'Cash flow']">
        <a href="{{ route('reports.expected') }}" class="btn btn-soft-primary">Expected payments</a>
    </x-page-header>

    @include('reports.partials.range', ['from' => $from, 'to' => $to])

    @php $in = $byType->sum('in'); $out = $byType->sum('out'); $max = max(1, $byWeek->max('in'), $byWeek->max('out')); @endphp
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-4"><x-kpi label="Money in" :value="money($in)" icon="solar:arrow-down-outline" color="success" /></div>
        <div class="col-6 col-md-4"><x-kpi label="Money out" :value="money($out)" icon="solar:arrow-up-outline" color="danger" /></div>
        <div class="col-12 col-md-4"><x-kpi label="Net cash flow" :value="money($in - $out)" icon="solar:wallet-money-outline" :color="$in - $out >= 0 ? 'success' : 'danger'" /></div>
    </div>

    <div class="row">
        <div class="col-xl-7">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">By week</h5></div>
                <div class="card-body">
                    @forelse ($byWeek as $w)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small"><span class="fw-semibold">Week of {{ $w['week'] }}</span><span>Net <strong class="money {{ $w['in'] - $w['out'] < 0 ? 'text-danger' : 'text-success' }}">{{ money($w['in'] - $w['out']) }}</strong></span></div>
                            <div class="d-flex align-items-center gap-2 mt-1"><span class="small text-muted" style="width: 34px">In</span><div class="progress flex-grow-1" style="height: 8px"><div class="progress-bar bg-success" style="width: {{ $w['in'] / $max * 100 }}%"></div></div><span class="small money" style="width: 120px; text-align: right">{{ money($w['in']) }}</span></div>
                            <div class="d-flex align-items-center gap-2 mt-1"><span class="small text-muted" style="width: 34px">Out</span><div class="progress flex-grow-1" style="height: 8px"><div class="progress-bar bg-danger" style="width: {{ $w['out'] / $max * 100 }}%"></div></div><span class="small money" style="width: 120px; text-align: right">{{ money($w['out']) }}</span></div>
                        </div>
                    @empty
                        <div class="text-muted small">No money movements in this period.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">By type</h5></div>
                <ul class="list-group list-group-flush">
                    @foreach ($byType as $t)
                        <li class="list-group-item d-flex justify-content-between"><span>{{ $t['label'] }}</span>
                            <span>@if ($t['in'])<strong class="money text-success">+{{ money($t['in']) }}</strong>@endif @if ($t['out'])<strong class="money text-danger">-{{ money($t['out']) }}</strong>@endif</span></li>
                    @endforeach
                </ul>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Expenses by category</h5></div>
                <ul class="list-group list-group-flush">
                    @forelse ($expenses as $cat => $amt)
                        <li class="list-group-item d-flex justify-content-between"><span>{{ $cat }}</span><span class="money">{{ money($amt) }}</span></li>
                    @empty
                        <li class="list-group-item small text-muted">No expenses.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection

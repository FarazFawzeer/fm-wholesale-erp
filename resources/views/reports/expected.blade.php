@extends('layouts.vertical', ['subtitle' => 'Expected Payments'])

@section('content')
    <x-page-header title="Expected Incoming & Outgoing Payments" :crumbs="['Reports' => route('reports.index'), 'Expected']">
        <div class="btn-group">
            @foreach ([14, 30, 60, 90] as $d)<a href="?days={{ $d }}" class="btn btn-sm {{ $days == $d ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $d }} days</a>@endforeach
        </div>
    </x-page-header>

    @php $in = $items->where('dir', 'in')->sum('amount'); $out = $items->where('dir', 'out')->sum('amount'); @endphp
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><x-kpi label="Cash & bank now" :value="money($cash)" icon="solar:wallet-outline" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Expected in" :value="money($in)" icon="solar:arrow-down-outline" color="success" sub="Customer dues + cheques" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Expected out" :value="money($out)" icon="solar:arrow-up-outline" color="danger" sub="Supplier dues + issued cheques" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Projected position" :value="money($cash + $in - $out)" icon="solar:graph-up-outline" :color="$cash + $in - $out >= 0 ? 'success' : 'danger'" :sub="'In ' . $days . ' days if all is collected'" /></div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">By week</h5></div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Period</th><th class="text-end">Incoming</th><th class="text-end">Outgoing</th><th class="text-end">Net</th></tr></thead>
                    <tbody>
                        @foreach ($weeks as $w)
                            <tr>
                                <td class="td-title">{{ $w['label'] }}</td>
                                <td data-label="In" class="text-end money text-success">{{ money($w['in']) }}</td>
                                <td data-label="Out" class="text-end money text-danger">{{ money($w['out']) }}</td>
                                <td data-label="Net" class="text-end money fw-semibold">{{ money($w['in'] - $w['out']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Details</h5></div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Date</th><th>Item</th><th>Party</th><th class="text-end">In</th><th class="text-end">Out</th></tr></thead>
                    <tbody>
                        @forelse ($items as $i)
                            <tr>
                                <td data-label="Date" class="{{ $i['overdue'] ? 'text-danger fw-semibold' : '' }}">{{ $i['date']->format('d M Y') }}</td>
                                <td class="td-title"><a href="{{ $i['url'] }}">{{ $i['what'] }}</a></td>
                                <td data-label="Party">{{ $i['party'] }}</td>
                                <td data-label="In" class="text-end money text-success">{{ $i['dir'] === 'in' ? money($i['amount']) : '' }}</td>
                                <td data-label="Out" class="text-end money text-danger">{{ $i['dir'] === 'out' ? money($i['amount']) : '' }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="5" text="Nothing expected in this period." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

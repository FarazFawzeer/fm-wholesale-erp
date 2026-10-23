@extends('layouts.vertical', ['subtitle' => 'Outstanding & Ageing'])

@section('content')
    <x-page-header title="Outstanding & Ageing" :crumbs="['Reports' => route('reports.index'), 'Outstanding']">
        <button onclick="window.print()" class="btn btn-light">Print</button>
    </x-page-header>

    <div class="row g-3 mb-3">
        @foreach ($buckets as $k => $l)
            <div class="col-6 col-md"><x-kpi :label="$l" :value="money($rows->sum($k))" icon="solar:alarm-outline" :color="['current' => 'success', 'd30' => 'warning', 'd60' => 'warning', 'd90' => 'danger', 'd90p' => 'danger'][$k]" /></div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Customer</th>@foreach ($buckets as $l)<th class="text-end">{{ $l }}</th>@endforeach<th class="text-end">Total</th></tr></thead>
                    <tbody>
                        @forelse ($rows as $r)
                            <tr>
                                <td class="td-title"><a href="{{ route('reports.statement', ['customer_id' => $r['customer']->id]) }}" class="doc-link">{{ $r['customer']->name }}</a></td>
                                @foreach ($buckets as $k => $l)
                                    <td data-label="{{ $l }}" class="text-end money {{ $k !== 'current' && $r[$k] > 0 ? 'text-danger' : '' }}">{{ $r[$k] > 0 ? money($r[$k]) : '-' }}</td>
                                @endforeach
                                <td data-label="Total" class="text-end money fw-bold">{{ money($r['total']) }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="7" text="Nothing outstanding." />
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr><td>Total</td>@foreach ($buckets as $k => $l)<td class="text-end money">{{ money($rows->sum($k)) }}</td>@endforeach<td class="text-end money">{{ money($rows->sum('total')) }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

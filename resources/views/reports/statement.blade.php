@extends('layouts.vertical', ['subtitle' => 'Customer Statement'])

@section('content')
    <x-page-header title="Customer Statement" :crumbs="['Reports' => route('reports.index'), 'Statement']" />

    <div class="card no-print">
        <div class="card-body">
            <form class="filter-bar">
                <div class="filter-grow"><label class="form-label small mb-1">Customer</label>
                    <select name="customer_id" class="form-select" data-choices required><option value="">Select customer</option>@foreach ($customers as $c)<option value="{{ $c->id }}" @selected($customer?->id == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
                <div><label class="form-label small mb-1">From</label><input type="date" name="from" value="{{ $from }}" class="form-control"></div>
                <div><label class="form-label small mb-1">To</label><input type="date" name="to" value="{{ $to }}" class="form-control"></div>
                <button class="btn btn-primary">Show</button>
                @if ($customer)<button type="button" class="btn btn-light" onclick="window.print()">Print</button>@endif
            </form>
        </div>
    </div>

    @if ($customer)
        @php
            $closing = $opening + $rows->sum('debit') - $rows->sum('credit');
            $wa = $customer->whatsappNumber();
        @endphp
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between gap-2">
                <div><h5 class="mb-0">{{ $customer->business_name ?: $customer->name }}</h5><div class="small text-muted">{{ $customer->code }} &middot; {{ $customer->address }} {{ $customer->city }}</div></div>
                <div class="text-end"><div class="small text-muted">{{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</div><div class="fs-5 fw-bold money">Balance {{ money($closing) }}</div>
                    @if ($wa)<a target="_blank" rel="noopener" class="btn btn-sm btn-success mt-1 no-print" href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Dear ' . $customer->name . ', your account balance with ' . setting('company_name') . ' as at ' . \Carbon\Carbon::parse($to)->format('d M Y') . ' is ' . money($closing) . '. Thank you.') }}">Send balance on WhatsApp</a>@endif</div>
            </div>
            <div class="card-body table-wrap p-0">
                <div class="table-responsive">
                    <table class="table erp-table stack mb-0">
                        <thead><tr><th>Date</th><th>Reference</th><th>Description</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th></tr></thead>
                        <tbody>
                            <tr class="table-light"><td class="td-title" colspan="5">Opening balance</td><td data-label="Balance" class="text-end money fw-semibold">{{ money($opening) }}</td></tr>
                            @php $run = $opening; @endphp
                            @forelse ($rows as $r)
                                @php $run += $r['debit'] - $r['credit']; @endphp
                                <tr>
                                    <td data-label="Date">{{ $r['date']->format('d M Y') }}</td>
                                    <td class="td-title"><a href="{{ $r['url'] }}">{{ $r['ref'] }}</a></td>
                                    <td data-label="Description">{{ $r['desc'] }}</td>
                                    <td data-label="Debit" class="text-end money">{{ $r['debit'] ? money($r['debit']) : '' }}</td>
                                    <td data-label="Credit" class="text-end money text-success">{{ $r['credit'] ? money($r['credit']) : '' }}</td>
                                    <td data-label="Balance" class="text-end money fw-semibold">{{ money($run) }}</td>
                                </tr>
                            @empty
                                <x-empty :cols="6" text="No transactions in this period." />
                            @endforelse
                        </tbody>
                        <tfoot class="table-light fw-bold"><tr><td colspan="3">Closing balance</td><td class="text-end money">{{ money($rows->sum('debit')) }}</td><td class="text-end money">{{ money($rows->sum('credit')) }}</td><td class="text-end money">{{ money($closing) }}</td></tr></tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

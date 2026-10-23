@extends('layouts.vertical', ['subtitle' => 'Daily Cashbook'])

@section('content')
    @php $d = \Carbon\Carbon::parse($date); @endphp
    <x-page-header title="Daily Cashbook" :crumbs="['Cash & Bank', 'Cashbook']">
        <a href="{{ route('cashbook', ['date' => $d->copy()->subDay()->toDateString()]) }}" class="btn btn-light">&larr; Prev</a>
        <form><input type="date" name="date" value="{{ $date }}" class="form-control" onchange="this.form.submit()"></form>
        <a href="{{ route('cashbook', ['date' => $d->copy()->addDay()->toDateString()]) }}" class="btn btn-light">Next &rarr;</a>
        <button onclick="window.print()" class="btn btn-light">Print</button>
    </x-page-header>

    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">{{ $d->format('l, d M Y') }} - balances</h5></div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Account</th><th class="text-end">Opening</th><th class="text-end">Money in</th><th class="text-end">Money out</th><th class="text-end">Closing</th></tr></thead>
                    <tbody>
                        @foreach ($accounts as $a)
                            <tr>
                                <td class="td-title"><a href="{{ route('accounts.show', $a['account']) }}" class="doc-link">{{ $a['account']->name }}</a></td>
                                <td data-label="Opening" class="text-end money">{{ money($a['opening']) }}</td>
                                <td data-label="In" class="text-end money text-success">{{ money($a['in']) }}</td>
                                <td data-label="Out" class="text-end money text-danger">{{ money($a['out']) }}</td>
                                <td data-label="Closing" class="text-end money fw-bold">{{ money($a['closing']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr><td>Total</td><td class="text-end money">{{ money($accounts->sum('opening')) }}</td><td class="text-end money text-success">{{ money($accounts->sum('in')) }}</td><td class="text-end money text-danger">{{ money($accounts->sum('out')) }}</td><td class="text-end money">{{ money($accounts->sum('closing')) }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Transactions</h5></div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Description</th><th>Account</th><th>Type</th><th class="text-end">In</th><th class="text-end">Out</th></tr></thead>
                    <tbody>
                        @forelse ($rows as $r)
                            <tr>
                                <td class="td-title">{{ $r->description }}</td>
                                <td data-label="Account">{{ $r->account->name }}</td>
                                <td data-label="Type">{{ ucwords(str_replace('_', ' ', $r->type)) }}</td>
                                <td data-label="In" class="text-end money text-success">{{ $r->amount > 0 ? money($r->amount) : '' }}</td>
                                <td data-label="Out" class="text-end money text-danger">{{ $r->amount < 0 ? money(-$r->amount) : '' }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="5" text="No money movements on this day." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

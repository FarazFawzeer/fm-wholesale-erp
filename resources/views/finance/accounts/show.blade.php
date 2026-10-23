@extends('layouts.vertical', ['subtitle' => $account->name])

@section('content')
    <x-page-header :title="$account->name" :crumbs="['Accounts' => route('accounts.index'), $account->name]" />

    <div class="card">
        <div class="card-body border-bottom">
            <form class="filter-bar">
                <input type="date" name="from" value="{{ $from }}" class="form-control">
                <input type="date" name="to" value="{{ $to }}" class="form-control">
                <button class="btn btn-light">Show</button>
                <span class="ms-auto">Current balance <strong class="money">{{ money($account->balance()) }}</strong></span>
            </form>
        </div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Date</th><th>Description</th><th class="text-end">In</th><th class="text-end">Out</th><th class="text-end">Balance</th></tr></thead>
                    <tbody>
                        <tr class="table-light"><td class="td-title" colspan="4">Opening balance {{ \Carbon\Carbon::parse($from)->format('d M Y') }}</td><td data-label="Balance" class="text-end money fw-semibold">{{ money($opening) }}</td></tr>
                        @php $run = $opening; @endphp
                        @forelse ($rows as $r)
                            @php $run += $r->amount; @endphp
                            <tr>
                                <td data-label="Date">{{ $r->date->format('d M Y') }}</td>
                                <td class="td-title">{{ $r->description }}</td>
                                <td data-label="In" class="text-end money text-success">{{ $r->amount > 0 ? money($r->amount) : '' }}</td>
                                <td data-label="Out" class="text-end money text-danger">{{ $r->amount < 0 ? money(-$r->amount) : '' }}</td>
                                <td data-label="Balance" class="text-end money fw-semibold">{{ money($run) }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="5" text="No transactions in this period." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

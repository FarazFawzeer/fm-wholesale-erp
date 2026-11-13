@extends('layouts.vertical', ['subtitle' => 'Statement'])

@section('content')
    <x-page-header :title="'Statement - ' . $customer->name" :crumbs="['Customers' => route('customers.index'), $customer->name => route('customers.show', $customer), 'Statement']">
        <button type="button" class="btn btn-soft-secondary" onclick="window.print()"><iconify-icon icon="solar:printer-outline" class="align-middle me-1"></iconify-icon>Print</button>
    </x-page-header>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Date</th><th>Details</th><th class="text-end">Bill</th><th class="text-end">Paid / returned</th><th class="text-end">Balance</th></tr></thead>
                    <tbody>
                        @php $bal = $customer->opening_balance; @endphp
                        @if ($customer->opening_balance > 0)
                            <tr><td class="td-title">Start</td><td data-label="Details">Old balance</td><td data-label="Bill" class="text-end money">{{ money($bal, false) }}</td><td></td><td data-label="Balance" class="text-end money fw-semibold">{{ money($bal, false) }}</td></tr>
                        @endif
                        @forelse ($rows as $r)
                            @php $bal += $r['debit'] - $r['credit']; @endphp
                            <tr>
                                <td class="td-title">{{ $r['date']->format('d M Y') }}</td>
                                <td data-label="Details"><a href="{{ $r['url'] }}">{{ $r['text'] }}</a></td>
                                <td data-label="Bill" class="text-end money">{{ $r['debit'] ? money($r['debit'], false) : '' }}</td>
                                <td data-label="Paid" class="text-end money text-success">{{ $r['credit'] ? money($r['credit'], false) : '' }}</td>
                                <td data-label="Balance" class="text-end money fw-semibold">{{ money($bal, false) }}</td>
                            </tr>
                        @empty
                            <x-empty :cols="5" text="Nothing yet." />
                        @endforelse
                    </tbody>
                    <tfoot><tr class="fw-bold"><td colspan="4">Balance now</td><td class="text-end money {{ $bal > 0.009 ? 'text-danger' : '' }}">{{ money($bal) }}</td></tr></tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

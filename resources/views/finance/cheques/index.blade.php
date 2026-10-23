@extends('layouts.vertical', ['subtitle' => 'Cheques'])

@section('content')
    <x-page-header title="Cheque Management" :crumbs="['Payments', 'Cheques']" />

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link {{ $direction === 'received' ? 'active' : '' }}" href="{{ route('cheques.index', ['direction' => 'received']) }}">Received from customers</a></li>
        <li class="nav-item"><a class="nav-link {{ $direction === 'issued' ? 'active' : '' }}" href="{{ route('cheques.index', ['direction' => 'issued']) }}">Issued to suppliers</a></li>
    </ul>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><x-kpi label="Not yet cleared" :value="money($summary['pending'])" icon="solar:card-outline" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Post-dated" :value="money($summary['pdc'])" icon="solar:calendar-outline" color="info" sub="Future dated" /></div>
        <div class="col-6 col-lg-3"><x-kpi :label="$direction === 'received' ? 'Due to deposit' : 'Due to clear'" :value="money($summary['due'])" icon="solar:alarm-outline" color="warning" sub="Date reached" /></div>
        <div class="col-6 col-lg-3"><x-kpi label="Returned / bounced" :value="money($summary['bounced'])" icon="solar:danger-triangle-outline" color="danger" /></div>
    </div>

    <ul class="nav nav-pills nav-tabs-scroll mb-3 gap-1">
        @foreach (['' => 'All', 'open' => 'Open', 'pending' => 'Pending', 'deposited' => 'Deposited', 'cleared' => 'Cleared', 'bounced' => 'Bounced'] as $k => $l)
            <li class="nav-item"><a class="nav-link {{ request('status', '') === $k ? 'active' : '' }}" href="{{ route('cheques.index', array_filter(['direction' => $direction, 'status' => $k])) }}">{{ $l }}</a></li>
        @endforeach
    </ul>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Cheque</th><th>{{ $direction === 'received' ? 'Customer' : 'Supplier' }}</th><th>Cheque date</th><th>Status</th><th class="text-end">Amount</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($cheques as $ch)
                            @php $days = (int) now()->startOfDay()->diffInDays($ch->cheque_date, false); @endphp
                            <tr>
                                <td class="td-title"><strong>#{{ $ch->cheque_no }}</strong> <span class="small text-muted">{{ $ch->bank }} {{ $ch->branch }}</span>
                                    @if ($ch->payment)<div class="small"><a href="{{ route('payments.show', $ch->payment) }}">{{ $ch->payment->number }}</a></div>@endif</td>
                                <td data-label="Party">{{ $ch->party()?->name }}</td>
                                <td data-label="Cheque date">
                                    {{ $ch->cheque_date->format('d M Y') }}
                                    @if ($ch->isOpen())
                                        <div class="small {{ $days < 0 ? 'text-danger' : ($days <= 3 ? 'text-warning' : 'text-muted') }}">{{ $days > 0 ? 'in ' . $days . ' days (PDC)' : ($days === 0 ? 'today' : abs($days) . ' days ago') }}</div>
                                    @endif
                                </td>
                                <td data-label="Status">{!! status_badge($ch->status) !!}@if ($ch->status_date)<div class="small text-muted">{{ $ch->status_date->format('d M') }}</div>@endif</td>
                                <td data-label="Amount" class="text-end money fw-semibold">{{ money($ch->amount) }}</td>
                                <td class="td-actions text-end text-nowrap">
                                    @if ($ch->isOpen())
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-popper-config='{"strategy":"fixed"}'>Update</button>
                                            <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 260px">
                                                <form method="POST" action="{{ route('cheques.status', $ch) }}">
                                                    @csrf
                                                    <select name="status" class="form-select form-select-sm mb-2">
                                                        @if ($direction === 'received' && $ch->status === 'pending')<option value="deposited">Deposited to bank</option>@endif
                                                        <option value="cleared">Cleared</option>
                                                        <option value="bounced">Returned / bounced</option>
                                                        <option value="cancelled">Cancelled</option>
                                                    </select>
                                                    <select name="account_id" class="form-select form-select-sm mb-2">@foreach ($accounts as $a)<option value="{{ $a->id }}" @selected($ch->account_id == $a->id)>{{ $a->name }}</option>@endforeach</select>
                                                    <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" class="form-control form-control-sm mb-2">
                                                    <input name="remarks" class="form-control form-control-sm mb-2" placeholder="Remarks">
                                                    <button class="btn btn-sm btn-primary w-100">Save</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <x-empty :cols="6" text="No cheques." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($cheques->hasPages())<div class="card-footer">{{ $cheques->links() }}</div>@endif
    </div>
@endsection

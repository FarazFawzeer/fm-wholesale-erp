@extends('layouts.vertical', ['subtitle' => 'Cheques'])

@section('content')
    <x-page-header title="Cheques" />

    <div class="row g-3 mb-3">
        <div class="col-6 col-md-4"><x-kpi label="Customer cheques to clear" :value="money($pendingIn)" icon="solar:card-recive-outline" color="info" :href="route('cheques', ['direction' => 'received'])" /></div>
        <div class="col-6 col-md-4"><x-kpi label="Our cheques to clear" :value="money($pendingOut)" icon="solar:card-send-outline" color="warning" :href="route('cheques', ['direction' => 'issued'])" /></div>
    </div>

    <form class="filter-bar mb-3" method="GET">
        <select name="direction" class="form-select" onchange="this.form.submit()">
            <option value="">From customers & to suppliers</option>
            <option value="received" @selected(request('direction') === 'received')>From customers</option>
            <option value="issued" @selected(request('direction') === 'issued')>Given to suppliers</option>
        </select>
        <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="pending" @selected($status === 'pending')>Not cleared yet</option>
            <option value="cleared" @selected($status === 'cleared')>Cleared</option>
            <option value="bounced" @selected($status === 'bounced')>Returned (bounced)</option>
            <option value="all" @selected($status === 'all')>All</option>
        </select>
    </form>

    <div class="card">
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Cheque</th><th>Cheque date</th><th>From / to</th><th class="text-end">Amount</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($cheques as $ch)
                            <tr>
                                <td class="td-title"><span class="badge {{ $ch->direction === 'received' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} me-1">{{ $ch->direction === 'received' ? 'IN' : 'OUT' }}</span><b>#{{ $ch->cheque_no }}</b> <small class="text-muted">{{ $ch->bank }}</small></td>
                                <td data-label="Cheque date" class="{{ $ch->isPending() && $ch->cheque_date->isPast() ? 'text-danger fw-semibold' : '' }}">{{ $ch->cheque_date->format('d M Y') }}</td>
                                <td data-label="From / to">{{ $ch->party()?->name }}</td>
                                <td data-label="Amount" class="text-end money fw-semibold">{{ money($ch->amount) }}</td>
                                <td data-label="Status">{!! status_badge($ch->status) !!}</td>
                                <td class="td-actions text-end">
                                    @if ($ch->isPending())
                                        <form method="POST" action="{{ route('cheques.status', $ch) }}" class="d-inline">@csrf<input type="hidden" name="status" value="cleared"><button class="btn btn-sm btn-soft-success">Cleared</button></form>
                                        <form method="POST" action="{{ route('cheques.status', $ch) }}" class="d-inline" data-confirm="Mark cheque #{{ $ch->cheque_no }} as returned? The amount will be owed again.">@csrf<input type="hidden" name="status" value="bounced"><button class="btn btn-sm btn-soft-danger">Returned</button></form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <x-empty :cols="6" text="No cheques here." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{ $cheques->links() }}
@endsection

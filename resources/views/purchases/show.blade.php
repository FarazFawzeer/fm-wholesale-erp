@extends('layouts.vertical', ['subtitle' => $purchase->number])

@section('content')
    <x-page-header :title="'Purchase ' . $purchase->number" :crumbs="['Purchases' => route('purchases.index'), $purchase->number]">
        @if ($purchase->balance() > 0)
            <a href="{{ route('payments.pay', ['supplier' => $purchase->supplier_id, 'purchase' => $purchase->id]) }}" class="btn btn-warning"><iconify-icon icon="solar:card-send-outline" class="align-middle me-1"></iconify-icon>Pay</a>
        @endif
        <a href="{{ route('returns.supplier', $purchase) }}" class="btn btn-soft-secondary"><iconify-icon icon="solar:undo-left-round-outline" class="align-middle me-1"></iconify-icon>Return to supplier</a>
    </x-page-header>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between flex-wrap gap-2">
                    <div><a href="{{ route('suppliers.show', $purchase->supplier) }}" class="fw-bold fs-5">{{ $purchase->supplier->name }}</a>
                        <div class="small text-muted">{{ $purchase->date->format('d M Y') }}@if ($purchase->supplier_bill_no) &middot; Supplier bill {{ $purchase->supplier_bill_no }}@endif</div></div>
                    <div>{!! status_badge($purchase->status()) !!}</div>
                </div>
                <div class="card-body">
                    @foreach ($groups as $items)
                        @php $p = $items->first()->product; @endphp
                        <div class="doc-line">
                            <x-thumb :product="$p" :size="52" />
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold">{{ $p->name }} <span class="text-muted small">{{ $p->sizeLabel() ? 'Size ' . $p->sizeLabel() : '' }}</span></div>
                                <div class="colour-list">
                                    @foreach ($items as $it)<x-colour-chip :name="$it->colour->colour" :qty="$it->qty" />@if ($it->returned_qty)<small class="text-danger me-2">-{{ $it->returned_qty }} returned</small>@endif @endforeach
                                </div>
                                <div class="small text-muted">{{ $items->sum('qty') }} pairs &times; {{ money($items->first()->unit_price) }}</div>
                            </div>
                            <strong class="money">{{ money($items->sum('line_total')) }}</strong>
                        </div>
                    @endforeach

                    <div class="totals-box ms-auto mt-3" style="max-width: 340px">
                        <div class="row-total"><span>Amount ({{ $purchase->totalPairs() }} pairs)</span><span class="money">{{ money($purchase->subtotal) }}</span></div>
                        @if ($purchase->discount > 0)<div class="row-total"><span>Discount</span><span class="money">- {{ money($purchase->discount) }}</span></div>@endif
                        @if ($purchase->other_costs > 0)<div class="row-total"><span>Transport / other</span><span class="money">{{ money($purchase->other_costs) }}</span></div>@endif
                        <div class="row-total grand"><span>Total</span><span class="money">{{ money($purchase->total) }}</span></div>
                        @if ($purchase->returned_amount > 0)<div class="row-total text-danger"><span>Returned</span><span class="money">- {{ money($purchase->returned_amount) }}</span></div>@endif
                        <div class="row-total"><span>Paid</span><span class="money text-success">{{ money($purchase->paid_amount) }}</span></div>
                        <div class="row-total fw-bold"><span>Balance</span><span class="money {{ $purchase->balance() > 0 ? 'text-warning' : '' }}">{{ money($purchase->balance()) }}</span></div>
                    </div>
                    @if ($purchase->notes)<div class="small text-muted mt-3">Note: {{ $purchase->notes }}</div>@endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Payments</h5></div>
                <ul class="list-group list-group-flush">
                    @forelse ($purchase->allocations as $a)
                        <li class="list-group-item d-flex justify-content-between">
                            <span><a href="{{ route('payments.show', $a->payment) }}">{{ $a->payment->number }}</a> <small class="text-muted">{{ $a->payment->date->format('d M') }} &middot; {{ $a->payment->methodSummary() }}</small></span>
                            <strong class="money">{{ money($a->amount) }}</strong>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">Nothing paid yet.</li>
                    @endforelse
                </ul>
            </div>
            @if ($purchase->returns->count())
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Returned to supplier</h5></div>
                    <div class="list-group list-group-flush">
                        @foreach ($purchase->returns as $r)
                            <a href="{{ route('returns.show', $r) }}" class="list-group-item list-group-item-action d-flex justify-content-between"><span><span class="doc-link">{{ $r->number }}</span> <small class="text-muted">{{ $r->date->format('d M') }} {{ $r->reason }}</small></span><strong class="money">- {{ money($r->total) }}</strong></a>
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="small text-muted">Saved by {{ $purchase->user?->name ?? '-' }} on {{ $purchase->created_at->format('d M Y H:i') }}</div>
        </div>
    </div>
@endsection

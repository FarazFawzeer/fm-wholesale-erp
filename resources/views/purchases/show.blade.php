@extends('layouts.vertical', ['subtitle' => $purchase->number])

@section('content')
    <x-page-header :title="'Purchase ' . $purchase->number" :crumbs="['Purchases' => route('purchases.index'), $purchase->number]">
        @if ($purchase->balance() > 0)
            <a href="{{ route('payments.pay', ['supplier' => $purchase->supplier_id, 'purchase' => $purchase->id]) }}" class="btn btn-warning"><iconify-icon icon="solar:card-send-outline" class="align-middle me-1"></iconify-icon>Pay</a>
        @endif
        <button type="button" class="btn btn-soft-danger" data-bs-toggle="modal" data-bs-target="#missing-modal"><iconify-icon icon="solar:box-minimalistic-outline" class="align-middle me-1"></iconify-icon>Missing / damaged</button>
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
                                <div class="fw-semibold">{{ $p->name }} <span class="text-muted small">{{ $p->sizeLabel() && ! $items->contains(fn ($it) => $it->colour->size) ? 'Size ' . $p->sizeLabel() : '' }}</span></div>
                                <div class="colour-list">
                                    <x-colour-chips :lines="$items" />@if ($items->sum('returned_qty'))<small class="text-danger ms-1">-{{ $items->sum('returned_qty') }} sent back</small>@endif
                                </div>
                                <div class="small text-muted">{{ $items->sum('qty') }} pairs &times; {{ money($items->first()->unit_price) }}</div>
                                @foreach ($items->where('missing_qty', '>', 0) as $it)
                                    <div class="missing-note"><iconify-icon icon="solar:danger-triangle-outline"></iconify-icon>{{ $it->colour->title }}: <b>{{ $it->missing_qty }}</b> not received &middot; {{ $it->missing_reason }}</div>
                                @endforeach
                            </div>
                            <strong class="money">{{ money($items->sum('line_total')) }}</strong>
                        </div>
                    @endforeach

                    <div class="totals-box ms-auto mt-3" style="max-width: 340px">
                        <div class="row-total"><span>Amount ({{ $purchase->totalPairs() }} pairs)</span><span class="money">{{ money($purchase->subtotal) }}</span></div>
                        @if ($purchase->missingPairs())
                            <div class="row-total text-danger small"><span>Received {{ $purchase->totalPairs() - $purchase->missingPairs() }} &middot; {{ $purchase->missingPairs() }} missing / damaged</span><span class="money">{{ money($purchase->items->sum(fn ($it) => $it->missing_qty * $it->unit_cost)) }}</span></div>
                        @endif
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

    {{-- Record pairs that did not arrive (found after saving the purchase) --}}
    <div class="modal fade" id="missing-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form method="POST" action="{{ route('purchases.missing', $purchase) }}" class="modal-content">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Missing / damaged pairs</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">You paid for these pairs but they did not arrive in good condition (damaged, kept by customs, missing from the box). Type how many on each line - they are taken out of stock and shown in <b>Reports &rsaquo; Missing pairs</b>.</p>
                    @foreach ($purchase->items as $it)
                        <div class="missing-row">
                            <div class="min-w-0 flex-grow-1"><b class="d-block text-truncate">{{ $it->product->name }}</b><small class="text-muted">{{ $it->colour->title }} &middot; billed {{ $it->qty }} pairs</small></div>
                            <input type="number" min="0" max="{{ $it->qty - $it->returned_qty }}" name="missing[{{ $it->id }}]" value="{{ $it->missing_qty ?: '' }}" class="form-control text-center" placeholder="0" inputmode="numeric" aria-label="Missing pairs">
                            <input name="reason[{{ $it->id }}]" value="{{ $it->missing_reason }}" class="form-control" list="missing-reasons" placeholder="Why?" aria-label="Reason">
                        </div>
                    @endforeach
                    <datalist id="missing-reasons">@foreach (\App\Models\PurchaseItem::REASONS as $r)<option value="{{ $r }}">@endforeach</datalist>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
            </form>
        </div>
    </div>
@endsection

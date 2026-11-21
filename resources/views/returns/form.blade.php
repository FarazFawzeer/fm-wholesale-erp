@extends('layouts.vertical', ['subtitle' => 'Return'])

@php
    $isCustomer = $type === 'customer';
    $party = $isCustomer ? $doc->customer : $doc->supplier;
    $action = $isCustomer ? route('returns.customer.store', $doc) : route('returns.supplier.store', $doc);
    $back = $isCustomer ? route('invoices.show', $doc) : route('purchases.show', $doc);
    // same discount share the system uses when it saves the return
    $factor = $doc->subtotal > 0 ? ($isCustomer ? $doc->total / $doc->subtotal : ($doc->subtotal - $doc->discount) / $doc->subtotal) : 1;
    $net = $doc->total - $doc->returned_amount;
    $groups = $doc->items->groupBy('product_id');
    $left = $doc->items->sum(fn ($it) => $it->qty - ($it->missing_qty ?? 0) - $it->returned_qty);
@endphp

@section('content')
    <x-page-header :title="$isCustomer ? 'Customer return' : 'Return to supplier'" :crumbs="['Returns' => route('returns.index'), ($isCustomer ? 'Invoice ' : 'Purchase ') . $doc->number => $back, 'Return']" />

    <form method="POST" action="{{ $action }}" data-return-form data-factor="{{ $factor }}" data-net="{{ $net }}" data-paid="{{ $doc->paid_amount }}">
        @csrf
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body ret-head">
                        <span class="ret-icon {{ $isCustomer ? 'in' : 'out' }}"><iconify-icon icon="{{ $isCustomer ? 'solar:box-minimalistic-outline' : 'solar:delivery-outline' }}"></iconify-icon></span>
                        <div class="min-w-0">
                            <div class="fw-bold fs-5 text-truncate">{{ $party->name }}</div>
                            <div class="small text-muted"><span class="mono">{{ $doc->number }}</span> &middot; {{ $doc->date->format('d M Y') }} &middot; {{ $isCustomer ? 'Sold' : 'Bought' }} {{ $doc->totalPairs() }} pairs</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center gap-2">
                        <div>
                            <h5 class="card-title mb-0">{{ $isCustomer ? 'How many pairs came back?' : 'How many pairs are you sending back?' }}</h5>
                            <div class="small text-muted">{{ $isCustomer ? 'They go back into stock.' : 'They are taken out of stock.' }}</div>
                        </div>
                        @if ($left > 0)<button type="button" class="btn btn-sm btn-light text-nowrap" data-return-all>Return all</button>@endif
                    </div>
                    <div class="card-body pt-1">
                        @foreach ($groups as $items)
                            @php $p = $items->first()->product; @endphp
                            <div class="ret-product">
                                <div class="ret-product-head">
                                    <x-thumb :product="$p" :size="44" />
                                    <div class="min-w-0">
                                        <span class="item-no">{{ $p->code }}</span>
                                        <div class="fw-semibold text-truncate">{{ $p->name }}</div>
                                    </div>
                                    <div class="ms-auto text-end small text-muted text-nowrap">{{ money($items->first()->unit_price) }}<br>per pair</div>
                                </div>
                                @foreach ($items as $it)
                                    @php
                                        $max = $it->qty - ($isCustomer ? 0 : $it->missing_qty) - $it->returned_qty;
                                        if (! $isCustomer) $max = min($max, max(0, (int) $it->colour->qty));
                                    @endphp
                                    <div class="ret-line {{ $max <= 0 ? 'is-done' : '' }}">
                                        <div class="min-w-0">
                                            <x-colour-chip :name="$it->colour->title" />
                                            <div class="small text-muted">
                                                {{ $isCustomer ? 'Sold' : 'Bought' }} {{ $it->qty }}
                                                @if (! $isCustomer && $it->missing_qty) &middot; <span class="text-danger">{{ $it->missing_qty }} not received</span>@endif
                                                @if ($it->returned_qty) &middot; <span class="text-danger">{{ $it->returned_qty }} already returned</span>@endif
                                                @if (! $isCustomer) &middot; {{ $it->colour->qty }} in stock @endif
                                            </div>
                                        </div>
                                        @if ($max > 0)
                                            <div class="qty-step">
                                                <button type="button" data-step="-1" aria-label="Less">&minus;</button>
                                                <input type="number" min="0" max="{{ $max }}" name="qty[{{ $it->id }}]" value="{{ old('qty.' . $it->id) }}" placeholder="0" inputmode="numeric" data-price="{{ $it->unit_price }}">
                                                <button type="button" data-step="1" aria-label="More">+</button>
                                            </div>
                                            <div class="small text-muted text-end ret-max">max {{ $max }}</div>
                                        @else
                                            <span class="badge bg-light text-muted">{{ $isCustomer || $it->qty - ($isCustomer ? 0 : $it->missing_qty) - $it->returned_qty <= 0 ? 'All returned' : 'No stock' }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card">
                    <div class="card-body row g-2">
                        <div class="col-md-4"><label class="form-label">Date</label><input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" class="form-control" required></div>
                        <div class="col-md-8"><label class="form-label">Reason</label>
                            <input name="reason" value="{{ old('reason') }}" class="form-control" list="return-reasons" placeholder="e.g. Damaged, wrong size">
                            <datalist id="return-reasons"><option value="Damaged"><option value="Wrong size"><option value="Wrong colour"><option value="Not sold"><option value="Quality problem"></datalist>
                            <div class="reason-chips">@foreach (['Damaged', 'Wrong size', 'Wrong colour', 'Not sold', 'Quality problem'] as $r)<button type="button" data-reason="{{ $r }}">{{ $r }}</button>@endforeach</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card ret-summary">
                    <div class="card-header"><h5 class="card-title mb-0">What will happen</h5></div>
                    <div class="card-body">
                        <div class="row-total"><span>Pairs {{ $isCustomer ? 'coming back' : 'going back' }}</span><b data-ret-pairs>0</b></div>
                        <div class="row-total"><span>Return amount</span><b class="money" data-ret-amount>{{ money(0) }}</b></div>
                        <hr class="my-2">
                        <div class="row-total"><span>Bill total now</span><span class="money">{{ money($net) }}</span></div>
                        <div class="row-total"><span>Bill after return</span><span class="money" data-ret-after>{{ money($net) }}</span></div>
                        <div class="row-total"><span>Paid on this bill</span><span class="money">{{ money($doc->paid_amount) }}</span></div>
                        <div class="ret-outcome mt-2" data-ret-outcome>Enter the pairs to see the result.</div>
                    </div>
                </div>
                <div class="sticky-actions d-flex gap-2 mb-3">
                    <a href="{{ $back }}" class="btn btn-light flex-fill">Cancel</a>
                    <button class="btn btn-primary flex-fill px-4" data-ret-save disabled>Save return</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('page-js')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-return-form]');
    if (!form) return;
    const isCustomer = @json($isCustomer);
    const factor = parseFloat(form.dataset.factor) || 1;
    const net = parseFloat(form.dataset.net) || 0;
    const paid = parseFloat(form.dataset.paid) || 0;
    const fmt = (n) => 'Rs ' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const inputs = [...form.querySelectorAll('input[name^="qty["]')];
    const $ = (s) => form.querySelector(s);

    const clamp = (inp) => {
        let v = parseInt(inp.value, 10);
        const max = parseInt(inp.max, 10);
        if (isNaN(v) || v < 0) v = 0;
        if (v > max) v = max;
        inp.value = v ? v : '';
    };

    const refresh = () => {
        let pairs = 0, amount = 0;
        inputs.forEach((inp) => {
            const q = parseInt(inp.value, 10) || 0;
            pairs += q;
            amount += Math.round(parseFloat(inp.dataset.price) * q * factor * 100) / 100;
            inp.closest('.ret-line').classList.toggle('is-picked', q > 0);
        });
        const after = Math.max(0, net - amount);
        $('[data-ret-pairs]').textContent = pairs;
        $('[data-ret-amount]').textContent = fmt(amount);
        $('[data-ret-after]').textContent = fmt(after);
        $('[data-ret-save]').disabled = pairs === 0;

        const out = $('[data-ret-outcome]');
        out.className = 'ret-outcome mt-2';
        if (!pairs) { out.textContent = 'Enter the pairs to see the result.'; return; }
        const extra = Math.round((paid - after) * 100) / 100;
        if (extra > 0.009) {
            out.classList.add('credit');
            out.innerHTML = isCustomer
                ? 'Customer already paid <b>' + fmt(extra) + '</b> more than the new bill. It will be used on their other unpaid bills, and anything left stays as <b>credit for the next bill</b>.'
                : 'You already paid <b>' + fmt(extra) + '</b> more than the new bill. It will be used on your other unpaid bills with this supplier, and anything left stays as <b>credit with the supplier</b>.';
        } else {
            out.classList.add('due');
            const due = Math.round((after - paid) * 100) / 100;
            out.innerHTML = (isCustomer ? 'Customer now owes <b>' : 'You now owe <b>') + fmt(Math.max(0, due)) + '</b> on this bill (was ' + fmt(Math.max(0, net - paid)) + ').';
        }
    };

    form.addEventListener('click', (e) => {
        const step = e.target.closest('[data-step]');
        if (step) {
            const inp = step.parentElement.querySelector('input');
            inp.value = (parseInt(inp.value, 10) || 0) + parseInt(step.dataset.step, 10);
            clamp(inp); refresh();
        }
        if (e.target.closest('[data-return-all]')) {
            const all = inputs.every((i) => parseInt(i.value, 10) === parseInt(i.max, 10));
            inputs.forEach((i) => (i.value = all ? '' : i.max));
            refresh();
        }
        const reason = e.target.closest('[data-reason]');
        if (reason) form.querySelector('[name=reason]').value = reason.dataset.reason;
    });
    inputs.forEach((inp) => {
        inp.addEventListener('input', refresh);
        inp.addEventListener('change', () => { clamp(inp); refresh(); });
    });
    form.addEventListener('submit', () => { $('[data-ret-save]').disabled = true; });
    refresh();
});
</script>
@endpush

@extends('layouts.vertical', ['subtitle' => 'Deliver ' . $invoice->number])

@section('content')
    <x-page-header :title="'Deliver ' . $invoice->number" :crumbs="['Invoices' => route('invoices.index'), $invoice->number => route('invoices.show', $invoice), 'Deliver']" />

    <form method="POST" action="{{ route('deliveries.store', $invoice) }}">
        @csrf
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                <div class="alert alert-info small">Enter what is going out now. Leave the rest for a later delivery (partial delivery). Stock is taken from <strong>{{ $invoice->warehouse->name }}</strong>.</div>
                @foreach ($byProduct as $rows)
                    @php $p = $rows->first()->product; $rows = $rows->sortBy(fn ($r) => (float) $r->variant->size); @endphp
                    <div class="line-card">
                        <div class="line-head"><div><div class="line-title">{{ $p->code }} - {{ $p->name }} {{ $p->colour }}</div></div>
                            <button type="button" class="btn btn-sm btn-light" onclick="this.closest('.line-card').querySelectorAll('input').forEach(i => i.value = i.max)">Fill all</button></div>
                        <div class="size-grid">
                            @foreach ($rows as $r)
                                @php $left = $r->qty - $r->delivered_qty; $stock = (int) $r->variant->stocks->firstWhere('warehouse_id', $invoice->warehouse_id)?->qty; @endphp
                                <div class="size-cell">
                                    <label>{{ $r->variant->size }}</label>
                                    <input type="number" min="0" max="{{ $left }}" name="qty[{{ $r->id }}]" value="{{ min($left, $stock) ?: '' }}" placeholder="0" class="form-control form-control-sm" @disabled($left <= 0)>
                                    <small>{{ $left }} due</small>
                                    <small class="{{ $stock < $left ? 'low' : '' }}">{{ $stock }} stock</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-xl-4 order-1 order-xl-2">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-2 small text-muted">Customer</div>
                        <div class="fw-semibold mb-3">{{ $invoice->customer->name }}<div class="small text-muted fw-normal">{{ $invoice->customer->address }} {{ $invoice->customer->city }}</div></div>
                        <div class="mb-3"><label class="form-label">Delivery date</label><input type="date" name="date" value="{{ now()->format('Y-m-d') }}" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Vehicle no</label><input name="vehicle_no" class="form-control" placeholder="e.g. LB-4521"></div>
                        <div class="mb-3"><label class="form-label">Received by</label><input name="received_by" class="form-control"></div>
                        <div><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">Create delivery note</button>
        </div>
    </form>
@endsection

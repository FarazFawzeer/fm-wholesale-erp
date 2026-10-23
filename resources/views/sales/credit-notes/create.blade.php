@extends('layouts.vertical', ['subtitle' => 'Return ' . $invoice->number])

@section('content')
    <x-page-header :title="'Return / Credit Note - ' . $invoice->number" :crumbs="['Invoices' => route('invoices.index'), $invoice->number => route('invoices.show', $invoice), 'Return']" />

    <form method="POST" action="{{ route('credit-notes.store', $invoice) }}">
        @csrf
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                @foreach ($byProduct as $rows)
                    @php $p = $rows->first()->product; $rows = $rows->sortBy(fn ($r) => (float) $r->variant->size); @endphp
                    <div class="line-card">
                        <div class="line-head"><div><div class="line-title">{{ $p->code }} - {{ $p->name }} {{ $p->colour }}</div>
                            <div class="line-meta">Net price {{ money($rows->first()->line_total / max(1, $rows->first()->qty)) }} / pair</div></div></div>
                        <div class="size-grid">
                            @foreach ($rows as $r)
                                <div class="size-cell">
                                    <label>{{ $r->variant->size }}</label>
                                    <input type="number" min="0" name="qty[{{ $r->id }}]" placeholder="0" class="form-control form-control-sm" max="{{ $r->qty - $r->returned_qty }}">
                                    <small>sold {{ $r->qty }}</small>
                                    @if ($r->returned_qty)<small class="low">ret {{ $r->returned_qty }}</small>@endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-xl-4 order-1 order-xl-2">
                <div class="card">
                    <div class="card-body">
                        <div class="fw-semibold mb-3">{{ $invoice->customer->name }}</div>
                        <div class="mb-3"><label class="form-label">Date</label><input type="date" name="date" value="{{ now()->format('Y-m-d') }}" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Reason</label>
                            <select name="reason" class="form-select">
                                @foreach (['Wrong size sent', 'Damaged / defective', 'Customer returned unsold stock', 'Wrong item sent', 'Price adjustment'] as $r)<option>{{ $r }}</option>@endforeach
                            </select></div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="restock" value="0">
                            <input class="form-check-input" type="checkbox" name="restock" value="1" id="restock" checked>
                            <label class="form-check-label" for="restock">Goods came back - add to stock</label>
                        </div>
                        <div class="form-text">Untick for a price adjustment only (no stock movement).</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-danger">Create credit note</button>
        </div>
    </form>
@endsection

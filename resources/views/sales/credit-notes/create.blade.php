@extends('layouts.vertical', ['subtitle' => 'Return ' . $invoice->number])

@section('content')
    <x-page-header :title="'Return goods - ' . $invoice->number" :crumbs="['Invoices' => route('invoices.index'), $invoice->number => route('invoices.show', $invoice), 'Return']" />

    <form method="POST" action="{{ route('credit-notes.store', $invoice) }}">
        @csrf
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                <div class="card">
                    <div class="card-body table-wrap p-0">
                        <div class="table-responsive">
                            <table class="table erp-table stack mb-0 align-middle">
                                <thead><tr><th>Product</th><th class="text-end">Sold</th><th class="text-end">Price / pair</th><th style="width: 140px">Pairs returned</th></tr></thead>
                                <tbody>
                                    @foreach ($invoice->lines as $r)
                                        <tr>
                                            <td class="td-title"><strong>{{ $r->product->name }}</strong> <span class="small text-muted">{{ $r->product->code }} &middot; Size {{ $r->product->sizeLabel() }}</span>
                                                @if ($r->returned_qty)<div class="small text-danger">{{ $r->returned_qty }} already returned</div>@endif</td>
                                            <td data-label="Sold" class="text-end">{{ $r->qty }}</td>
                                            <td data-label="Price / pair" class="text-end money">{{ money($r->line_total / max(1, $r->qty)) }}</td>
                                            <td data-label="Pairs returned"><input type="number" min="0" max="{{ $r->qty - $r->returned_qty }}" inputmode="numeric" name="qty[{{ $r->id }}]" placeholder="0" class="form-control text-end"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 order-1 order-xl-2">
                <div class="card">
                    <div class="card-body">
                        <div class="fw-semibold mb-3">{{ $invoice->customer->name }}</div>
                        <div class="mb-3"><label class="form-label">Date</label><input type="date" name="date" value="{{ now()->format('Y-m-d') }}" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Reason</label>
                            <select name="reason" class="form-select">
                                @foreach (['Damaged', 'Wrong size', 'Wrong item sent', 'Customer returned unsold stock', 'Price reduced'] as $r)<option>{{ $r }}</option>@endforeach
                            </select></div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="restock" value="0">
                            <input class="form-check-input" type="checkbox" name="restock" value="1" id="restock" checked>
                            <label class="form-check-label" for="restock">Put the shoes back in stock</label>
                        </div>
                        <div class="form-text">Turn off if the shoes are damaged or only the price is reduced.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-danger">Save return</button>
        </div>
    </form>
@endsection

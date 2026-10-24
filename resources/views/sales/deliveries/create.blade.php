@extends('layouts.vertical', ['subtitle' => 'Deliver ' . $invoice->number])

@section('content')
    <x-page-header :title="'Deliver ' . $invoice->number" :crumbs="['Invoices' => route('invoices.index'), $invoice->number => route('invoices.show', $invoice), 'Deliver']" />

    <form method="POST" action="{{ route('deliveries.store', $invoice) }}">
        @csrf
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                <div class="alert alert-info small">Enter how many pairs are going out now. You can send the rest later. Stock is taken from <strong>{{ $invoice->warehouse->name }}</strong>.</div>
                <div class="card">
                    <div class="card-body table-wrap p-0">
                        <div class="table-responsive">
                            <table class="table erp-table stack mb-0 align-middle">
                                <thead><tr><th>Product</th><th class="text-end">Still to send</th><th class="text-end">In stock</th><th style="width: 140px">Send now</th></tr></thead>
                                <tbody>
                                    @foreach ($invoice->lines as $r)
                                        @php $left = $r->qty - $r->delivered_qty; $stock = (int) $r->variant->stocks->firstWhere('warehouse_id', $invoice->warehouse_id)?->qty; @endphp
                                        <tr>
                                            <td class="td-title"><strong>{{ $r->product->name }}</strong> <span class="small text-muted">{{ $r->product->code }} &middot; Size {{ $r->product->sizeLabel() }} {{ $r->product->colour ? '· ' . $r->product->colour : '' }}</span></td>
                                            <td data-label="Still to send" class="text-end">{{ $left }}</td>
                                            <td data-label="In stock" class="text-end {{ $stock < $left ? 'text-danger fw-semibold' : '' }}">{{ $stock }}</td>
                                            <td data-label="Send now"><input type="number" min="0" max="{{ $left }}" inputmode="numeric" name="qty[{{ $r->id }}]" value="{{ min($left, $stock) ?: '' }}" placeholder="0" class="form-control text-end" @disabled($left <= 0)></td>
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
                        <div class="fw-semibold mb-3">{{ $invoice->customer->name }}<div class="small text-muted fw-normal">{{ $invoice->customer->address }} {{ $invoice->customer->city }}</div></div>
                        <div class="mb-3"><label class="form-label">Date</label><input type="date" name="date" value="{{ now()->format('Y-m-d') }}" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Vehicle no</label><input name="vehicle_no" class="form-control" placeholder="Optional"></div>
                        <div class="mb-3"><label class="form-label">Received by</label><input name="received_by" class="form-control" placeholder="Optional"></div>
                        <div><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">Save delivery</button>
        </div>
    </form>
@endsection

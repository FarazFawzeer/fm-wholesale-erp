@extends('layouts.vertical', ['subtitle' => 'Receive Goods ' . $grn->number])

@section('content')
    <x-page-header :title="'Receive goods - ' . $grn->number" :crumbs="['Received Goods' => route('grns.index'), $grn->number]" />

    @include('purchasing.partials.workflow', ['po' => $grn->purchaseOrder])

    <form method="POST" action="{{ route('grns.update', $grn) }}">
        @csrf @method('PUT')
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                <div class="alert alert-info small">Count the goods. Change <strong>Received</strong> if fewer pairs came. Put damaged pairs in <strong>Damaged</strong>; they will not be sold.</div>
                <div class="card">
                    <div class="card-body table-wrap p-0">
                        <div class="table-responsive">
                            <table class="table erp-table stack mb-0 align-middle">
                                <thead><tr><th>Product</th><th class="text-end">Ordered</th><th style="width: 130px">Received</th><th style="width: 130px">Damaged</th></tr></thead>
                                <tbody>
                                    @foreach ($grn->items as $i)
                                        @php $p = $i->variant->product; @endphp
                                        <tr>
                                            <td class="td-title"><strong>{{ $p->name }}</strong> <span class="small text-muted">{{ $p->code }} &middot; Size {{ $p->sizeLabel() }} {{ $p->colour ? '· ' . $p->colour : '' }}</span></td>
                                            <td data-label="Ordered" class="text-end">{{ $i->ordered_qty }}</td>
                                            <td data-label="Received"><input type="number" min="0" inputmode="numeric" name="received[{{ $i->id }}]" value="{{ $i->received_qty }}" class="form-control text-end"></td>
                                            <td data-label="Damaged"><input type="number" min="0" inputmode="numeric" name="damaged[{{ $i->id }}]" value="{{ $i->damaged_qty ?: '' }}" placeholder="0" class="form-control text-end border-danger-subtle"></td>
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
                    <div class="card-body info-list">
                        <div><div class="label">Order</div><div class="value"><a href="{{ route('purchase-orders.show', $grn->purchaseOrder) }}">{{ $grn->purchaseOrder->number }}</a></div></div>
                        <div><div class="label">Supplier</div><div class="value">{{ $grn->supplier->name }}</div></div>
                        <div><div class="label">Store</div><div class="value">{{ $grn->warehouse->name }}</div></div>
                    </div>
                    <div class="card-body border-top">
                        <div class="mb-3"><label class="form-label">Received date</label><input type="date" name="received_date" value="{{ $grn->received_date->format('Y-m-d') }}" class="form-control"></div>
                        <div><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2">{{ $grn->notes }}</textarea></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <button name="action" value="save" class="btn btn-light">Save for later</button>
            <button name="action" value="post" class="btn btn-success" onclick="return confirm('Add these goods to stock? This cannot be changed afterwards.')">Add to stock</button>
        </div>
    </form>
@endsection

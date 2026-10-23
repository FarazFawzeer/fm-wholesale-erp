@extends('layouts.vertical', ['subtitle' => 'GRN ' . $grn->number])

@section('content')
    <x-page-header :title="'Goods Received - ' . $grn->number" :crumbs="['GRN' => route('grns.index'), $grn->number]" />

    @include('purchasing.partials.workflow', ['po' => $grn->purchaseOrder])

    <form method="POST" action="{{ route('grns.update', $grn) }}">
        @csrf @method('PUT')
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                <div class="alert alert-info small">
                    Count each carton and enter what actually arrived per size. <strong>Damaged</strong> pairs are kept separately and are not sellable.
                    Good pairs go into stock at the landed cost from import costing.
                </div>
                @foreach ($byProduct as $items)
                    @php $p = $items->first()->variant->product; $items = $items->sortBy(fn ($i) => (float) $i->variant->size); @endphp
                    <div class="line-card">
                        <div class="line-head">
                            <div>
                                <div class="line-title">{{ $p->code }} - {{ $p->name }} {{ $p->colour }}</div>
                                <div class="line-meta">{{ $p->brand?->name }} &middot; landed {{ money($items->first()->unit_cost) }} / pair &middot; {{ $p->pairs_per_carton }} pairs/carton</div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm text-center align-middle mb-0">
                                <thead class="table-light"><tr><th class="text-start">Size</th>@foreach ($items as $i)<th>{{ $i->variant->size }}</th>@endforeach</tr></thead>
                                <tbody>
                                    <tr><td class="text-start small text-muted">Expected</td>@foreach ($items as $i)<td class="text-muted">{{ $i->ordered_qty }}</td>@endforeach</tr>
                                    <tr><td class="text-start small fw-semibold">Received</td>@foreach ($items as $i)<td><input type="number" min="0" inputmode="numeric" name="received[{{ $i->id }}]" value="{{ $i->received_qty }}" class="form-control form-control-sm text-center" style="min-width: 58px"></td>@endforeach</tr>
                                    <tr><td class="text-start small fw-semibold text-danger">Damaged</td>@foreach ($items as $i)<td><input type="number" min="0" inputmode="numeric" name="damaged[{{ $i->id }}]" value="{{ $i->damaged_qty ?: '' }}" placeholder="0" class="form-control form-control-sm text-center border-danger-subtle" style="min-width: 58px"></td>@endforeach</tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-xl-4 order-1 order-xl-2">
                <div class="card">
                    <div class="card-body info-list">
                        <div><div class="label">Purchase order</div><div class="value"><a href="{{ route('purchase-orders.show', $grn->purchaseOrder) }}">{{ $grn->purchaseOrder->number }}</a></div></div>
                        <div><div class="label">Supplier</div><div class="value">{{ $grn->supplier->name }}</div></div>
                        <div><div class="label">Shipment</div><div class="value">{{ $grn->purchaseOrder->shipment_ref ?: '-' }}</div></div>
                        <div><div class="label">Store</div><div class="value">{{ $grn->warehouse->name }}</div></div>
                    </div>
                    <div class="card-body border-top">
                        <div class="mb-3"><label class="form-label">Received date</label><input type="date" name="received_date" value="{{ $grn->received_date->format('Y-m-d') }}" class="form-control"></div>
                        <div><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2" placeholder="Seal no, carton condition...">{{ $grn->notes }}</textarea></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <button name="action" value="save" class="btn btn-light">Save draft</button>
            <button name="action" value="post" class="btn btn-success" onclick="return confirm('Post this GRN? Stock and landed cost will be updated and it cannot be edited afterwards.')">Post GRN &amp; update stock</button>
        </div>
    </form>
@endsection

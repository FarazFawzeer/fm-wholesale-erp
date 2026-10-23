@extends('layouts.vertical', ['subtitle' => 'New Stock Adjustment'])

@section('content')
    <x-page-header title="New Stock Adjustment" :crumbs="['Adjustments' => route('stock-adjustments.index'), 'New']" />

    <form method="POST" action="{{ route('stock-adjustments.store') }}">
        @csrf
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                <div class="card">
                    <div class="card-body" data-line-editor data-mode="stock" data-products='@json($catalog)' data-initial='@json($initial)'>
                        <select class="product-picker"></select>
                        <div class="lines mt-3"></div>
                        <div class="empty-lines text-center text-muted py-4">Add products and enter the pairs affected per size (always positive numbers).</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 order-1 order-xl-2">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3"><label class="form-label">Reason</label>
                            <select name="reason" id="reason" class="form-select">@foreach (\App\Models\StockAdjustment::REASONS as $k => $l)<option value="{{ $k }}" @selected(old('reason') == $k)>{{ $l }}</option>@endforeach</select>
                            <div class="form-text" id="reason-help"></div>
                        </div>
                        <div class="mb-3" id="direction-wrap"><label class="form-label">Count result</label>
                            <select name="direction" class="form-select"><option value="remove">Less than system (remove)</option><option value="add">More than system (add)</option></select></div>
                        <div class="mb-3"><label class="form-label">Store</label>
                            <select name="warehouse_id" id="warehouse_id" class="form-select">@foreach ($warehouses as $w)<option value="{{ $w->id }}" @selected($w->is_default)>{{ $w->name }}</option>@endforeach</select></div>
                        <div class="mb-3"><label class="form-label">Date</label><input type="date" name="date" value="{{ now()->format('Y-m-d') }}" class="form-control"></div>
                        <div><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2" placeholder="Why? (e.g. water damage, count on 23 Oct)"></textarea></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="{{ route('stock-adjustments.index') }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">Post adjustment</button>
        </div>
    </form>
@endsection

@push('page-js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const help = {
                count: 'Correct the system after a physical count.',
                damaged: 'Moves good stock to damaged (not sellable).',
                damaged_writeoff: 'Removes damaged stock permanently.',
                lost: 'Removes missing stock.',
                found: 'Adds surplus stock found.',
                supplier_return: 'Sends stock back to the supplier.',
            };
            const r = document.getElementById('reason');
            const sync = () => {
                document.getElementById('reason-help').textContent = help[r.value] || '';
                document.getElementById('direction-wrap').classList.toggle('d-none', r.value !== 'count');
            };
            r.addEventListener('change', sync);
            sync();
        });
    </script>
@endpush

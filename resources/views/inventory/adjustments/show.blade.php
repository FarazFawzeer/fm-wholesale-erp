@extends('layouts.vertical', ['subtitle' => $a->number])

@section('content')
    <x-page-header :title="'Adjustment ' . $a->number" :crumbs="['Adjustments' => route('stock-adjustments.index'), $a->number]" />
    <div class="card">
        <div class="card-header">{{ \App\Models\StockAdjustment::REASONS[$a->reason] ?? $a->reason }} &middot; {{ $a->warehouse->name }} &middot; {{ $a->date->format('d M Y') }} &middot; {{ $a->user?->name }}</div>
        <div class="card-body">
            @foreach ($byProduct as $items)
                @php $p = $items->first()->variant->product; @endphp
                <div class="mb-2"><strong>{{ $p->code }}</strong> {{ $p->name }} {{ $p->colour }}:
                    @foreach ($items->sortBy(fn ($i) => (float) $i->variant->size) as $i)<span class="badge bg-light text-dark fw-normal">{{ $i->variant->size }}: {{ $i->qty > 0 ? '+' : '' }}{{ $i->qty }}</span> @endforeach</div>
            @endforeach
            @if ($a->notes)<div class="small text-muted mt-2">{{ $a->notes }}</div>@endif
        </div>
        <div class="card-footer d-flex justify-content-between"><span>Total {{ $a->items->sum('qty') }} pairs</span><span class="money fw-semibold">{{ money($a->value) }}</span></div>
    </div>
@endsection

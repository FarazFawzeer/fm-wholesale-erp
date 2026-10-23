@extends('layouts.vertical', ['subtitle' => $t->number])

@section('content')
    <x-page-header :title="'Transfer ' . $t->number" :crumbs="['Transfers' => route('stock-transfers.index'), $t->number]">
        <button onclick="window.print()" class="btn btn-light">Print</button>
    </x-page-header>
    <div class="card">
        <div class="card-header">{{ $t->from->name }} &rarr; <strong>{{ $t->to->name }}</strong> &middot; {{ $t->date->format('d M Y') }} &middot; {{ $t->user?->name }}</div>
        <div class="card-body">
            @foreach ($byProduct as $items)
                @php $p = $items->first()->variant->product; @endphp
                <div class="mb-2"><strong>{{ $p->code }}</strong> {{ $p->name }} {{ $p->colour }}:
                    @foreach ($items->sortBy(fn ($i) => (float) $i->variant->size) as $i)<span class="badge bg-light text-dark fw-normal">{{ $i->variant->size }} &times; {{ $i->qty }}</span> @endforeach
                    <span class="fw-semibold ms-1">= {{ $items->sum('qty') }} pairs</span></div>
            @endforeach
            @if ($t->notes)<div class="small text-muted mt-2">{{ $t->notes }}</div>@endif
        </div>
        <div class="card-footer fw-semibold">Total {{ $t->items->sum('qty') }} pairs</div>
    </div>
@endsection

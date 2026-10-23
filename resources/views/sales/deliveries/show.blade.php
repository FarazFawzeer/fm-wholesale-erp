<x-print-doc title="DELIVERY NOTE" :back="route('invoices.show', $note->invoice)">
    <x-slot:meta>
        <div class="b">{{ $note->number }}</div>
        <div class="muted">Date: {{ $note->date->format('d M Y') }}</div>
        <div class="muted">Invoice: {{ $note->invoice->number }}</div>
    </x-slot:meta>

    <div class="parties">
        <div>
            <div class="muted">Deliver to</div>
            <div class="b">{{ $note->customer->business_name ?: $note->customer->name }}</div>
            <div>{{ $note->customer->address }} {{ $note->customer->city }}</div>
            <div>{{ $note->customer->phone }}</div>
        </div>
        <div class="r">
            <div class="muted">From store</div><div class="b">{{ $note->warehouse->name }}</div>
            <div class="muted">Vehicle</div><div>{{ $note->vehicle_no ?: '-' }}</div>
        </div>
    </div>

    <table>
        <thead><tr><th>#</th><th>Item</th><th>Sizes</th><th class="r">Pairs</th></tr></thead>
        <tbody>
            @foreach ($byProduct->values() as $n => $items)
                @php $p = $items->first()->line->product; @endphp
                <tr>
                    <td>{{ $n + 1 }}</td>
                    <td><span class="b">{{ $p->code }}</span> {{ $p->brand?->name }} {{ $p->name }} {{ $p->colour }}</td>
                    <td class="sizes">@foreach ($items->sortBy(fn ($i) => (float) $i->variant->size) as $i){{ $i->variant->size }}×{{ $i->qty }}{{ ! $loop->last ? ', ' : '' }}@endforeach</td>
                    <td class="r b">{{ $items->sum('qty') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table class="totals"><tr class="grand"><td>Total pairs</td><td class="r">{{ $note->items->sum('qty') }}</td></tr></table>

    @if ($note->notes)<p class="muted">Note: {{ $note->notes }}</p>@endif
    <div class="foot">
        <div class="sign">Issued by {{ $note->user?->name }}</div>
        <div class="sign">Received in good condition{{ $note->received_by ? ' - ' . $note->received_by : '' }}</div>
    </div>
</x-print-doc>

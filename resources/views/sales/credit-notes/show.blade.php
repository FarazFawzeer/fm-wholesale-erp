<x-print-doc title="CREDIT NOTE" :back="route('invoices.show', $note->invoice)">
    <x-slot:meta>
        <div class="b">{{ $note->number }}</div>
        <div class="muted">Date: {{ $note->date->format('d M Y') }}</div>
        <div class="muted">Against invoice: {{ $note->invoice->number }}</div>
    </x-slot:meta>

    <div class="parties">
        <div>
            <div class="muted">Customer</div>
            <div class="b">{{ $note->customer->business_name ?: $note->customer->name }}</div>
            <div>{{ $note->customer->address }} {{ $note->customer->city }}</div>
        </div>
        <div class="r">
            <div class="muted">Reason</div><div class="b">{{ $note->reason }}</div>
            <div class="muted">{{ $note->restock ? 'Goods returned to stock' : 'Price adjustment' }}</div>
        </div>
    </div>

    <table>
        <thead><tr><th>Item</th><th>Size</th><th class="r">Pairs</th><th class="r">Price</th><th class="r">Amount</th></tr></thead>
        <tbody>
            @foreach ($note->items as $i)
                <tr>
                    <td><span class="b">{{ $i->line->product->code }}</span> {{ $i->line->product->name }} {{ $i->line->product->colour }}</td>
                    <td>{{ $i->variant->size }}</td>
                    <td class="r">{{ $i->qty }}</td>
                    <td class="r">{{ money($i->unit_price, false) }}</td>
                    <td class="r">{{ money($i->line_total, false) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table class="totals"><tr class="grand"><td>Credit (Rs)</td><td class="r">{{ money($note->total, false) }}</td></tr></table>
    <div class="foot"><div class="sign">Authorised by {{ $note->user?->name }}</div><div class="sign">Customer</div></div>
</x-print-doc>

<x-print-doc :title="$invoice->sale_type === 'cash' ? 'CASH INVOICE' : 'INVOICE'" :back="route('invoices.show', $invoice)">
    <x-slot:meta>
        <div class="b">{{ $invoice->number }}</div>
        <div class="muted">Date: {{ $invoice->date->format('d M Y') }}</div>
        <div class="muted">Due: {{ $invoice->due_date->format('d M Y') }}</div>
    </x-slot:meta>

    <div class="parties">
        <div>
            <div class="muted">Bill to</div>
            <div class="b">{{ $invoice->customer->business_name ?: $invoice->customer->name }}</div>
            <div>{{ $invoice->customer->address }} {{ $invoice->customer->city }}</div>
            <div>{{ $invoice->customer->phone }}</div>
        </div>
        <div class="r">
            <div class="muted">Customer code</div><div class="b">{{ $invoice->customer->code }}</div>
            <div class="muted">Sales person</div><div>{{ $invoice->user?->name }}</div>
        </div>
    </div>

    <table>
        <thead><tr><th>#</th><th>Item</th><th class="r">Pairs</th><th class="r">Price</th><th class="r">Disc</th><th class="r">Amount</th></tr></thead>
        <tbody>
            @foreach ($byProduct->values() as $n => $rows)
                @php $f = $rows->first(); @endphp
                <tr>
                    <td>{{ $n + 1 }}</td>
                    <td><span class="b">{{ $f->product->code }}</span> {{ $f->product->brand?->name }} {{ $f->product->name }} {{ $f->product->colour }}
                        <div class="sizes">Sizes: @foreach ($rows->sortBy(fn ($r) => (float) $r->variant->size) as $r){{ $r->variant->size }}×{{ $r->qty }}{{ ! $loop->last ? ', ' : '' }}@endforeach</div></td>
                    <td class="r">{{ $rows->sum('qty') }}</td>
                    <td class="r">{{ money($f->unit_price, false) }}</td>
                    <td class="r">{{ $f->discount_percent > 0 ? $f->discount_percent . '%' : '' }}</td>
                    <td class="r">{{ money($rows->sum('line_total'), false) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Total pairs</td><td class="r">{{ $invoice->lines->sum('qty') }}</td></tr>
        <tr><td>Gross</td><td class="r">{{ money($invoice->subtotal, false) }}</td></tr>
        @if ($invoice->discount_amount > 0)<tr><td>Discount</td><td class="r">-{{ money($invoice->discount_amount, false) }}</td></tr>@endif
        <tr class="grand"><td>Total (Rs)</td><td class="r">{{ money($invoice->total, false) }}</td></tr>
        @if ($invoice->paid_amount > 0)<tr><td>Paid</td><td class="r">{{ money($invoice->paid_amount, false) }}</td></tr>@endif
        @if ($invoice->credited_amount > 0)<tr><td>Credit notes</td><td class="r">-{{ money($invoice->credited_amount, false) }}</td></tr>@endif
        <tr><td class="b">Balance</td><td class="r b">{{ money($invoice->balance(), false) }}</td></tr>
    </table>

    @if ($invoice->notes)<p class="muted">Note: {{ $invoice->notes }}</p>@endif
    <p class="muted">Goods once sold will be exchanged only with the original invoice within 7 days. Cheques to be written in favour of {{ setting('company_name') }}.</p>

    <div class="foot">
        <div class="sign">Prepared by</div>
        <div class="sign">Customer signature</div>
    </div>
</x-print-doc>

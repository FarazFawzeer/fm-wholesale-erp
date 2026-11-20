@php
    $c = $invoice->customer;
    $balance = $invoice->balance();
    $net = $invoice->total - $invoice->returned_amount;
    $share = $public ? null : 'https://wa.me/' . ($c->whatsappNumber() ?? '');
    $pdf = $public ? route('invoice.public.pdf', $invoice->share_token) : route('invoices.pdf', $invoice);
    $terms = collect(terms_list());
    $status = $balance <= 0 ? 'Paid' : ($invoice->paid_amount > 0 ? 'Part paid' : 'Not paid');
@endphp
<x-print-doc title="Invoice {{ $invoice->number }}" :back="route('invoices.show', $invoice)" :public="$public" :share="$share" :pdf="$pdf" :pdf-name="'Invoice-' . $invoice->number . '.pdf'">
    <div class="top">
        <div class="logo"><img src="{{ asset('images/logo-dark.png') }}" alt="{{ setting('company_name') }}"></div>
        <div class="title">
            <h1>Invoice</h1>
            <div class="no"># {{ $invoice->number }}</div>
            <div class="bd-label">Balance Due</div>
            <div class="bd">{{ money($balance) }}</div>
        </div>
    </div>

    <div class="mid">
        <div class="company">
            <div class="name">{{ setting('company_name') }}</div>
            @if (setting('company_tagline'))<div>{{ setting('company_tagline') }}</div>@endif
            @if (setting('company_address'))<div>{{ setting('company_address') }}</div>@endif
            @if (setting('company_phone'))<div>{{ setting('company_phone') }}</div>@endif
        </div>
        <table class="dates">
            <tr><td>Invoice Date :</td><td>{{ $invoice->date->format('d M Y') }}</td></tr>
            <tr><td>Status :</td><td>{{ $status }}</td></tr>
            <tr><td>Total Pairs :</td><td>{{ $invoice->totalPairs() }}</td></tr>
            @if ($invoice->user)<tr><td>Sales Person :</td><td>{{ $invoice->user->name }}</td></tr>@endif
        </table>
    </div>

    <div class="billto">
        <div class="lbl">Bill To</div>
        <div class="who">{{ $c->name }}</div>
        @if ($c->shop_name)<div>{{ $c->shop_name }}</div>@endif
        @if ($c->address || $c->city)<div class="muted">{{ trim($c->address . ', ' . $c->city, ', ') }}</div>@endif
        @if ($c->phone)<div class="muted">{{ $c->phone }}</div>@endif
    </div>

    <table class="items">
        <thead><tr><th>#</th><th>Item &amp; Description</th><th class="r">Pairs</th><th class="r">Rate</th><th class="r">Amount</th></tr></thead>
        <tbody>
            @foreach ($groups as $items)
                @php $p = $items->first()->product; @endphp
                <tr>
                    <td class="n">{{ $loop->iteration }}</td>
                    <td>
                        <div class="nm">{{ $p->name }}</div>
                        <div class="sub"><b>Item No:</b> <span class="mono">{{ $p->code }}</span>@if ($p->sizeLabel() && ! $items->contains(fn ($it) => $it->colour->size)) &nbsp;·&nbsp; <b>Size:</b> {{ $p->sizeLabel() }}@endif</div>
                        <div class="cols">{{ $items->map(fn ($it) => $it->colour->title . ' × ' . $it->qty)->implode(', ') }}</div>
                    </td>
                    <td class="r m first" data-label="Pairs">{{ $items->sum('qty') }}</td>
                    <td class="r nowrap m" data-label="Rate">{{ money($items->first()->unit_price, false) }}</td>
                    <td class="r nowrap m amt" data-label="Amount">{{ money($items->sum('line_total'), false) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="sum">
        <table class="totals">
            <tr><td>Sub Total</td><td>{{ money($invoice->subtotal, false) }}</td></tr>
            @if ($invoice->discount > 0)<tr><td>Discount</td><td>(-) {{ money($invoice->discount, false) }}</td></tr>@endif
            <tr class="strong"><td>Total</td><td>{{ money($invoice->total) }}</td></tr>
            @if ($invoice->returned_amount > 0)<tr><td>Goods Returned</td><td>(-) {{ money($invoice->returned_amount, false) }}</td></tr>@endif
            @if ($invoice->paid_amount > 0)<tr><td>Payment Made</td><td>(-) {{ money($invoice->paid_amount, false) }}</td></tr>@endif
            <tr class="due"><td>Balance Due</td><td>{{ money($balance) }}</td></tr>
        </table>
    </div>
    <div class="words">Total in words: <b>{{ amount_in_words($net) }}</b></div>

    @if ($invoice->notes)<div class="note"><b>Notes:</b> {{ $invoice->notes }}</div>@endif

    @if ($terms->count())
        <div class="terms">
            <h4>Terms &amp; Conditions</h4>
            <ol>@foreach ($terms as $t)<li>{{ $t }}</li>@endforeach</ol>
        </div>
    @endif

    <div class="signs">
        <div class="sign">
            <div class="line"></div>
            <div class="who">{{ setting('company_name') }}</div>
            <div class="muted">Authorized Signature</div>
        </div>
        <div class="sign right">
            <div class="line"></div>
            <div class="who">{{ $c->name }}</div>
            <div class="muted">Customer Signature</div>
        </div>
    </div>
</x-print-doc>

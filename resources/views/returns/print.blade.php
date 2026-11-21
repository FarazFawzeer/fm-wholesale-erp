@php
    $title = $isCustomer ? 'Credit Note' : 'Return Note';
@endphp
<x-print-doc :title="$title . ' ' . $return->number" :back="route('returns.show', $return)">
    <div class="top">
        <div class="logo"><img src="{{ asset('images/logo-dark.png') }}" alt="{{ setting('company_name') }}"></div>
        <div class="title">
            <h1>{{ $title }}</h1>
            <div class="no"># {{ $return->number }}</div>
            <div class="bd-label">Return Amount</div>
            <div class="bd">{{ money($return->total) }}</div>
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
            <tr><td>Date :</td><td>{{ $return->date->format('d M Y') }}</td></tr>
            <tr><td>{{ $isCustomer ? 'Invoice' : 'Purchase' }} :</td><td>{{ $doc->number }}</td></tr>
            @if (! $isCustomer && $doc->supplier_bill_no)<tr><td>Supplier Bill :</td><td>{{ $doc->supplier_bill_no }}</td></tr>@endif
            <tr><td>Total Pairs :</td><td>{{ $return->items->sum('qty') }}</td></tr>
            @if ($return->reason)<tr><td>Reason :</td><td>{{ $return->reason }}</td></tr>@endif
        </table>
    </div>

    <div class="billto">
        <div class="lbl">{{ $isCustomer ? 'Customer' : 'Supplier' }}</div>
        <div class="who">{{ $party->name }}</div>
        @if ($party->shop_name ?? null)<div>{{ $party->shop_name }}</div>@endif
        @if ($party->address || ($party->city ?? null))<div class="muted">{{ trim($party->address . ', ' . ($party->city ?? ''), ', ') }}</div>@endif
        @if ($party->phone)<div class="muted">{{ $party->phone }}</div>@endif
    </div>

    <table class="items">
        <thead><tr><th>#</th><th>Item &amp; Description</th><th class="r">Pairs</th><th class="r">Rate</th><th class="r">Amount</th></tr></thead>
        <tbody>
            @foreach ($groups as $items)
                @php $p = $items->first()->colour->product; @endphp
                <tr>
                    <td class="n">{{ $loop->iteration }}</td>
                    <td>
                        <div class="nm">{{ $p->name }}</div>
                        <div class="sub"><b>Item No:</b> <span class="mono">{{ $p->code }}</span>@if ($p->sizeLabel() && ! $items->contains(fn ($it) => $it->colour->size)) &nbsp;·&nbsp; <b>Size:</b> {{ $p->sizeLabel() }}@endif</div>
                        <div class="cols">{!! collect(colour_lines($items))->map(fn ($l) => e($l))->implode('<br>') !!}</div>
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
            <tr class="due"><td>{{ $isCustomer ? 'Credited to customer' : 'Deducted from our bill' }}</td><td>{{ money($return->total) }}</td></tr>
        </table>
    </div>
    <div class="words">Amount in words: <b>{{ amount_in_words($return->total) }}</b></div>

    <div class="signs">
        <div class="sign">
            <div class="line"></div>
            <div class="who">{{ setting('company_name') }}</div>
            <div class="muted">Authorized Signature</div>
        </div>
        <div class="sign right">
            <div class="line"></div>
            <div class="who">{{ $party->name }}</div>
            <div class="muted">{{ $isCustomer ? 'Customer' : 'Supplier' }} Signature</div>
        </div>
    </div>
</x-print-doc>

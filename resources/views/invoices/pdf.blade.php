@php
    $c = $invoice->customer;
    $balance = $invoice->balance();
    $net = $invoice->total - $invoice->returned_amount;
    $terms = collect(terms_list());
    $status = $balance <= 0 ? 'Paid' : ($invoice->paid_amount > 0 ? 'Part paid' : 'Not paid');
    $logo = public_path('images/logo-pdf.jpg'); // JPEG: works even when the server has no image (GD) extension
    $logoSrc = is_file($logo) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logo)) : null;
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Invoice {{ $invoice->number }}</title>
<style>
    @page { margin: 32px 36px; }
    * { box-sizing: border-box; }
    body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #222; line-height: 1.45; }
    table { border-collapse: collapse; width: 100%; }
    .muted { color: #666; }
    .r { text-align: right; }
    .top td { vertical-align: top; }
    .logo { height: 58px; }
    h1 { font-size: 26px; margin: 0; font-weight: normal; letter-spacing: .5px; }
    .no { font-size: 12px; font-weight: bold; margin-top: 2px; }
    .bd-label { font-size: 10px; color: #555; margin-top: 10px; }
    .bd { font-size: 17px; font-weight: bold; }
    .company .name { font-size: 13px; font-weight: bold; }
    .dates td { padding: 2px 0; }
    .dates td.k { color: #555; text-align: right; padding-right: 10px; }
    .billto { margin: 18px 0 14px; }
    .billto .lbl { color: #555; font-size: 10px; }
    .billto .who { font-size: 12px; font-weight: bold; }
    .items th { background: #2d2d2d; color: #fff; font-weight: bold; padding: 7px 8px; font-size: 10.5px; text-align: left; }
    .items th.r { text-align: right; }
    .items td { padding: 8px; border-bottom: 1px solid #e3e3e3; vertical-align: top; }
    .items .nm { font-weight: bold; font-size: 11.5px; }
    .items .sub { color: #555; font-size: 10px; }
    .items .cols { color: #333; font-size: 10px; margin-top: 2px; }
    .totals { width: 52%; margin-left: 48%; margin-top: 10px; }
    .totals td { padding: 5px 8px; }
    .totals .strong td { font-weight: bold; }
    .totals .due td { background: #f0f0f0; font-weight: bold; font-size: 12.5px; }
    .words { margin-top: 12px; font-size: 10.5px; color: #444; }
    .note { margin-top: 10px; font-size: 10.5px; }
    .terms { margin-top: 16px; }
    .terms h4 { font-size: 11px; margin: 0 0 4px; }
    .terms ol { margin: 0; padding-left: 16px; font-size: 10px; color: #444; }
    .signs { margin-top: 46px; }
    .signs td { width: 50%; vertical-align: bottom; }
    .line { border-top: 1px solid #999; width: 70%; margin-bottom: 4px; }
    .right .line { margin-left: 30%; }
    .right { text-align: right; }
</style>
</head>
<body>
    <table class="top">
        <tr>
            <td>@if ($logoSrc)<img src="{{ $logoSrc }}" class="logo">@endif</td>
            <td class="r">
                <h1>Invoice</h1>
                <div class="no"># {{ $invoice->number }}</div>
                <div class="bd-label">Balance Due</div>
                <div class="bd">{{ money($balance) }}</div>
            </td>
        </tr>
    </table>

    <table class="top" style="margin-top: 14px">
        <tr>
            <td class="company">
                <div class="name">{{ setting('company_name') }}</div>
                @if (setting('company_tagline'))<div>{{ setting('company_tagline') }}</div>@endif
                @if (setting('company_address'))<div>{{ setting('company_address') }}</div>@endif
                @if (setting('company_phone'))<div>{{ setting('company_phone') }}</div>@endif
            </td>
            <td style="width: 46%">
                <table class="dates">
                    <tr><td class="k">Invoice Date :</td><td>{{ $invoice->date->format('d M Y') }}</td></tr>
                    <tr><td class="k">Status :</td><td>{{ $status }}</td></tr>
                    <tr><td class="k">Total Pairs :</td><td>{{ $invoice->totalPairs() }}</td></tr>
                    @if ($invoice->user)<tr><td class="k">Sales Person :</td><td>{{ $invoice->user->name }}</td></tr>@endif
                </table>
            </td>
        </tr>
    </table>

    <div class="billto">
        <div class="lbl">Bill To</div>
        <div class="who">{{ $c->name }}</div>
        @if ($c->shop_name)<div>{{ $c->shop_name }}</div>@endif
        @if ($c->address || $c->city)<div class="muted">{{ trim($c->address . ', ' . $c->city, ', ') }}</div>@endif
        @if ($c->phone)<div class="muted">{{ $c->phone }}</div>@endif
    </div>

    <table class="items">
        <thead><tr><th style="width: 24px">#</th><th>Item &amp; Description</th><th class="r" style="width: 50px">Pairs</th><th class="r" style="width: 80px">Rate</th><th class="r" style="width: 90px">Amount</th></tr></thead>
        <tbody>
            @foreach ($groups as $items)
                @php $p = $items->first()->product; @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <div class="nm">{{ $p->name }}</div>
                        <div class="sub"><b>Item No:</b> {{ $p->code }}@if ($p->sizeLabel() && ! $items->contains(fn ($it) => $it->colour->size)) &nbsp;&middot;&nbsp; <b>Size:</b> {{ $p->sizeLabel() }}@endif</div>
                        <div class="cols">{!! collect(colour_lines($items))->map(fn ($l) => e($l))->implode('<br>') !!}</div>
                    </td>
                    <td class="r">{{ $items->sum('qty') }}</td>
                    <td class="r">{{ money($items->first()->unit_price, false) }}</td>
                    <td class="r">{{ money($items->sum('line_total'), false) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Sub Total</td><td class="r">{{ money($invoice->subtotal, false) }}</td></tr>
        @if ($invoice->discount > 0)<tr><td>Discount</td><td class="r">(-) {{ money($invoice->discount, false) }}</td></tr>@endif
        <tr class="strong"><td>Total</td><td class="r">{{ money($invoice->total) }}</td></tr>
        @if ($invoice->returned_amount > 0)<tr><td>Goods Returned</td><td class="r">(-) {{ money($invoice->returned_amount, false) }}</td></tr>@endif
        @if ($invoice->paid_amount > 0)<tr><td>Payment Made</td><td class="r">(-) {{ money($invoice->paid_amount, false) }}</td></tr>@endif
        <tr class="due"><td>Balance Due</td><td class="r">{{ money($balance) }}</td></tr>
    </table>

    <div class="words">Total in words: <b>{{ amount_in_words($net) }}</b></div>
    @if ($invoice->notes)<div class="note"><b>Notes:</b> {{ $invoice->notes }}</div>@endif

    @if ($terms->count())
        <div class="terms">
            <h4>Terms &amp; Conditions</h4>
            <ol>@foreach ($terms as $t)<li>{{ $t }}</li>@endforeach</ol>
        </div>
    @endif

    <table class="signs">
        <tr>
            <td><div class="line"></div><b>{{ setting('company_name') }}</b><div class="muted">Authorized Signature</div></td>
            <td class="right"><div class="line"></div><b>{{ $c->name }}</b><div class="muted">Customer Signature</div></td>
        </tr>
    </table>
</body>
</html>

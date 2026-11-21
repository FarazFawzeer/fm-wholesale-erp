@php
    $logo = public_path('images/logo-pdf.jpg');
    $logoSrc = is_file($logo) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logo)) : null;
    $cols = 3;
    $rows = $products->chunk($cols);
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Catalogue</title>
<style>
    @page { margin: 28px 30px 40px; }
    * { box-sizing: border-box; }
    body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #222; line-height: 1.4; }
    table { border-collapse: collapse; width: 100%; }
    .head td { vertical-align: middle; }
    .logo { height: 52px; }
    h1 { font-size: 22px; margin: 0; font-weight: normal; letter-spacing: .5px; }
    .muted { color: #666; }
    .r { text-align: right; }
    .bar { border-top: 2px solid #111; margin: 12px 0 14px; }
    .grid { border-collapse: separate; border-spacing: 8px 10px; width: 100%; table-layout: fixed; }
    .grid tr { page-break-inside: avoid; }
    .card { width: 33.33%; border: 1px solid #ddd; border-radius: 8px; padding: 8px; vertical-align: top; }
    .ph { width: 100%; border-radius: 5px; overflow: hidden; background: #f2f2f0; }
    .ph img { display: block; width: 100%; height: auto; }
    .ph .none { height: 150px; line-height: 150px; text-align: center; color: #aaa; font-size: 11px; }
    .code { display: inline-block; margin-top: 8px; padding: 1px 6px; border-radius: 4px; background: #111827; color: #fff; font-size: 9.5px; font-weight: bold; letter-spacing: .4px; }
    .name { font-size: 11.5px; font-weight: bold; margin-top: 4px; line-height: 1.25; }
    .sub { color: #666; font-size: 9px; }
    .avail { margin-top: 5px; font-size: 9.5px; }
    .avail b { display: block; font-size: 8px; color: #666; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 1px; }
    .foot { position: fixed; bottom: -24px; left: 0; right: 0; font-size: 9.5px; color: #777; text-align: center; }
</style>
</head>
<body>
    <div class="foot">{{ setting('company_name') }}@if (setting('company_phone')) &middot; {{ setting('company_phone') }}@endif &middot; Stock as on {{ now()->format('d M Y') }} &middot; Reply with the item no, colour, size and pairs you need</div>

    <table class="head">
        <tr>
            <td>@if ($logoSrc)<img src="{{ $logoSrc }}" class="logo">@endif</td>
            <td class="r">
                <h1>Product Catalogue</h1>
                <div class="muted">{{ $products->count() }} {{ \Illuminate\Support\Str::plural('item', $products->count()) }} &middot; {{ now()->format('d M Y') }}</div>
            </td>
        </tr>
    </table>
    <table style="margin-top: 8px">
        <tr>
            <td>
                <b>{{ setting('company_name') }}</b>@if (setting('company_tagline')) &middot; {{ setting('company_tagline') }}@endif
                @if (setting('company_address'))<div class="muted">{{ setting('company_address') }}</div>@endif
            </td>
            <td class="r">@if (setting('company_phone'))<b>{{ setting('company_phone') }}</b>@endif</td>
        </tr>
    </table>
    <div class="bar" style="margin-bottom: 4px"></div>

    <table class="grid">
        @foreach ($rows as $row)
            <tr>
                @foreach ($row as $p)
                    @php $img = $p->pdfImagePath(); @endphp
                    <td class="card">
                        <div class="ph">@if ($img)<img src="{{ $img }}">@else<div class="none">No photo</div>@endif</div>
                        <span class="code">{{ $p->code }}</span>
                        <div class="name">{{ $p->name }}</div>
                        @if ($extra = collect([$p->brand?->name, $p->category?->name])->filter()->implode(' · '))<div class="sub">{{ $extra }}</div>@endif
                        <div class="avail">
                            <b>Available colours &amp; sizes</b>
                            @forelse ($p->availableLines($pairs) as $line)<div>{{ $line }}</div>@empty<div class="muted">Out of stock now</div>@endforelse
                        </div>
                    </td>
                @endforeach
                @for ($i = $row->count(); $i < $cols; $i++)<td style="width: 33.33%"></td>@endfor
            </tr>
        @endforeach
    </table>
</body>
</html>

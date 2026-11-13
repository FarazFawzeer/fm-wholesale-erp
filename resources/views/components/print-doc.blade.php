@props(['title', 'back' => null, 'public' => false, 'share' => null])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#ffffff">
    <title>{{ $title }} | {{ setting('company_name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
        :root { --ink: #1f2328; --text: #333; --muted: #6b7280; --line: #e5e7eb; --head: #2d2d2d; --soft: #f5f4f1; }
        * { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; }
        body { font-family: 'Inter', 'Segoe UI', Roboto, Arial, sans-serif; color: var(--text); margin: 0; background: #eceef1; font-size: 13px; line-height: 1.5; font-feature-settings: 'tnum' 1, 'cv11' 1; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .muted { color: var(--muted); }
        .r { text-align: right; } .nowrap { white-space: nowrap; } .b { font-weight: 600; color: var(--ink); }
        .mono { font-family: 'JetBrains Mono', ui-monospace, monospace; font-size: 1em; letter-spacing: -.01em; }

        /* Toolbar (screen only) */
        .toolbar { position: sticky; top: 0; z-index: 10; background: rgba(236, 238, 241, .94); backdrop-filter: blur(8px); }
        .toolbar-in { max-width: 820px; margin: 0 auto; display: flex; gap: 8px; align-items: center; padding: 12px 16px; }
        .toolbar .sp { flex: 1; }
        .tb { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 9px 15px; border-radius: 8px; border: 1px solid #d0d5dd; background: #fff; cursor: pointer; text-decoration: none; color: var(--ink); font: inherit; font-weight: 600; font-size: 14px; }
        .tb svg { width: 17px; height: 17px; }
        .tb.primary { background: var(--head); color: #fff; border-color: var(--head); }
        .tb.wa { background: #25d366; color: #fff; border-color: #25d366; }

        /* Sheet */
        .page { max-width: 820px; margin: 4px auto 32px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 10px 30px rgba(0,0,0,.06); padding: 44px 48px 48px; }

        .top { display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; }
        .logo img { height: 66px; display: block; }
        .title { text-align: right; }
        .title h1 { margin: 0; font-size: 34px; font-weight: 300; color: var(--ink); line-height: 1.1; letter-spacing: -.01em; }
        .title .no { margin-top: 6px; font-weight: 700; color: var(--ink); font-size: 13px; }
        .title .bd-label { margin-top: 16px; font-size: 11.5px; color: var(--muted); }
        .title .bd { font-size: 17px; font-weight: 700; color: var(--ink); }

        .mid { display: flex; justify-content: space-between; gap: 24px; margin-top: 22px; }
        .company .name { font-weight: 700; font-size: 14px; color: var(--ink); margin-bottom: 2px; }
        .company div { line-height: 1.6; }
        .dates { border-collapse: collapse; align-self: flex-start; }
        .dates td { padding: 2px 0; font-size: 13.5px; }
        .dates td:first-child { color: var(--muted); text-align: right; padding-right: 40px; }
        .dates td:last-child { text-align: right; color: var(--ink); }

        .billto { margin-top: 22px; }
        .billto .lbl { color: var(--muted); }
        .billto .who { font-weight: 700; font-size: 14px; color: var(--ink); margin-top: 2px; }

        /* Items */
        table.items { width: 100%; border-collapse: collapse; margin-top: 18px; }
        .items th { background: var(--head); color: #fff; font-weight: 500; font-size: 12.5px; padding: 9px 10px; text-align: left; border-left: 1px solid #444; }
        .items th:first-child { border-left: 0; width: 34px; }
        .items th.r { text-align: right; }
        .items td { padding: 11px 10px; border-bottom: 1px solid var(--line); vertical-align: top; }
        .items .nm { font-weight: 600; color: var(--ink); font-size: 13.5px; }
        .items .sub { font-size: 11.5px; color: var(--muted); margin-top: 1px; }
        .items .sub b { color: #444; font-weight: 600; }
        .items .cols { font-size: 11.5px; color: #555; margin-top: 3px; }

        /* Totals */
        .sum { display: flex; justify-content: flex-end; }
        table.totals { width: 340px; max-width: 100%; border-collapse: collapse; }
        .totals td { padding: 7px 10px; font-size: 13px; }
        .totals td:last-child { text-align: right; white-space: nowrap; }
        .totals .strong td { font-weight: 700; color: var(--ink); }
        .totals .due td { background: var(--soft); font-weight: 700; color: var(--ink); padding: 10px; }
        .words { margin-top: 10px; text-align: right; font-size: 11.5px; color: var(--muted); }
        .words b { color: #444; font-weight: 600; }

        .terms { margin-top: 34px; }
        .terms h4 { margin: 0 0 8px; font-size: 13px; color: var(--ink); font-weight: 700; }
        .terms ol { margin: 0; padding-left: 18px; font-size: 11.5px; color: #555; }
        .terms li { padding: 2px 0; }
        .note { margin-top: 18px; font-size: 12px; }

        .signs { display: flex; justify-content: space-between; gap: 24px; margin-top: 70px; }
        .sign .line { width: 200px; border-top: 1px solid #9ca3af; margin-bottom: 8px; }
        .sign .who { font-weight: 700; color: var(--ink); }
        .sign.right { text-align: right; }
        .sign.right .line { margin-left: auto; }

        /* Phone */
        @media (max-width: 640px) {
            .toolbar-in { padding: 10px 12px; }
            .tb { padding: 9px 12px; }
            .tb .lbl-long { display: none; }
            .page { margin: 0 8px 24px; padding: 22px 16px 26px; border-radius: 10px; }
            .logo img { height: 46px; }
            .title h1 { font-size: 26px; }
            .title .bd-label { margin-top: 10px; }
            .mid { flex-direction: column; gap: 14px; }
            .dates td:first-child { text-align: left; padding-right: 24px; }
            .dates { width: 100%; }
            .items thead { display: none; }
            .items, .items tbody, .items tr, .items td { display: block; width: 100%; }
            .items tr { padding: 12px 0; border-bottom: 1px solid var(--line); }
            .items td { border: 0; padding: 0; }
            .items td.n { display: none; }
            .items td.m { display: flex; justify-content: space-between; padding-top: 3px; font-size: 12.5px; }
            .items td.m::before { content: attr(data-label); color: var(--muted); }
            .items td.m.first { margin-top: 8px; }
            .items td.m.amt { font-weight: 700; color: var(--ink); }
            .items td.m.amt::before { font-weight: 400; }
            table.totals { width: 100%; }
            .words { text-align: left; }
            .signs { flex-direction: column; gap: 40px; margin-top: 48px; }
            .sign.right { text-align: left; }
            .sign.right .line { margin-left: 0; }
        }

        @page { size: A4; margin: 14mm; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .page { margin: 0; max-width: none; box-shadow: none; padding: 0; }
            .items tr { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div class="toolbar-in">
            @unless ($public)
                <a class="tb" href="{{ $back ?? url()->previous() }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>Back</a>
            @endunless
            <span class="sp"></span>
            @if ($share)
                <a class="tb wa" href="{{ $share }}" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2a8.2 8.2 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z"/></svg><span class="lbl-long">WhatsApp</span></a>
            @endif
            <button class="tb primary" onclick="window.print()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0-4-4m4 4 4-4M5 21h14"/></svg>Print / Save PDF</button>
        </div>
    </div>
    <div class="page">
        {{ $slot }}
    </div>
</body>
</html>

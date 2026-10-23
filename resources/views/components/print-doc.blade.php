@props(['title', 'back' => null])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | {{ setting('company_name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <style>
        :root { --ink: #111827; --muted: #6b7280; --line: #e5e7eb; }
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Roboto, Arial, sans-serif; color: var(--ink); margin: 0; background: #f3f4f6; font-size: 13px; }
        .page { max-width: 820px; margin: 16px auto; background: #fff; padding: 28px; border-radius: 8px; }
        .head { display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap; border-bottom: 3px solid #e31e24; padding-bottom: 14px; }
        .head img { height: 70px; }
        .doc-title { text-align: right; }
        .doc-title h1 { margin: 0; font-size: 22px; letter-spacing: 1px; }
        .muted { color: var(--muted); }
        .parties { display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin: 16px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 7px 6px; border-bottom: 1px solid var(--line); text-align: left; vertical-align: top; }
        th { background: #f9fafb; font-size: 11px; text-transform: uppercase; color: var(--muted); }
        .r { text-align: right; } .b { font-weight: 700; }
        .sizes { font-size: 11px; color: var(--muted); }
        .totals { margin-left: auto; width: 300px; max-width: 100%; margin-top: 10px; }
        .totals td { border: 0; padding: 4px 6px; }
        .grand td { border-top: 2px solid var(--ink); font-size: 15px; font-weight: 700; }
        .foot { margin-top: 40px; display: flex; justify-content: space-between; gap: 20px; flex-wrap: wrap; }
        .sign { border-top: 1px solid var(--ink); padding-top: 4px; width: 200px; text-align: center; color: var(--muted); }
        .toolbar { max-width: 820px; margin: 16px auto 0; display: flex; gap: 8px; justify-content: flex-end; }
        .toolbar button, .toolbar a { padding: 8px 16px; border-radius: 6px; border: 1px solid var(--line); background: #fff; cursor: pointer; text-decoration: none; color: var(--ink); font: inherit; }
        .toolbar .primary { background: #1a80f8; color: #fff; border-color: #1a80f8; }
        @media (max-width: 600px) { .page { padding: 16px; margin: 8px; } .doc-title { text-align: left; } }
        @media print { body { background: #fff; } .page { margin: 0; padding: 0; max-width: none; } .toolbar { display: none; } }
    </style>
</head>
<body>
    <div class="toolbar"><a href="{{ $back ?? url()->previous() }}">Back</a><button class="primary" onclick="window.print()">Print / Save PDF</button></div>
    <div class="page">
        <div class="head">
            <div>
                <img src="{{ asset('images/logo-dark.png') }}" alt="{{ setting('company_name') }}">
                <div class="muted">{{ setting('company_address') }} {{ setting('company_phone') ? '· ' . setting('company_phone') : '' }}</div>
            </div>
            <div class="doc-title">
                <h1>{{ $title }}</h1>
                {{ $meta }}
            </div>
        </div>
        {{ $slot }}
    </div>
</body>
</html>

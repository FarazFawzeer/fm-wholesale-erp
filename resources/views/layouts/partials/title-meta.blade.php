<meta charset="utf-8" />
<script>
    // Hide the sidebar before first paint on phones/tablets (avoids a flash while app JS loads)
    if (window.innerWidth <= 1140) document.documentElement.setAttribute('data-sidebar-size', 'hidden');
</script>
<title>{{ isset($subtitle) ? $subtitle . ' | ' : '' }}{{ setting('company_name', 'FM Shoes') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="description" content="FM Shoes - wholesale shoe business system" />
<meta name="author" content="FM Wholesale" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="robots" content="noindex, nofollow" />
<!-- Phone app (Add to Home Screen) -->
<link rel="manifest" href="{{ route('manifest') }}">
<meta name="theme-color" content="#ffffff">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ setting('company_name', 'FM Shoes') }}">
<meta name="application-name" content="{{ setting('company_name', 'FM Shoes') }}">

<!-- App favicon -->
<link rel="icon" type="image/png" sizes="64x64" href="{{ asset('images/favicon.png') }}">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" href="{{ asset('images/app/apple-touch-icon.png') }}">
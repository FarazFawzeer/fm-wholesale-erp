@php
    // Simple menu: one tap to each job
    $menu = [
        ['label' => 'Home', 'icon' => 'solar:home-2-outline', 'route' => 'root'],
        ['title' => 'Daily work'],
        ['label' => 'Sell', 'icon' => 'solar:cart-large-2-outline', 'route' => 'invoices.index', 'match' => 'invoices.*'],
        ['label' => 'Buy', 'icon' => 'solar:box-outline', 'route' => 'purchases.index', 'match' => 'purchases.*'],
        ['label' => 'Returns', 'icon' => 'solar:undo-left-round-outline', 'route' => 'returns.index', 'match' => 'returns.*'],
        ['label' => 'Products', 'icon' => 'ph:sneaker', 'route' => 'products.index', 'match' => 'products.*'],
        ['label' => 'Send catalogue', 'icon' => 'solar:gallery-send-outline', 'route' => 'catalogue.index', 'match' => 'catalogue.*'],
        ['label' => 'Customers', 'icon' => 'solar:users-group-rounded-outline', 'route' => 'customers.index', 'match' => 'customers.*'],
        ['label' => 'Suppliers', 'icon' => 'solar:delivery-outline', 'route' => 'suppliers.index', 'match' => 'suppliers.*'],
        ['title' => 'Money'],
        ['label' => 'Dues', 'icon' => 'solar:bell-bing-outline', 'route' => 'dues', 'match' => 'dues'],
        ['label' => 'Payments', 'icon' => 'solar:wallet-money-outline', 'route' => 'payments.index', 'match' => 'payments.*'],
        ['label' => 'Cheques', 'icon' => 'solar:document-text-outline', 'route' => 'cheques', 'match' => 'cheques*'],
        ['label' => 'Expenses', 'icon' => 'solar:bill-list-outline', 'route' => 'expenses.index', 'match' => 'expenses.*'],
        ['label' => 'Reports', 'icon' => 'solar:chart-square-outline', 'route' => 'reports.index', 'match' => 'reports.*'],
        ['title' => 'System'],
        ['label' => 'Settings', 'icon' => 'solar:settings-outline', 'route' => 'settings.edit', 'match' => ['settings.*', 'users.*']],
    ];
    $dueCount = \App\Support\Alerts::owingCount();
@endphp

<div class="app-sidebar">
    <div class="logo-box">
        <a href="{{ route('root') }}" class="logo-dark">
            <img src="{{ asset('images/logo-sm.png') }}" class="logo-sm" alt="FM Shoes">
            <img src="{{ asset('images/logo-dark.png') }}" class="logo-lg" alt="FM Shoes">
        </a>
        <a href="{{ route('root') }}" class="logo-light">
            <img src="{{ asset('images/logo-sm.png') }}" class="logo-sm" alt="FM Shoes">
            <img src="{{ asset('images/logo-light.png') }}" class="logo-lg" alt="FM Shoes">
        </a>
    </div>

    <div class="scrollbar" data-simplebar>
        <ul class="navbar-nav" id="navbar-nav">
            @foreach ($menu as $item)
                @if (isset($item['title']))
                    <li class="menu-title">{{ $item['title'] }}</li>
                @else
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs(...(array) ($item['match'] ?? $item['route'])) ? 'active' : '' }}" href="{{ route($item['route']) }}">
                            <span class="nav-icon"><iconify-icon icon="{{ $item['icon'] }}"></iconify-icon></span>
                            <span class="nav-text">{{ $item['label'] }}</span>
                            @if ($item['route'] === 'dues' && $dueCount)<span class="badge bg-danger rounded-pill ms-auto">{{ $dueCount }}</span>@endif
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>

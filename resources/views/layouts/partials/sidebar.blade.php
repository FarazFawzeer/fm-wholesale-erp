@php
    $menu = [
        ['title' => 'Overview'],
        ['label' => 'Dashboard', 'icon' => 'solar:home-2-outline', 'route' => 'root'],
        ['title' => 'Import & Stock'],
        ['label' => 'Import & Purchasing', 'icon' => 'solar:globus-outline', 'id' => 'menuPurchase', 'children' => [
            ['Purchase Orders', 'purchase-orders.index'],
            ['New Purchase Order', 'purchase-orders.create'],
            ['Goods Received (GRN)', 'grns.index'],
            ['Suppliers', 'suppliers.index'],
        ]],
        ['label' => 'Inventory', 'icon' => 'solar:box-outline', 'id' => 'menuInventory', 'children' => [
            ['Products', 'products.index'],
            ['Stock on Hand', 'stock.index'],
            ['Stock Movements', 'stock.movements'],
            ['Stock Transfers', 'stock-transfers.index'],
            ['Adjustments & Damages', 'stock-adjustments.index'],
            ['Bulk Import (Excel/CSV)', 'products.import'],
        ]],
        ['label' => 'Pricing', 'icon' => 'solar:tag-price-outline', 'id' => 'menuPricing', 'children' => [
            ['Bulk Price Update', 'products.bulk-price'],
            ['Customer Groups & Price Lists', 'customer-groups.index'],
        ]],
        ['title' => 'Sales'],
        ['label' => 'Sales', 'icon' => 'solar:cart-large-2-outline', 'id' => 'menuSales', 'children' => [
            ['New Invoice', 'invoices.create'],
            ['Invoices', 'invoices.index'],
            ['Quotations', 'quotations.index'],
            ['Sales Orders', 'sales-orders.index'],
            ['Delivery Notes', 'deliveries.index'],
            ['Returns / Credit Notes', 'credit-notes.index'],
        ]],
        ['label' => 'Customers', 'icon' => 'solar:users-group-rounded-outline', 'route' => 'customers.index'],
        ['title' => 'Money'],
        ['label' => 'Payments', 'icon' => 'solar:wallet-money-outline', 'id' => 'menuPayments', 'children' => [
            ['Customer Receipts', 'receipts.index'],
            ['Supplier Payments', 'supplier-payments.index'],
            ['Cheques', 'cheques.index'],
            ['Expenses', 'expenses.index'],
        ]],
        ['label' => 'Cash & Bank', 'icon' => 'solar:card-outline', 'id' => 'menuCash', 'children' => [
            ['Daily Cashbook', 'cashbook'],
            ['Accounts & Balances', 'accounts.index'],
        ]],
        ['label' => 'Credit Control', 'icon' => 'solar:shield-warning-outline', 'id' => 'menuCredit', 'children' => [
            ['Overdue & Reminders', 'credit-control.index'],
            ['Supplier Obligations', 'credit-control.payables'],
        ]],
        ['label' => 'Reports', 'icon' => 'solar:chart-square-outline', 'route' => 'reports.index'],
        ['title' => 'System'],
        ['label' => 'Settings', 'icon' => 'solar:settings-outline', 'id' => 'menuSettings', 'children' => [
            ['Company & Pricing Rules', 'settings.edit'],
            ['Brands, Categories & Stores', 'setup.index'],
            ['Users & Roles', 'users.index'],
        ]],
    ];
@endphp

<div class="app-sidebar">
    <!-- Sidebar Logo -->
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
                @elseif (isset($item['children']))
                    <li class="nav-item">
                        <a class="nav-link menu-arrow" href="#{{ $item['id'] }}" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="{{ $item['id'] }}">
                            <span class="nav-icon"><iconify-icon icon="{{ $item['icon'] }}"></iconify-icon></span>
                            <span class="nav-text">{{ $item['label'] }}</span>
                        </a>
                        <div class="collapse" id="{{ $item['id'] }}">
                            <ul class="nav sub-navbar-nav">
                                @foreach ($item['children'] as [$label, $route])
                                    <li class="sub-nav-item">
                                        <a class="sub-nav-link" href="{{ route($route) }}">{{ $label }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route($item['route']) }}">
                            <span class="nav-icon"><iconify-icon icon="{{ $item['icon'] }}"></iconify-icon></span>
                            <span class="nav-text">{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>

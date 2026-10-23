<?php
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
?>

<div class="app-sidebar">
    <!-- Sidebar Logo -->
    <div class="logo-box">
        <a href="<?php echo e(route('root')); ?>" class="logo-dark">
            <img src="<?php echo e(asset('images/logo-sm.png')); ?>" class="logo-sm" alt="FM Shoes">
            <img src="<?php echo e(asset('images/logo-dark.png')); ?>" class="logo-lg" alt="FM Shoes">
        </a>

        <a href="<?php echo e(route('root')); ?>" class="logo-light">
            <img src="<?php echo e(asset('images/logo-sm.png')); ?>" class="logo-sm" alt="FM Shoes">
            <img src="<?php echo e(asset('images/logo-light.png')); ?>" class="logo-lg" alt="FM Shoes">
        </a>
    </div>

    <div class="scrollbar" data-simplebar>
        <ul class="navbar-nav" id="navbar-nav">
            <?php $__currentLoopData = $menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(isset($item['title'])): ?>
                    <li class="menu-title"><?php echo e($item['title']); ?></li>
                <?php elseif(isset($item['children'])): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-arrow" href="#<?php echo e($item['id']); ?>" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="<?php echo e($item['id']); ?>">
                            <span class="nav-icon"><iconify-icon icon="<?php echo e($item['icon']); ?>"></iconify-icon></span>
                            <span class="nav-text"><?php echo e($item['label']); ?></span>
                        </a>
                        <div class="collapse" id="<?php echo e($item['id']); ?>">
                            <ul class="nav sub-navbar-nav">
                                <?php $__currentLoopData = $item['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $route]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="sub-nav-item">
                                        <a class="sub-nav-link" href="<?php echo e(route($route)); ?>"><?php echo e($label); ?></a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route($item['route'])); ?>">
                            <span class="nav-icon"><iconify-icon icon="<?php echo e($item['icon']); ?>"></iconify-icon></span>
                            <span class="nav-text"><?php echo e($item['label']); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
</div>
<?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/layouts/partials/sidebar.blade.php ENDPATH**/ ?>
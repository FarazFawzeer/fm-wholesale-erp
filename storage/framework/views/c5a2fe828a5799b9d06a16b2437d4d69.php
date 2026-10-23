<?php
    $alerts = \App\Support\Alerts::forTopbar();
?>
<header class="app-topbar">
     <div class="container-fluid">
          <div class="navbar-header">
               <div class="d-flex align-items-center gap-2">
                    <!-- Menu Toggle Button -->
                    <div class="topbar-item">
                         <button type="button" class="button-toggle-menu topbar-button" aria-label="Toggle menu">
                              <iconify-icon icon="solar:hamburger-menu-outline" class="fs-24 align-middle"></iconify-icon>
                         </button>
                    </div>
                    <div class="d-none d-md-flex gap-2">
                         <a href="<?php echo e(route('invoices.create')); ?>" class="btn btn-sm btn-primary"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>New Invoice</a>
                         <a href="<?php echo e(route('receipts.create')); ?>" class="btn btn-sm btn-soft-success">Receive Payment</a>
                         <a href="<?php echo e(route('purchase-orders.create')); ?>" class="btn btn-sm btn-soft-secondary">New PO</a>
                    </div>
               </div>

               <div class="d-flex align-items-center gap-1">
                    <!-- Quick add on phones -->
                    <div class="dropdown topbar-item d-md-none">
                         <button type="button" class="topbar-button" data-bs-toggle="dropdown" aria-label="Quick add">
                              <iconify-icon icon="solar:add-circle-bold" class="fs-24 align-middle text-primary"></iconify-icon>
                         </button>
                         <div class="dropdown-menu dropdown-menu-end">
                              <a class="dropdown-item" href="<?php echo e(route('invoices.create')); ?>">New Invoice</a>
                              <a class="dropdown-item" href="<?php echo e(route('quotations.create')); ?>">New Quotation</a>
                              <a class="dropdown-item" href="<?php echo e(route('receipts.create')); ?>">Receive Payment</a>
                              <a class="dropdown-item" href="<?php echo e(route('purchase-orders.create')); ?>">New Purchase Order</a>
                              <a class="dropdown-item" href="<?php echo e(route('expenses.create')); ?>">New Expense</a>
                         </div>
                    </div>

                    <!-- Theme Color (Light/Dark) -->
                    <div class="topbar-item">
                         <button type="button" class="topbar-button" id="light-dark-mode" aria-label="Toggle dark mode">
                              <iconify-icon icon="solar:moon-outline" class="fs-22 align-middle light-mode"></iconify-icon>
                              <iconify-icon icon="solar:sun-2-outline" class="fs-22 align-middle dark-mode"></iconify-icon>
                         </button>
                    </div>

                    <!-- Alerts -->
                    <div class="dropdown topbar-item">
                         <button type="button" class="topbar-button position-relative" data-bs-toggle="dropdown" aria-label="Alerts">
                              <iconify-icon icon="solar:bell-bing-outline" class="fs-22 align-middle"></iconify-icon>
                              <?php if($alerts->count()): ?>
                                   <span class="position-absolute topbar-badge fs-10 translate-middle badge bg-danger rounded-pill"><?php echo e($alerts->count()); ?></span>
                              <?php endif; ?>
                         </button>
                         <div class="dropdown-menu py-0 dropdown-lg dropdown-menu-end">
                              <div class="p-2 border-bottom bg-light bg-opacity-50">
                                   <h6 class="m-0 fs-16 fw-semibold">Alerts</h6>
                              </div>
                              <?php $__empty_1 = true; $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                   <a href="<?php echo e($alert['url']); ?>" class="dropdown-item p-2 border-bottom text-wrap d-flex gap-2 align-items-start">
                                        <span class="avatar-sm flex-shrink-0 rounded bg-<?php echo e($alert['color']); ?>-subtle text-<?php echo e($alert['color']); ?> d-flex align-items-center justify-content-center">
                                             <iconify-icon icon="<?php echo e($alert['icon']); ?>" class="fs-20"></iconify-icon>
                                        </span>
                                        <span>
                                             <span class="fw-semibold d-block"><?php echo e($alert['title']); ?></span>
                                             <span class="text-muted small"><?php echo e($alert['text']); ?></span>
                                        </span>
                                   </a>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                   <div class="p-3 text-center text-muted small">All clear - no alerts.</div>
                              <?php endif; ?>
                         </div>
                    </div>

                    <!-- User -->
                    <div class="dropdown topbar-item">
                         <a type="button" class="topbar-button" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <span class="d-flex align-items-center">
                                   <img class="rounded-circle" width="32" height="32" style="object-fit: cover" src="<?php echo e(Auth::user()?->image_path ? asset(Auth::user()->image_path) : asset('images/users/avatar-6.jpg')); ?>" alt="avatar">
                              </span>
                         </a>
                         <div class="dropdown-menu dropdown-menu-end">
                              <h6 class="dropdown-header"><?php echo e(Auth::user()?->name); ?><br><span class="fw-normal text-muted"><?php echo e(Auth::user()?->type); ?></span></h6>
                              <a class="dropdown-item" href="<?php echo e(route('admin.profile.edit')); ?>">
                                   <iconify-icon icon="solar:user-outline" class="align-middle me-2 fs-18"></iconify-icon><span class="align-middle">My Account</span>
                              </a>
                              <div class="dropdown-divider my-1"></div>
                              <form method="POST" action="<?php echo e(route('logout')); ?>">
                                   <?php echo csrf_field(); ?>
                                   <button type="submit" class="dropdown-item text-danger">
                                        <iconify-icon icon="solar:logout-3-outline" class="align-middle me-2 fs-18"></iconify-icon><span class="align-middle">Logout</span>
                                   </button>
                              </form>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</header>
<?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/layouts/partials/topbar.blade.php ENDPATH**/ ?>
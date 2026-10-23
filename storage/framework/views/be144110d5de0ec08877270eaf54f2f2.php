<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Reports','crumbs' => ['Reports']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Reports','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Reports'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>

    <?php
        $groups = [
            'Stock' => [
                ['Stock valuation', 'reports.stock-valuation', 'solar:box-outline', 'Pairs and value at landed cost, by category and store'],
                ['Low stock & missing sizes', 'reports.low-stock', 'solar:box-minimalistic-outline', 'Below re-order level or sizes sold out'],
                ['Import costing', 'reports.import-costing', 'solar:globus-outline', 'Freight, duty and charges per shipment'],
                ['Stock movements', 'stock.movements', 'solar:transfer-horizontal-outline', 'Every stock in / out with reference'],
            ],
            'Sales & profit' => [
                ['Product profit', 'reports.product-profit', 'solar:graph-up-outline', 'Sales, landed cost and margin per product'],
                ['Invoice profit', 'reports.invoice-profit', 'solar:bill-list-outline', 'Gross profit per invoice and net after expenses'],
            ],
            'Customers & money' => [
                ['Customer statement', 'reports.statement', 'solar:document-text-outline', 'Invoices, returns and payments with running balance'],
                ['Outstanding & ageing', 'reports.outstanding', 'solar:alarm-outline', 'Who owes what, by days overdue'],
                ['Cash flow', 'reports.cash-flow', 'solar:wallet-money-outline', 'Money in and out by type and week'],
                ['Expected payments', 'reports.expected', 'solar:calendar-outline', 'Incoming and outgoing for the coming weeks'],
                ['Daily cashbook', 'cashbook', 'solar:book-outline', 'Opening, in, out and closing per account'],
            ],
        ];
    ?>

    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $title => $reports): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <h6 class="text-uppercase text-muted fw-semibold small mt-2 mb-2"><?php echo e($title); ?></h6>
        <div class="row g-3 mb-3">
            <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $route, $icon, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-sm-6 col-xl-3">
                    <a href="<?php echo e(route($route)); ?>" class="card h-100 text-reset mb-0">
                        <div class="card-body d-flex gap-3">
                            <div class="avatar-md flex-shrink-0 rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center"><iconify-icon icon="<?php echo e($icon); ?>" class="fs-24"></iconify-icon></div>
                            <div><div class="fw-semibold"><?php echo e($label); ?></div><div class="small text-muted"><?php echo e($desc); ?></div></div>
                        </div>
                    </a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Reports'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/reports/index.blade.php ENDPATH**/ ?>
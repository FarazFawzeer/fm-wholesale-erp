<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Delivery Notes','crumbs' => ['Sales', 'Delivery Notes']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Delivery Notes','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Sales', 'Delivery Notes'])]); ?>
        <a href="<?php echo e(route('invoices.index', ['delivery' => 'pending'])); ?>" class="btn btn-soft-primary">Pending deliveries</a>
        <a href="<?php echo e(route('invoices.index', ['delivery' => 'partial'])); ?>" class="btn btn-soft-warning">Partly delivered</a>
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

    <div class="card">
        <div class="card-body border-bottom"><form class="filter-bar"><input type="search" name="q" value="<?php echo e(request('q')); ?>" class="form-control filter-grow" placeholder="DN number or customer"><button class="btn btn-light">Search</button></form></div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack mb-0">
                    <thead><tr><th>Delivery note</th><th>Invoice</th><th>Customer</th><th>Date</th><th>Store</th><th>Vehicle</th><th class="text-end">Pairs</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="td-title"><a href="<?php echo e(route('deliveries.show', $n)); ?>" class="doc-link"><?php echo e($n->number); ?></a></td>
                                <td data-label="Invoice"><a href="<?php echo e(route('invoices.show', $n->invoice)); ?>"><?php echo e($n->invoice->number); ?></a></td>
                                <td data-label="Customer"><?php echo e($n->customer->name); ?></td>
                                <td data-label="Date"><?php echo e($n->date->format('d M Y')); ?></td>
                                <td data-label="Store"><?php echo e($n->warehouse->code); ?></td>
                                <td data-label="Vehicle"><?php echo e($n->vehicle_no ?: '-'); ?></td>
                                <td data-label="Pairs" class="text-end fw-semibold"><?php echo e(qty($n->pairs)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['cols' => 7,'text' => 'No deliveries yet.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['cols' => 7,'text' => 'No deliveries yet.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4f22a152e0729cd34293e65bd200d933)): ?>
<?php $attributes = $__attributesOriginal4f22a152e0729cd34293e65bd200d933; ?>
<?php unset($__attributesOriginal4f22a152e0729cd34293e65bd200d933); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4f22a152e0729cd34293e65bd200d933)): ?>
<?php $component = $__componentOriginal4f22a152e0729cd34293e65bd200d933; ?>
<?php unset($__componentOriginal4f22a152e0729cd34293e65bd200d933); ?>
<?php endif; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($notes->hasPages()): ?><div class="card-footer"><?php echo e($notes->links()); ?></div><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Delivery Notes'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/sales/deliveries/index.blade.php ENDPATH**/ ?>
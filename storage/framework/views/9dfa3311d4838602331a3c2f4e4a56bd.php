<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Stock Movements','crumbs' => ['Inventory', 'Movements']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Stock Movements','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Inventory', 'Movements'])]); ?>
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
        <div class="card-body border-bottom">
            <form class="filter-bar">
                <input type="search" name="q" value="<?php echo e(request('q')); ?>" class="form-control filter-grow" placeholder="SKU or document no">
                <select name="type" class="form-select"><option value="">All types</option><?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($t); ?>" <?php if(request('type') == $t): echo 'selected'; endif; ?>><?php echo e(ucwords(str_replace('_', ' ', $t))); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
                <select name="warehouse_id" class="form-select"><option value="">All stores</option><?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($w->id); ?>" <?php if(request('warehouse_id') == $w->id): echo 'selected'; endif; ?>><?php echo e($w->code); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
                <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="form-control">
                <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="form-control">
                <button class="btn btn-light">Filter</button>
            </form>
        </div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Date / SKU</th><th>Product</th><th>Type</th><th>Store</th><th class="text-end">Pairs</th><th class="text-end">Unit cost</th><th>Reference</th><th>User</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="td-title"><?php echo e($m->date->format('d M Y')); ?> &middot; <strong><?php echo e($m->variant->sku); ?></strong> <span class="d-md-none small text-muted"><?php echo e($m->variant->product->name); ?></span></td>
                                <td data-label="Product" class="d-none d-md-table-cell"><?php echo e($m->variant->product->name); ?> <?php echo e($m->variant->product->colour); ?></td>
                                <td data-label="Type"><?php echo e(ucwords(str_replace('_', ' ', $m->type))); ?></td>
                                <td data-label="Store"><?php echo e($m->warehouse->code); ?></td>
                                <td data-label="Pairs" class="text-end fw-semibold <?php echo e($m->qty < 0 ? 'text-danger' : 'text-success'); ?>"><?php echo e($m->qty > 0 ? '+' : ''); ?><?php echo e($m->qty); ?></td>
                                <td data-label="Cost" class="text-end money"><?php echo e(money($m->unit_cost)); ?></td>
                                <td data-label="Ref"><?php echo e($m->reference_no); ?><div class="small text-muted"><?php echo e($m->note); ?></div></td>
                                <td data-label="User"><?php echo e($m->user?->name); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['cols' => 8,'text' => 'No movements.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['cols' => 8,'text' => 'No movements.']); ?>
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
        <?php if($movements->hasPages()): ?><div class="card-footer"><?php echo e($movements->links()); ?></div><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Stock Movements'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/inventory/stock/movements.blade.php ENDPATH**/ ?>
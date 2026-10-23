<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'New Stock Transfer','crumbs' => ['Transfers' => route('stock-transfers.index'), 'New']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'New Stock Transfer','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Transfers' => route('stock-transfers.index'), 'New'])]); ?>
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

    <form method="POST" action="<?php echo e(route('stock-transfers.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                <div class="card">
                    <div class="card-body" data-line-editor data-mode="stock" data-check-stock="1" data-products='<?php echo json_encode($catalog, 15, 512) ?>' data-initial='<?php echo json_encode($initial, 15, 512) ?>'>
                        <select class="product-picker"></select>
                        <div class="lines mt-3"></div>
                        <div class="empty-lines text-center text-muted py-4">Add products and enter pairs per size to move.</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 order-1 order-xl-2">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3"><label class="form-label">From store</label>
                            <select name="from_warehouse_id" id="warehouse_id" class="form-select"><?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($w->id); ?>" <?php if($w->is_default): echo 'selected'; endif; ?>><?php echo e($w->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                        <div class="mb-3"><label class="form-label">To store</label>
                            <select name="to_warehouse_id" class="form-select"><?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($w->id); ?>" <?php if(! $w->is_default): echo 'selected'; endif; ?>><?php echo e($w->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                        <div class="mb-3"><label class="form-label">Date</label><input type="date" name="date" value="<?php echo e(now()->format('Y-m-d')); ?>" class="form-control"></div>
                        <div><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                        <div class="totals-box border-top mt-3 pt-2"><div class="row-total grand"><span>Pairs</span><span class="js-sum-pairs">0</span></div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="<?php echo e(route('stock-transfers.index')); ?>" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">Transfer stock</button>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'New Stock Transfer'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/inventory/transfers/create.blade.php ENDPATH**/ ?>
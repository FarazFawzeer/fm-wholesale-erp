<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'New Stock Adjustment','crumbs' => ['Adjustments' => route('stock-adjustments.index'), 'New']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'New Stock Adjustment','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Adjustments' => route('stock-adjustments.index'), 'New'])]); ?>
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

    <form method="POST" action="<?php echo e(route('stock-adjustments.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                <div class="card">
                    <div class="card-body" data-line-editor data-mode="stock" data-products='<?php echo json_encode($catalog, 15, 512) ?>' data-initial='<?php echo json_encode($initial, 15, 512) ?>'>
                        <select class="product-picker"></select>
                        <div class="lines mt-3"></div>
                        <div class="empty-lines text-center text-muted py-4">Add products and enter the pairs affected per size (always positive numbers).</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 order-1 order-xl-2">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3"><label class="form-label">Reason</label>
                            <select name="reason" id="reason" class="form-select"><?php $__currentLoopData = \App\Models\StockAdjustment::REASONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php if(old('reason') == $k): echo 'selected'; endif; ?>><?php echo e($l); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
                            <div class="form-text" id="reason-help"></div>
                        </div>
                        <div class="mb-3" id="direction-wrap"><label class="form-label">Count result</label>
                            <select name="direction" class="form-select"><option value="remove">Less than system (remove)</option><option value="add">More than system (add)</option></select></div>
                        <div class="mb-3"><label class="form-label">Store</label>
                            <select name="warehouse_id" id="warehouse_id" class="form-select"><?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($w->id); ?>" <?php if($w->is_default): echo 'selected'; endif; ?>><?php echo e($w->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                        <div class="mb-3"><label class="form-label">Date</label><input type="date" name="date" value="<?php echo e(now()->format('Y-m-d')); ?>" class="form-control"></div>
                        <div><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2" placeholder="Why? (e.g. water damage, count on 23 Oct)"></textarea></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="<?php echo e(route('stock-adjustments.index')); ?>" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">Post adjustment</button>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('page-js'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const help = {
                count: 'Correct the system after a physical count.',
                damaged: 'Moves good stock to damaged (not sellable).',
                damaged_writeoff: 'Removes damaged stock permanently.',
                lost: 'Removes missing stock.',
                found: 'Adds surplus stock found.',
                supplier_return: 'Sends stock back to the supplier.',
            };
            const r = document.getElementById('reason');
            const sync = () => {
                document.getElementById('reason-help').textContent = help[r.value] || '';
                document.getElementById('direction-wrap').classList.toggle('d-none', r.value !== 'count');
            };
            r.addEventListener('change', sync);
            sync();
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'New Stock Adjustment'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/inventory/adjustments/create.blade.php ENDPATH**/ ?>
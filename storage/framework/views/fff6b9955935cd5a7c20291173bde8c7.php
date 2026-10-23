<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Deliver ' . $invoice->number,'crumbs' => ['Invoices' => route('invoices.index'), $invoice->number => route('invoices.show', $invoice), 'Deliver']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Deliver ' . $invoice->number),'crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Invoices' => route('invoices.index'), $invoice->number => route('invoices.show', $invoice), 'Deliver'])]); ?>
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

    <form method="POST" action="<?php echo e(route('deliveries.store', $invoice)); ?>">
        <?php echo csrf_field(); ?>
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                <div class="alert alert-info small">Enter what is going out now. Leave the rest for a later delivery (partial delivery). Stock is taken from <strong><?php echo e($invoice->warehouse->name); ?></strong>.</div>
                <?php $__currentLoopData = $byProduct; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $p = $rows->first()->product; $rows = $rows->sortBy(fn ($r) => (float) $r->variant->size); ?>
                    <div class="line-card">
                        <div class="line-head"><div><div class="line-title"><?php echo e($p->code); ?> - <?php echo e($p->name); ?> <?php echo e($p->colour); ?></div></div>
                            <button type="button" class="btn btn-sm btn-light" onclick="this.closest('.line-card').querySelectorAll('input').forEach(i => i.value = i.max)">Fill all</button></div>
                        <div class="size-grid">
                            <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $left = $r->qty - $r->delivered_qty; $stock = (int) $r->variant->stocks->firstWhere('warehouse_id', $invoice->warehouse_id)?->qty; ?>
                                <div class="size-cell">
                                    <label><?php echo e($r->variant->size); ?></label>
                                    <input type="number" min="0" max="<?php echo e($left); ?>" name="qty[<?php echo e($r->id); ?>]" value="<?php echo e(min($left, $stock) ?: ''); ?>" placeholder="0" class="form-control form-control-sm" <?php if($left <= 0): echo 'disabled'; endif; ?>>
                                    <small><?php echo e($left); ?> due</small>
                                    <small class="<?php echo e($stock < $left ? 'low' : ''); ?>"><?php echo e($stock); ?> stock</small>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="col-xl-4 order-1 order-xl-2">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-2 small text-muted">Customer</div>
                        <div class="fw-semibold mb-3"><?php echo e($invoice->customer->name); ?><div class="small text-muted fw-normal"><?php echo e($invoice->customer->address); ?> <?php echo e($invoice->customer->city); ?></div></div>
                        <div class="mb-3"><label class="form-label">Delivery date</label><input type="date" name="date" value="<?php echo e(now()->format('Y-m-d')); ?>" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Vehicle no</label><input name="vehicle_no" class="form-control" placeholder="e.g. LB-4521"></div>
                        <div class="mb-3"><label class="form-label">Received by</label><input name="received_by" class="form-control"></div>
                        <div><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="<?php echo e(route('invoices.show', $invoice)); ?>" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">Create delivery note</button>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Deliver ' . $invoice->number], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/sales/deliveries/create.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Return / Credit Note - ' . $invoice->number,'crumbs' => ['Invoices' => route('invoices.index'), $invoice->number => route('invoices.show', $invoice), 'Return']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Return / Credit Note - ' . $invoice->number),'crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Invoices' => route('invoices.index'), $invoice->number => route('invoices.show', $invoice), 'Return'])]); ?>
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

    <form method="POST" action="<?php echo e(route('credit-notes.store', $invoice)); ?>">
        <?php echo csrf_field(); ?>
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                <?php $__currentLoopData = $byProduct; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $p = $rows->first()->product; $rows = $rows->sortBy(fn ($r) => (float) $r->variant->size); ?>
                    <div class="line-card">
                        <div class="line-head"><div><div class="line-title"><?php echo e($p->code); ?> - <?php echo e($p->name); ?> <?php echo e($p->colour); ?></div>
                            <div class="line-meta">Net price <?php echo e(money($rows->first()->line_total / max(1, $rows->first()->qty))); ?> / pair</div></div></div>
                        <div class="size-grid">
                            <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="size-cell">
                                    <label><?php echo e($r->variant->size); ?></label>
                                    <input type="number" min="0" name="qty[<?php echo e($r->id); ?>]" placeholder="0" class="form-control form-control-sm" max="<?php echo e($r->qty - $r->returned_qty); ?>">
                                    <small>sold <?php echo e($r->qty); ?></small>
                                    <?php if($r->returned_qty): ?><small class="low">ret <?php echo e($r->returned_qty); ?></small><?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="col-xl-4 order-1 order-xl-2">
                <div class="card">
                    <div class="card-body">
                        <div class="fw-semibold mb-3"><?php echo e($invoice->customer->name); ?></div>
                        <div class="mb-3"><label class="form-label">Date</label><input type="date" name="date" value="<?php echo e(now()->format('Y-m-d')); ?>" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Reason</label>
                            <select name="reason" class="form-select">
                                <?php $__currentLoopData = ['Wrong size sent', 'Damaged / defective', 'Customer returned unsold stock', 'Wrong item sent', 'Price adjustment']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option><?php echo e($r); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select></div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="restock" value="0">
                            <input class="form-check-input" type="checkbox" name="restock" value="1" id="restock" checked>
                            <label class="form-check-label" for="restock">Goods came back - add to stock</label>
                        </div>
                        <div class="form-text">Untick for a price adjustment only (no stock movement).</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="<?php echo e(route('invoices.show', $invoice)); ?>" class="btn btn-light">Cancel</a>
            <button class="btn btn-danger">Create credit note</button>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Return ' . $invoice->number], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/sales/credit-notes/create.blade.php ENDPATH**/ ?>
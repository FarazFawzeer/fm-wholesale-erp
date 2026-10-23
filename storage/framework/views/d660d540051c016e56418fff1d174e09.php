<?php $in = $direction === 'in'; $label = $in ? 'Receive Payment' : 'Pay Supplier'; $param = $in ? 'customer_id' : 'supplier_id'; ?>


<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $label,'crumbs' => [($in ? 'Receipts' : 'Supplier Payments') => route($in ? 'receipts.index' : 'supplier-payments.index'), 'New']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($label),'crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([($in ? 'Receipts' : 'Supplier Payments') => route($in ? 'receipts.index' : 'supplier-payments.index'), 'New'])]); ?>
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
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label"><?php echo e($in ? 'Customer' : 'Supplier'); ?></label>
                    <select name="<?php echo e($param); ?>" class="form-select" onchange="this.form.submit()">
                        <option value="">Select <?php echo e($in ? 'customer' : 'supplier'); ?></option>
                        <?php $__currentLoopData = $parties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p->id); ?>" <?php if($party?->id == $p->id): echo 'selected'; endif; ?>><?php echo e($p->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php if($party): ?>
                    <div class="col-md-6 small">
                        <span class="text-muted"><?php echo e($in ? 'Outstanding' : 'We owe'); ?>:</span> <strong class="money"><?php echo e(money($party->balance())); ?></strong>
                        <?php if($in && $party->overdueAmount() > 0): ?> &middot; <span class="text-danger">overdue <?php echo e(money($party->overdueAmount())); ?></span><?php endif; ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if($party): ?>
        <form method="POST" action="<?php echo e(route('payments.store')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="direction" value="<?php echo e($direction); ?>">
            <input type="hidden" name="party_id" value="<?php echo e($party->id); ?>">
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Payment methods</h5></div>
                        <div class="card-body" data-payment-lines data-accounts='<?php echo json_encode($accounts, 15, 512) ?>' data-start-empty="0" data-target="<?php echo e($focusInvoice ? $documents->firstWhere('id', $focusInvoice)?->balance() : ''); ?>">
                            <div class="row g-2 mb-3">
                                <div class="col-6"><label class="form-label">Date</label><input type="date" name="date" value="<?php echo e(now()->format('Y-m-d')); ?>" class="form-control"></div>
                            </div>
                            <div class="pay-lines"></div>
                            <button type="button" class="btn btn-sm btn-light js-add-pay"><iconify-icon icon="solar:add-circle-outline" class="align-middle"></iconify-icon> Add another method (split)</button>
                            <div class="totals-box mt-3"><div class="row-total grand"><span>Total <?php echo e($in ? 'received' : 'paid'); ?></span><span class="js-paid money">0.00</span></div></div>
                            <div class="mt-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card" data-allocations>
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Apply to <?php echo e($in ? 'invoices' : 'purchase orders'); ?></h5>
                            <div class="form-check form-switch mb-0"><input class="form-check-input js-auto" type="checkbox" id="auto" <?php echo e($focusInvoice ? '' : 'checked'); ?>><label class="form-check-label small" for="auto">Oldest first</label></div>
                        </div>
                        <div class="card-body table-wrap p-0">
                            <div class="table-responsive">
                                <table class="table erp-table stack mb-0">
                                    <thead><tr><th>Document</th><th>Due</th><th class="text-end">Balance</th><th class="text-end">Apply</th></tr></thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <?php $bal = $in ? $d->balance() : $d->balance(); $overdue = $d->due_date && $d->due_date->isPast(); ?>
                                            <tr>
                                                <td class="td-title"><strong><?php echo e($d->number); ?></strong> <span class="small text-muted"><?php echo e(($in ? $d->date : $d->order_date)->format('d M Y')); ?></span></td>
                                                <td data-label="Due" class="<?php echo e($overdue ? 'text-danger fw-semibold' : ''); ?>"><?php echo e($d->due_date?->format('d M Y') ?? '-'); ?></td>
                                                <td data-label="Balance" class="text-end money"><?php echo e(money($bal)); ?></td>
                                                <td data-label="Apply" class="text-end"><input type="number" step="0.01" min="0" max="<?php echo e($bal); ?>" name="alloc[<?php echo e($d->id); ?>]" data-due="<?php echo e($bal); ?>" value="<?php echo e($focusInvoice == $d->id ? $bal : ''); ?>" class="form-control form-control-sm text-end js-alloc ms-auto" style="max-width: 140px"></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['cols' => 4,'text' => 'Nothing outstanding. The amount will be kept as an advance.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['cols' => 4,'text' => 'Nothing outstanding. The amount will be kept as an advance.']); ?>
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
                                    <tfoot class="table-light"><tr><td colspan="3" class="text-end">Applied</td><td class="text-end money fw-semibold js-alloc-total">0.00</td></tr></tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer small text-muted">Any amount not applied stays on the account as an advance.</div>
                    </div>
                </div>
            </div>
            <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
                <a href="<?php echo e(url()->previous()); ?>" class="btn btn-light">Cancel</a>
                <button class="btn btn-success px-4"><?php echo e($in ? 'Save receipt' : 'Save payment'); ?></button>
            </div>
        </form>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => $label], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/finance/payments/create.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $po->exists ? 'Edit ' . $po->number : 'New Purchase Order','crumbs' => ['Purchase Orders' => route('purchase-orders.index'), $po->exists ? 'Edit' : 'New']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($po->exists ? 'Edit ' . $po->number : 'New Purchase Order'),'crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Purchase Orders' => route('purchase-orders.index'), $po->exists ? 'Edit' : 'New'])]); ?>
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

    <form method="POST" action="<?php echo e($po->exists ? route('purchase-orders.update', $po) : route('purchase-orders.store')); ?>">
        <?php echo csrf_field(); ?>
        <?php if($po->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Products (pairs per size)</h5></div>
                    <div class="card-body" data-line-editor data-mode="purchase" data-price-label="Unit price (<?php echo e($po->currency ?: 'USD'); ?>)"
                        data-products='<?php echo json_encode($catalog, 15, 512) ?>' data-initial='<?php echo json_encode($initial, 15, 512) ?>'>
                        <select class="product-picker"></select>
                        <div class="lines mt-3"></div>
                        <div class="empty-lines text-center text-muted py-4">Search and add the shoes you are ordering. Use <strong>Quick fill</strong> to spread cartons across the size run.</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 order-1 order-xl-2">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Order details</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Supplier *</label>
                            <select name="supplier_id" class="form-select" data-choices required>
                                <option value="">Select supplier</option>
                                <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(old('supplier_id', $po->supplier_id) == $s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?> (<?php echo e($s->currency); ?>)</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="row g-2">
                            <div class="col-6 mb-3"><label class="form-label">Order date *</label><input type="date" name="order_date" class="form-control" value="<?php echo e(old('order_date', $po->order_date?->format('Y-m-d'))); ?>" required></div>
                            <div class="col-6 mb-3"><label class="form-label">Expected arrival</label><input type="date" name="expected_date" class="form-control" value="<?php echo e(old('expected_date', $po->expected_date?->format('Y-m-d'))); ?>"></div>
                            <div class="col-5 mb-3"><label class="form-label">Currency *</label>
                                <select name="currency" class="form-select"><?php $__currentLoopData = ['USD', 'CNY', 'EUR', 'INR', 'THB', 'LKR']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option <?php if(old('currency', $po->currency) == $c): echo 'selected'; endif; ?>><?php echo e($c); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                            <div class="col-7 mb-3"><label class="form-label">Exchange rate (Rs) *</label><input type="number" step="0.0001" name="exchange_rate" class="form-control" value="<?php echo e(old('exchange_rate', $po->exchange_rate)); ?>" required></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Container / shipment ref</label><input name="shipment_ref" class="form-control" value="<?php echo e(old('shipment_ref', $po->shipment_ref)); ?>" placeholder="e.g. MSKU 7712093"></div>
                        <div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" rows="2" class="form-control"><?php echo e(old('notes', $po->notes)); ?></textarea></div>
                        <div class="totals-box border-top pt-2">
                            <div class="row-total"><span>Total pairs</span><strong class="js-sum-pairs">0</strong></div>
                            <div class="row-total grand"><span>Supplier total (<?php echo e($po->currency ?: 'USD'); ?>)</span><span class="js-sum-total money">0.00</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="<?php echo e($po->exists ? route('purchase-orders.show', $po) : route('purchase-orders.index')); ?>" class="btn btn-light">Cancel</a>
            <button name="save_as" value="draft" class="btn btn-soft-secondary">Save draft</button>
            <button name="save_as" value="ordered" class="btn btn-primary">Save &amp; mark ordered</button>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => $po->exists ? 'Edit ' . $po->number : 'New Purchase Order'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/purchasing/orders/form.blade.php ENDPATH**/ ?>
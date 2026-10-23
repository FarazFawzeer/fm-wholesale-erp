<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'New Invoice','crumbs' => ['Invoices' => route('invoices.index'), 'New']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'New Invoice','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Invoices' => route('invoices.index'), 'New'])]); ?>
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

    <?php if($order): ?>
        <div class="alert alert-info small">Creating invoice from <?php echo e($order->label()); ?> <a href="<?php echo e(route('sales-docs.show', $order)); ?>"><?php echo e($order->number); ?></a>. Check quantities against stock before saving.</div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('invoices.store')); ?>" id="invoice-form">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="sales_order_id" value="<?php echo e($order?->id); ?>">
        <div class="row">
            <div class="col-xl-8 order-2 order-xl-1">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Items (pairs per size)</h5></div>
                    <div class="card-body" data-line-editor data-mode="sales" data-check-stock="1"
                        data-max-discount="<?php echo e(setting('max_discount_percent')); ?>" data-manager="<?php echo e(auth()->user()->isManager() ? 1 : 0); ?>"
                        data-products='<?php echo json_encode($catalog, 15, 512) ?>' data-initial='<?php echo json_encode($initial, 15, 512) ?>'>
                        <select class="product-picker"></select>
                        <div class="lines mt-3"></div>
                        <div class="empty-lines text-center text-muted py-4">Search a product to add it. Prices follow the customer's group automatically.</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 order-1 order-xl-2">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Customer *</label>
                            <?php echo $__env->make('sales.partials.customer-select', ['selected' => $customerId], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                        <div class="row g-2">
                            <div class="col-6 mb-3"><label class="form-label">Date *</label><input type="date" name="date" value="<?php echo e(old('date', now()->format('Y-m-d'))); ?>" class="form-control" required></div>
                            <div class="col-6 mb-3"><label class="form-label">Store</label>
                                <select name="warehouse_id" id="warehouse_id" class="form-select"><?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($w->id); ?>" <?php if(old('warehouse_id', $w->is_default ? $w->id : null) == $w->id): echo 'selected'; endif; ?>><?php echo e($w->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Sale type</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="sale_type" id="st-credit" value="credit" <?php if(old('sale_type', 'credit') === 'credit'): echo 'checked'; endif; ?>>
                                <label class="btn btn-outline-primary" for="st-credit">Credit sale</label>
                                <input type="radio" class="btn-check" name="sale_type" id="st-cash" value="cash" <?php if(old('sale_type') === 'cash'): echo 'checked'; endif; ?>>
                                <label class="btn btn-outline-primary" for="st-cash">Cash sale</label>
                            </div>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="deliver_now" value="1" id="deliver_now" <?php if(old('deliver_now', true)): echo 'checked'; endif; ?>>
                            <label class="form-check-label" for="deliver_now">Deliver all items now (create delivery note)</label>
                        </div>
                        <?php if(auth()->user()->isManager()): ?>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="override_credit" value="1" id="override_credit" <?php if(old('override_credit')): echo 'checked'; endif; ?>>
                                <label class="form-check-label" for="override_credit">Manager override: allow over credit limit / overdue</label>
                            </div>
                        <?php endif; ?>
                        <div class="mb-0"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes')); ?></textarea></div>
                        <div class="totals-box border-top mt-3 pt-2">
                            <div class="row-total"><span>Pairs</span><strong class="js-sum-pairs">0</strong></div>
                            <div class="row-total"><span>Gross</span><span class="js-sum-gross money">0.00</span></div>
                            <div class="row-total text-danger"><span>Discount</span><span class="js-sum-discount money">0.00</span></div>
                            <div class="row-total grand"><span>Total (Rs)</span><span class="js-sum-total money">0.00</span></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Payment</h5>
                        <div class="form-check form-switch mb-0" id="take-payment-wrap">
                            <input class="form-check-input" type="checkbox" name="take_payment" value="1" id="take_payment" <?php if(old('take_payment')): echo 'checked'; endif; ?>>
                            <label class="form-check-label small" for="take_payment">Paid now</label>
                        </div>
                    </div>
                    <div class="card-body" id="payment-box" data-payment-lines data-accounts='<?php echo json_encode($accounts, 15, 512) ?>' data-initial='<?php echo json_encode(array_values(old('payments', [])), 512) ?>'>
                        <div class="pay-lines"></div>
                        <button type="button" class="btn btn-sm btn-light js-add-pay"><iconify-icon icon="solar:add-circle-outline" class="align-middle"></iconify-icon> Add another method (split payment)</button>
                        <div class="totals-box mt-2 small">
                            <div class="row-total"><span>Paid</span><strong class="js-paid">0.00</strong></div>
                            <div class="row-total"><span>Balance on credit</span><strong class="js-balance">0.00</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="<?php echo e(route('invoices.index')); ?>" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary px-4">Save invoice</button>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('page-js'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const box = document.getElementById('payment-box');
            const take = document.getElementById('take_payment');
            const wrap = document.getElementById('take-payment-wrap');
            const sync = () => {
                const cash = document.getElementById('st-cash').checked;
                wrap.classList.toggle('d-none', cash);
                const on = cash || take.checked;
                box.classList.toggle('d-none', !on);
                box.querySelectorAll('input, select').forEach((i) => (i.disabled = !on));
            };
            document.querySelectorAll('[name=sale_type]').forEach((r) => r.addEventListener('change', sync));
            take.addEventListener('change', sync);
            setTimeout(sync, 0);
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'New Invoice'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/sales/invoices/create.blade.php ENDPATH**/ ?>
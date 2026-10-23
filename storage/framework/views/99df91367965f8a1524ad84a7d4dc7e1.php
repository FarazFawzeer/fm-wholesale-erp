<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Company & Pricing Rules','crumbs' => ['Settings']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Company & Pricing Rules','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Settings'])]); ?>
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

    <form method="POST" action="<?php echo e(route('settings.update')); ?>">
        <?php echo csrf_field(); ?>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Company (shown on invoices)</h5></div>
                    <div class="card-body">
                        <div class="mb-3"><label class="form-label">Company name</label><input name="company_name" value="<?php echo e($s['company_name']); ?>" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Tagline</label><input name="company_tagline" value="<?php echo e($s['company_tagline']); ?>" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Address</label><input name="company_address" value="<?php echo e($s['company_address']); ?>" class="form-control"></div>
                        <div><label class="form-label">Phone</label><input name="company_phone" value="<?php echo e($s['company_phone']); ?>" class="form-control"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Pricing, discount & credit rules</h5></div>
                    <div class="card-body">
                        <div class="mb-3"><label class="form-label">Default markup on landed cost (%)</label><input type="number" step="0.01" name="default_markup_percent" value="<?php echo e($s['default_markup_percent']); ?>" class="form-control">
                            <div class="form-text">Used for suggested prices when the product and its category have no markup rule.</div></div>
                        <div class="mb-3"><label class="form-label">Minimum margin over cost (%)</label><input type="number" step="0.01" name="min_margin_percent" value="<?php echo e($s['min_margin_percent']); ?>" class="form-control">
                            <div class="form-text">Sets the minimum selling price when a product has no fixed minimum. Sales staff can't sell below it.</div></div>
                        <div class="mb-3"><label class="form-label">Maximum discount for sales staff (%)</label><input type="number" step="0.01" name="max_discount_percent" value="<?php echo e($s['max_discount_percent']); ?>" class="form-control">
                            <div class="form-text">Managers can go above this.</div></div>
                        <div class="row g-2">
                            <div class="col-6"><label class="form-label">Default credit terms (days)</label><input type="number" name="default_payment_terms" value="<?php echo e($s['default_payment_terms']); ?>" class="form-control"></div>
                            <div class="col-6"><label class="form-label">Remind before due (days)</label><input type="number" name="reminder_days_before_due" value="<?php echo e($s['reminder_days_before_due']); ?>" class="form-control"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex justify-content-end mb-4"><button class="btn btn-primary">Save settings</button></div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Settings'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/settings/edit.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $group->name . ' - Price List','crumbs' => ['Customer Groups' => route('customer-groups.index'), 'Price list']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($group->name . ' - Price List'),'crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Customer Groups' => route('customer-groups.index'), 'Price list'])]); ?>
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

    <form method="POST" action="<?php echo e(route('customer-groups.prices.save', $group)); ?>">
        <?php echo csrf_field(); ?>
        <div class="card">
            <div class="card-header small text-muted">Leave a price empty to use the group rule (<?php echo e($group->discount_percent >= 0 ? $group->discount_percent . '% off' : abs($group->discount_percent) . '% above'); ?> wholesale).</div>
            <div class="card-body table-wrap p-0">
                <div class="table-responsive">
                    <table class="table erp-table stack mb-0">
                        <thead><tr><th>Product</th><th class="text-end">Landed cost</th><th class="text-end">Wholesale</th><th class="text-end">Group rule price</th><th class="text-end">Special price</th></tr></thead>
                        <tbody>
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $special = $p->priceListItems->firstWhere('customer_group_id', $group->id); ?>
                                <tr>
                                    <td class="td-title"><strong><?php echo e($p->code); ?></strong> <span class="small text-muted"><?php echo e($p->brand?->name); ?> <?php echo e($p->name); ?> <?php echo e($p->colour); ?></span></td>
                                    <td data-label="Cost" class="text-end money"><?php echo e(money($p->cost_price)); ?></td>
                                    <td data-label="Wholesale" class="text-end money"><?php echo e(money($p->selling_price)); ?></td>
                                    <td data-label="Rule price" class="text-end money"><?php echo e(money(round($p->selling_price * (1 - $group->discount_percent / 100), 2))); ?></td>
                                    <td data-label="Special" class="text-end"><input type="number" step="0.01" name="prices[<?php echo e($p->id); ?>]" value="<?php echo e($special?->price); ?>" class="form-control form-control-sm text-end ms-auto" style="max-width: 130px" placeholder="-"></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end sticky-actions"><button class="btn btn-primary">Save price list</button></div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => $group->name . ' Price List'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/masters/customer-groups/prices.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Customer Groups & Price Lists','crumbs' => ['Pricing', 'Customer Groups']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Customer Groups & Price Lists','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Pricing', 'Customer Groups'])]); ?>
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

    <div class="alert alert-info small">
        Each customer belongs to a group. Their price is the group's <strong>special price list</strong> for that product if one is set,
        otherwise the wholesale price with the group's discount (a negative discount adds a premium).
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Group</th><th class="text-end">Discount</th><th class="text-end">Customers</th><th class="text-end">Special prices</th><th></th></tr></thead>
                            <tbody>
                                <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="td-title">
                                            <form method="POST" action="<?php echo e(route('customer-groups.update', $g)); ?>" class="d-flex flex-wrap gap-2 align-items-center" id="g<?php echo e($g->id); ?>">
                                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                                <input name="name" value="<?php echo e($g->name); ?>" class="form-control form-control-sm" style="max-width: 220px">
                                                <input name="description" value="<?php echo e($g->description); ?>" class="form-control form-control-sm" style="max-width: 260px" placeholder="Description">
                                            </form>
                                        </td>
                                        <td data-label="Discount %" class="text-end"><input form="g<?php echo e($g->id); ?>" type="number" step="0.01" name="discount_percent" value="<?php echo e($g->discount_percent); ?>" class="form-control form-control-sm text-end ms-auto" style="max-width: 100px"></td>
                                        <td data-label="Customers" class="text-end"><?php echo e($g->customers_count); ?></td>
                                        <td data-label="Special prices" class="text-end"><?php echo e($g->price_list_items_count); ?></td>
                                        <td class="td-actions text-end text-nowrap">
                                            <button form="g<?php echo e($g->id); ?>" class="btn btn-sm btn-light">Save</button>
                                            <a href="<?php echo e(route('customer-groups.prices', $g)); ?>" class="btn btn-sm btn-soft-primary">Price list</a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Add group</h5></div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('customer-groups.store')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
                        <div class="mb-2"><label class="form-label">Discount % off wholesale</label><input type="number" step="0.01" name="discount_percent" value="0" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Description</label><input name="description" class="form-control"></div>
                        <button class="btn btn-primary w-100">Add group</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Customer Groups'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/masters/customer-groups/index.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Brands, Categories & Stores','crumbs' => ['Settings', 'Setup']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Brands, Categories & Stores','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Settings', 'Setup'])]); ?>
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

    <?php
        $tab = request('tab', 'brands');
        $tabs = [
            'brands' => ['Brands', $brands, ['name' => 'Name', 'country' => 'Country']],
            'categories' => ['Categories & markup', $categories, ['name' => 'Name', 'markup_percent' => 'Markup %']],
            'warehouses' => ['Stores / warehouses', $warehouses, ['code' => 'Code', 'name' => 'Name', 'address' => 'Address']],
            'expense-categories' => ['Expense categories', $expenseCategories, ['name' => 'Name']],
        ];
    ?>

    <ul class="nav nav-tabs nav-tabs-scroll mb-3">
        <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => [$label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="nav-item"><a class="nav-link <?php echo e($tab === $key ? 'active' : ''); ?>" href="?tab=<?php echo e($key); ?>"><?php echo e($label); ?></a></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>

    <?php [$label, $rows, $fields] = $tabs[$tab]; ?>
    <div class="card">
        <?php if($tab === 'categories'): ?>
            <div class="card-header small text-muted">Category markup is used for suggested selling prices (landed cost + markup). Empty = company default <?php echo e(setting('default_markup_percent')); ?>%.</div>
        <?php endif; ?>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><th><?php echo e($l); ?></th><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><th></th></tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td data-label="<?php echo e($l); ?>"><input form="r<?php echo e($row->id); ?>" name="<?php echo e($f); ?>" value="<?php echo e($row->$f); ?>" class="form-control form-control-sm"></td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <td class="td-actions text-end text-nowrap">
                                    <form method="POST" action="<?php echo e(route('setup.update', [$tab, $row->id])); ?>" id="r<?php echo e($row->id); ?>" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?><button class="btn btn-sm btn-light">Save</button></form>
                                    <form method="POST" action="<?php echo e(route('setup.destroy', [$tab, $row->id])); ?>" class="d-inline" data-confirm="Delete this item?"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-soft-danger">Delete</button></form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr class="table-light">
                            <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td data-label="<?php echo e($l); ?>"><input form="new-row" name="<?php echo e($f); ?>" class="form-control form-control-sm" placeholder="New <?php echo e(strtolower($l)); ?>"></td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <td class="td-actions text-end"><form method="POST" action="<?php echo e(route('setup.store', $tab)); ?>" id="new-row"><?php echo csrf_field(); ?><button class="btn btn-sm btn-primary">Add</button></form></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Setup'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/masters/setup.blade.php ENDPATH**/ ?>
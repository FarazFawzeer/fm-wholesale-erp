<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Customers','crumbs' => ['Sales', 'Customers']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Customers','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Sales', 'Customers'])]); ?>
        <a href="<?php echo e(route('customers.create')); ?>" class="btn btn-primary"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>New Customer</a>
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
        <div class="card-body border-bottom">
            <form class="filter-bar">
                <input type="search" name="q" value="<?php echo e(request('q')); ?>" class="form-control filter-grow" placeholder="Search name, code, city or phone">
                <select name="group" class="form-select" onchange="this.form.submit()">
                    <option value="">All groups</option>
                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($g->id); ?>" <?php if(request('group') == $g->id): echo 'selected'; endif; ?>><?php echo e($g->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button class="btn btn-light">Search</button>
            </form>
        </div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack">
                    <thead><tr><th>Customer</th><th>Group</th><th>City</th><th>Phone</th><th class="text-end">Credit limit</th><th class="text-end">Outstanding</th><th class="text-end">Overdue</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php $bal = $c->balance(); $over = $c->overdueAmount(); ?>
                            <tr>
                                <td class="td-title"><a href="<?php echo e(route('customers.show', $c)); ?>" class="doc-link"><?php echo e($c->name); ?></a><div class="small text-muted"><?php echo e($c->code); ?> <?php echo e($c->business_name && $c->business_name != $c->name ? '· ' . $c->business_name : ''); ?></div></td>
                                <td data-label="Group"><?php echo e($c->group?->name); ?></td>
                                <td data-label="City"><?php echo e($c->city); ?></td>
                                <td data-label="Phone"><?php echo e($c->phone); ?></td>
                                <td data-label="Credit limit" class="text-end money"><?php echo e(money($c->credit_limit)); ?></td>
                                <td data-label="Outstanding" class="text-end money fw-semibold <?php echo e($c->credit_limit > 0 && $bal > $c->credit_limit ? 'text-danger' : ''); ?>"><?php echo e(money($bal)); ?></td>
                                <td data-label="Overdue" class="text-end money <?php echo e($over > 0 ? 'text-danger fw-semibold' : 'text-muted'); ?>"><?php echo e(money($over)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['cols' => 7,'text' => 'No customers found.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['cols' => 7,'text' => 'No customers found.']); ?>
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
                </table>
            </div>
        </div>
        <?php if($customers->hasPages()): ?><div class="card-footer"><?php echo e($customers->links()); ?></div><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Customers'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/masters/customers/index.blade.php ENDPATH**/ ?>
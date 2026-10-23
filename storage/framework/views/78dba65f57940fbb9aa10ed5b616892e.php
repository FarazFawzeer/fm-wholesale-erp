<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Invoices','crumbs' => ['Sales', 'Invoices']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Invoices','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Sales', 'Invoices'])]); ?>
        <a href="<?php echo e(route('invoices.create')); ?>" class="btn btn-primary"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>New Invoice</a>
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

    <ul class="nav nav-pills nav-tabs-scroll mb-3 gap-1">
        <?php $__currentLoopData = ['' => 'All', 'unpaid' => 'Unpaid', 'partial' => 'Part paid', 'paid' => 'Paid', 'overdue' => 'Overdue']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="nav-item"><a class="nav-link <?php echo e(request('status', '') === $k ? 'active' : ''); ?>" href="<?php echo e(route('invoices.index', array_filter(['status' => $k]))); ?>"><?php echo e($l); ?></a></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>

    <div class="card">
        <div class="card-body border-bottom">
            <form class="filter-bar">
                <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
                <input type="search" name="q" value="<?php echo e(request('q')); ?>" class="form-control filter-grow" placeholder="Invoice no or customer">
                <select name="delivery" class="form-select"><option value="">Any delivery</option><?php $__currentLoopData = ['pending', 'partial', 'delivered']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($d); ?>" <?php if(request('delivery') == $d): echo 'selected'; endif; ?>><?php echo e(ucfirst($d)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
                <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="form-control" title="From">
                <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="form-control" title="To">
                <button class="btn btn-light">Filter</button>
            </form>
        </div>
        <div class="card-body py-2 border-bottom small d-flex flex-wrap gap-3">
            <span>Total <strong class="money"><?php echo e(money($totals->total)); ?></strong></span>
            <span>Outstanding <strong class="money text-danger"><?php echo e(money($totals->balance)); ?></strong></span>
            <span>Gross profit <strong class="money text-success"><?php echo e(money($totals->profit)); ?></strong></span>
        </div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack mb-0">
                    <thead><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Due</th><th>Payment</th><th>Delivery</th><th class="text-end">Total</th><th class="text-end">Balance</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="td-title"><a href="<?php echo e(route('invoices.show', $inv)); ?>" class="doc-link"><?php echo e($inv->number); ?></a> <?php echo status_badge($inv->sale_type); ?></td>
                                <td data-label="Customer"><?php echo e($inv->customer->name); ?></td>
                                <td data-label="Date"><?php echo e($inv->date->format('d M Y')); ?></td>
                                <td data-label="Due" class="<?php echo e($inv->daysOverdue() ? 'text-danger fw-semibold' : ''); ?>"><?php echo e($inv->due_date->format('d M Y')); ?></td>
                                <td data-label="Payment"><?php echo status_badge($inv->daysOverdue() ? 'overdue' : $inv->payment_status); ?></td>
                                <td data-label="Delivery"><?php echo status_badge($inv->delivery_status); ?></td>
                                <td data-label="Total" class="text-end money"><?php echo e(money($inv->total)); ?></td>
                                <td data-label="Balance" class="text-end money fw-semibold"><?php echo e(money($inv->balance())); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['cols' => 8,'text' => 'No invoices found.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['cols' => 8,'text' => 'No invoices found.']); ?>
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
        <?php if($invoices->hasPages()): ?><div class="card-footer"><?php echo e($invoices->links()); ?></div><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Invoices'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/sales/invoices/index.blade.php ENDPATH**/ ?>
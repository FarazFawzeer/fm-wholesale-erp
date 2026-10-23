<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Customer Statement','crumbs' => ['Reports' => route('reports.index'), 'Statement']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Customer Statement','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Reports' => route('reports.index'), 'Statement'])]); ?>
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

    <div class="card no-print">
        <div class="card-body">
            <form class="filter-bar">
                <div class="filter-grow"><label class="form-label small mb-1">Customer</label>
                    <select name="customer_id" class="form-select" data-choices required><option value="">Select customer</option><?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if($customer?->id == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                <div><label class="form-label small mb-1">From</label><input type="date" name="from" value="<?php echo e($from); ?>" class="form-control"></div>
                <div><label class="form-label small mb-1">To</label><input type="date" name="to" value="<?php echo e($to); ?>" class="form-control"></div>
                <button class="btn btn-primary">Show</button>
                <?php if($customer): ?><button type="button" class="btn btn-light" onclick="window.print()">Print</button><?php endif; ?>
            </form>
        </div>
    </div>

    <?php if($customer): ?>
        <?php
            $closing = $opening + $rows->sum('debit') - $rows->sum('credit');
            $wa = $customer->whatsappNumber();
        ?>
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between gap-2">
                <div><h5 class="mb-0"><?php echo e($customer->business_name ?: $customer->name); ?></h5><div class="small text-muted"><?php echo e($customer->code); ?> &middot; <?php echo e($customer->address); ?> <?php echo e($customer->city); ?></div></div>
                <div class="text-end"><div class="small text-muted"><?php echo e(\Carbon\Carbon::parse($from)->format('d M Y')); ?> - <?php echo e(\Carbon\Carbon::parse($to)->format('d M Y')); ?></div><div class="fs-5 fw-bold money">Balance <?php echo e(money($closing)); ?></div>
                    <?php if($wa): ?><a target="_blank" rel="noopener" class="btn btn-sm btn-success mt-1 no-print" href="https://wa.me/<?php echo e($wa); ?>?text=<?php echo e(rawurlencode('Dear ' . $customer->name . ', your account balance with ' . setting('company_name') . ' as at ' . \Carbon\Carbon::parse($to)->format('d M Y') . ' is ' . money($closing) . '. Thank you.')); ?>">Send balance on WhatsApp</a><?php endif; ?></div>
            </div>
            <div class="card-body table-wrap p-0">
                <div class="table-responsive">
                    <table class="table erp-table stack mb-0">
                        <thead><tr><th>Date</th><th>Reference</th><th>Description</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th></tr></thead>
                        <tbody>
                            <tr class="table-light"><td class="td-title" colspan="5">Opening balance</td><td data-label="Balance" class="text-end money fw-semibold"><?php echo e(money($opening)); ?></td></tr>
                            <?php $run = $opening; ?>
                            <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php $run += $r['debit'] - $r['credit']; ?>
                                <tr>
                                    <td data-label="Date"><?php echo e($r['date']->format('d M Y')); ?></td>
                                    <td class="td-title"><a href="<?php echo e($r['url']); ?>"><?php echo e($r['ref']); ?></a></td>
                                    <td data-label="Description"><?php echo e($r['desc']); ?></td>
                                    <td data-label="Debit" class="text-end money"><?php echo e($r['debit'] ? money($r['debit']) : ''); ?></td>
                                    <td data-label="Credit" class="text-end money text-success"><?php echo e($r['credit'] ? money($r['credit']) : ''); ?></td>
                                    <td data-label="Balance" class="text-end money fw-semibold"><?php echo e(money($run)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['cols' => 6,'text' => 'No transactions in this period.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['cols' => 6,'text' => 'No transactions in this period.']); ?>
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
                        <tfoot class="table-light fw-bold"><tr><td colspan="3">Closing balance</td><td class="text-end money"><?php echo e(money($rows->sum('debit'))); ?></td><td class="text-end money"><?php echo e(money($rows->sum('credit'))); ?></td><td class="text-end money"><?php echo e(money($closing)); ?></td></tr></tfoot>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Customer Statement'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/reports/statement.blade.php ENDPATH**/ ?>
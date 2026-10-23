<?php $__env->startSection('content'); ?>
    <?php $d = \Carbon\Carbon::parse($date); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Daily Cashbook','crumbs' => ['Cash & Bank', 'Cashbook']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Daily Cashbook','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Cash & Bank', 'Cashbook'])]); ?>
        <a href="<?php echo e(route('cashbook', ['date' => $d->copy()->subDay()->toDateString()])); ?>" class="btn btn-light">&larr; Prev</a>
        <form><input type="date" name="date" value="<?php echo e($date); ?>" class="form-control" onchange="this.form.submit()"></form>
        <a href="<?php echo e(route('cashbook', ['date' => $d->copy()->addDay()->toDateString()])); ?>" class="btn btn-light">Next &rarr;</a>
        <button onclick="window.print()" class="btn btn-light">Print</button>
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
        <div class="card-header"><h5 class="card-title mb-0"><?php echo e($d->format('l, d M Y')); ?> - balances</h5></div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Account</th><th class="text-end">Opening</th><th class="text-end">Money in</th><th class="text-end">Money out</th><th class="text-end">Closing</th></tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="td-title"><a href="<?php echo e(route('accounts.show', $a['account'])); ?>" class="doc-link"><?php echo e($a['account']->name); ?></a></td>
                                <td data-label="Opening" class="text-end money"><?php echo e(money($a['opening'])); ?></td>
                                <td data-label="In" class="text-end money text-success"><?php echo e(money($a['in'])); ?></td>
                                <td data-label="Out" class="text-end money text-danger"><?php echo e(money($a['out'])); ?></td>
                                <td data-label="Closing" class="text-end money fw-bold"><?php echo e(money($a['closing'])); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr><td>Total</td><td class="text-end money"><?php echo e(money($accounts->sum('opening'))); ?></td><td class="text-end money text-success"><?php echo e(money($accounts->sum('in'))); ?></td><td class="text-end money text-danger"><?php echo e(money($accounts->sum('out'))); ?></td><td class="text-end money"><?php echo e(money($accounts->sum('closing'))); ?></td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Transactions</h5></div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Description</th><th>Account</th><th>Type</th><th class="text-end">In</th><th class="text-end">Out</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="td-title"><?php echo e($r->description); ?></td>
                                <td data-label="Account"><?php echo e($r->account->name); ?></td>
                                <td data-label="Type"><?php echo e(ucwords(str_replace('_', ' ', $r->type))); ?></td>
                                <td data-label="In" class="text-end money text-success"><?php echo e($r->amount > 0 ? money($r->amount) : ''); ?></td>
                                <td data-label="Out" class="text-end money text-danger"><?php echo e($r->amount < 0 ? money(-$r->amount) : ''); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['cols' => 5,'text' => 'No money movements on this day.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['cols' => 5,'text' => 'No money movements on this day.']); ?>
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
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Daily Cashbook'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/finance/accounts/cashbook.blade.php ENDPATH**/ ?>
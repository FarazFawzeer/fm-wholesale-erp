<div class="card no-print">
    <div class="card-body">
        <form class="filter-bar">
            <div><label class="form-label small mb-1">From</label><input type="date" name="from" value="<?php echo e($from); ?>" class="form-control"></div>
            <div><label class="form-label small mb-1">To</label><input type="date" name="to" value="<?php echo e($to); ?>" class="form-control"></div>
            <?php echo e($slot ?? ''); ?>

            <button class="btn btn-light">Show</button>
            <div class="btn-group">
                <?php $__currentLoopData = ['This month' => [now()->startOfMonth(), now()], 'Last 30 days' => [now()->subDays(30), now()], 'Last month' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l => [$a, $b]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a class="btn btn-sm btn-outline-secondary" href="?<?php echo e(http_build_query(array_merge(request()->except(['from', 'to']), ['from' => $a->toDateString(), 'to' => $b->toDateString()]))); ?>"><?php echo e($l); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button type="button" class="btn btn-light ms-auto" onclick="window.print()"><iconify-icon icon="solar:printer-outline" class="align-middle"></iconify-icon></button>
        </form>
    </div>
</div>
<?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/reports/partials/range.blade.php ENDPATH**/ ?>
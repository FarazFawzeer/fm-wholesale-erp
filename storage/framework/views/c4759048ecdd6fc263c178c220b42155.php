<?php
    $s = $po->status;
    $costed = $po->charges_total > 0 || in_array($s, ['costed', 'partial', 'received']);
    $steps = [
        ['Purchase Order', 'Order shoes from overseas supplier', in_array($s, ['ordered', 'costed', 'partial', 'received']), $s === 'draft'],
        ['Import Costing', 'Supplier cost + freight + insurance + duty + other', $costed, in_array($s, ['ordered']) && ! $costed],
        ['Goods Received (GRN)', 'Verify quantities, sizes and damaged items', in_array($s, ['partial', 'received']), $s === 'costed' || $s === 'partial'],
        ['Stock & Cost Updated', 'Inventory, landed cost and profit updated', $s === 'received', false],
    ];
?>
<div class="workflow mb-3">
    <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => [$title, $text, $done, $current]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="step <?php echo e($done ? 'done' : ($current ? 'current' : '')); ?>">
            <div class="step-title">
                <?php if($done): ?><iconify-icon icon="solar:check-circle-bold" class="text-success align-middle"></iconify-icon><?php else: ?><span class="text-muted"><?php echo e($i + 1); ?>.</span><?php endif; ?>
                <?php echo e($title); ?>

            </div>
            <div class="text-muted"><?php echo e($text); ?></div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/purchasing/partials/workflow.blade.php ENDPATH**/ ?>
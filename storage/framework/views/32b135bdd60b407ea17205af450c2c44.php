<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Import Costing by Shipment','crumbs' => ['Reports' => route('reports.index'), 'Import costing']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Import Costing by Shipment','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Reports' => route('reports.index'), 'Import costing'])]); ?>
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
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead>
                        <tr><th>PO / shipment</th><th class="text-end">Pairs</th><th class="text-end">Goods (Rs)</th>
                            <?php $__currentLoopData = \App\Models\PurchaseOrder::CHARGE_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><th class="text-end"><?php echo e($l); ?></th><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <th class="text-end">Landed total</th><th class="text-end">Uplift</th><th class="text-end">Per pair</th></tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $goods = $po->supplierCostLkr(); ?>
                            <tr>
                                <td class="td-title"><a href="<?php echo e(route('purchase-orders.costing', $po)); ?>" class="doc-link"><?php echo e($po->number); ?></a> <span class="small text-muted"><?php echo e($po->supplier->name); ?> · <?php echo e($po->shipment_ref); ?></span></td>
                                <td data-label="Pairs" class="text-end"><?php echo e(qty($po->pairs)); ?></td>
                                <td data-label="Goods" class="text-end money"><?php echo e(money($goods)); ?></td>
                                <?php $__currentLoopData = \App\Models\PurchaseOrder::CHARGE_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $v = $po->charges->where('type', $k)->sum('amount'); ?>
                                    <td data-label="<?php echo e($l); ?>" class="text-end money <?php echo e($v ? '' : 'text-muted'); ?>"><?php echo e($v ? money($v) : '-'); ?></td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <td data-label="Landed" class="text-end money fw-bold"><?php echo e(money($po->landed_total)); ?></td>
                                <td data-label="Uplift" class="text-end"><span class="badge bg-warning-subtle text-warning">+<?php echo e($goods > 0 ? number_format($po->charges_total / $goods * 100, 1) : 0); ?>%</span></td>
                                <td data-label="Per pair" class="text-end money"><?php echo e(money($po->landed_total / max(1, $po->pairs))); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Import Costing'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/reports/import-costing.blade.php ENDPATH**/ ?>
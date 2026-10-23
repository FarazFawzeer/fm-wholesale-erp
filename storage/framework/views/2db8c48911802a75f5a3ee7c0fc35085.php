<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Purchase Orders & Imports','crumbs' => ['Import & Purchasing', 'Purchase Orders']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Purchase Orders & Imports','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Import & Purchasing', 'Purchase Orders'])]); ?>
        <a href="<?php echo e(route('purchase-orders.create')); ?>" class="btn btn-primary"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>New Purchase Order</a>
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
        <?php $__currentLoopData = ['' => 'All', 'draft' => 'Draft', 'ordered' => 'Ordered', 'costed' => 'Costed / in transit', 'partial' => 'Partly received', 'received' => 'Received']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="nav-item"><a class="nav-link <?php echo e(request('status', '') === $key ? 'active' : ''); ?>" href="<?php echo e(route('purchase-orders.index', array_filter(['status' => $key]))); ?>"><?php echo e($label); ?>

                <?php if($key && ($counts[$key] ?? 0)): ?><span class="badge bg-light text-dark ms-1"><?php echo e($counts[$key]); ?></span><?php endif; ?></a></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>

    <div class="card">
        <div class="card-body border-bottom">
            <form class="filter-bar">
                <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
                <input type="search" name="q" value="<?php echo e(request('q')); ?>" class="form-control filter-grow" placeholder="PO number or container / shipment ref">
                <select name="supplier_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All suppliers</option>
                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s->id); ?>" <?php if(request('supplier_id') == $s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button class="btn btn-light">Search</button>
            </form>
        </div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack mb-0">
                    <thead><tr><th>PO</th><th>Supplier</th><th>Order date</th><th>Shipment</th><th class="text-end">Pairs</th><th class="text-end">Supplier value</th><th class="text-end">Landed total</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="td-title"><a href="<?php echo e(route('purchase-orders.show', $po)); ?>" class="doc-link"><?php echo e($po->number); ?></a></td>
                                <td data-label="Supplier"><?php echo e($po->supplier->name); ?></td>
                                <td data-label="Order date"><?php echo e($po->order_date->format('d M Y')); ?></td>
                                <td data-label="Shipment"><?php echo e($po->shipment_ref ?: '-'); ?></td>
                                <td data-label="Pairs" class="text-end"><?php echo e(qty($po->received)); ?> / <?php echo e(qty($po->pairs)); ?></td>
                                <td data-label="Supplier value" class="text-end money"><?php echo e($po->currency); ?> <?php echo e(number_format($po->subtotal_fx, 2)); ?></td>
                                <td data-label="Landed" class="text-end money fw-semibold"><?php echo e(money($po->landed_total)); ?></td>
                                <td data-label="Status"><?php echo status_badge($po->status); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['cols' => 8,'text' => 'No purchase orders found.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['cols' => 8,'text' => 'No purchase orders found.']); ?>
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
        <?php if($orders->hasPages()): ?><div class="card-footer"><?php echo e($orders->links()); ?></div><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Purchase Orders'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/purchasing/orders/index.blade.php ENDPATH**/ ?>
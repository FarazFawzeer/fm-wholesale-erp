<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $product->code . ' - ' . $product->name,'crumbs' => ['Products' => route('products.index'), $product->code]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->code . ' - ' . $product->name),'crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Products' => route('products.index'), $product->code])]); ?>
        <a href="<?php echo e(route('products.edit', $product)); ?>" class="btn btn-primary"><iconify-icon icon="solar:pen-outline" class="align-middle me-1"></iconify-icon>Edit</a>
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

    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3"><?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Stock on hand','value' => qty($product->totalStock()) . ' pairs','icon' => 'solar:box-outline','sub' => number_format($product->totalStock() / max(1, $product->pairs_per_carton), 1) . ' cartons']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Stock on hand','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(qty($product->totalStock()) . ' pairs'),'icon' => 'solar:box-outline','sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($product->totalStock() / max(1, $product->pairs_per_carton), 1) . ' cartons')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $attributes = $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $component = $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?></div>
        <div class="col-6 col-xl-3"><?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Landed cost','value' => money($product->cost_price),'icon' => 'solar:globus-outline','color' => 'info','sub' => 'Weighted average per pair']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Landed cost','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($product->cost_price)),'icon' => 'solar:globus-outline','color' => 'info','sub' => 'Weighted average per pair']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $attributes = $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $component = $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?></div>
        <div class="col-6 col-xl-3"><?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Wholesale price','value' => money($product->selling_price),'icon' => 'solar:tag-price-outline','color' => 'success','sub' => 'Margin ' . number_format($product->marginPercent(), 1) . '%']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Wholesale price','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($product->selling_price)),'icon' => 'solar:tag-price-outline','color' => 'success','sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Margin ' . number_format($product->marginPercent(), 1) . '%')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $attributes = $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $component = $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?></div>
        <div class="col-6 col-xl-3"><?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Minimum price','value' => money($product->minimumPrice()),'icon' => 'solar:shield-warning-outline','color' => 'warning','sub' => 'Suggested ' . money($product->suggestedPrice())]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Minimum price','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($product->minimumPrice())),'icon' => 'solar:shield-warning-outline','color' => 'warning','sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Suggested ' . money($product->suggestedPrice()))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $attributes = $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $component = $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?></div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Stock by size</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table erp-table text-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-start">Store</th>
                                    <?php $__currentLoopData = $product->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th><?php echo e($v->size); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $row = $product->variants->map(fn ($v) => (int) $v->stocks->firstWhere('warehouse_id', $w->id)?->qty); ?>
                                    <tr>
                                        <td class="text-start fw-semibold"><?php echo e($w->name); ?></td>
                                        <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td class="<?php echo e($q <= 0 ? 'text-danger' : ''); ?>"><?php echo e($q); ?></td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <td class="fw-bold"><?php echo e($row->sum()); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php $dmg = $product->variants->map(fn ($v) => (int) $v->stocks->sum('damaged_qty')); ?>
                                <?php if($dmg->sum()): ?>
                                    <tr class="table-light">
                                        <td class="text-start text-danger">Damaged (not sellable)</td>
                                        <?php $__currentLoopData = $dmg; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td class="text-danger"><?php echo e($q ?: ''); ?></td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <td class="fw-bold text-danger"><?php echo e($dmg->sum()); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer small text-muted">SKUs: <?php echo e($product->variants->pluck('sku')->implode(', ')); ?></div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Recent stock movements</h5></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Date</th><th>Type</th><th>Size</th><th>Store</th><th class="text-end">Qty</th><th>Reference</th></tr></thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td data-label="Date"><?php echo e($m->date->format('d M Y')); ?></td>
                                        <td data-label="Type"><?php echo e(ucwords(str_replace('_', ' ', $m->type))); ?></td>
                                        <td data-label="Size"><?php echo e($m->variant->size); ?></td>
                                        <td data-label="Store"><?php echo e($m->warehouse->code); ?></td>
                                        <td data-label="Qty" class="text-end fw-semibold <?php echo e($m->qty < 0 ? 'text-danger' : 'text-success'); ?>"><?php echo e($m->qty > 0 ? '+' : ''); ?><?php echo e($m->qty); ?></td>
                                        <td data-label="Ref"><?php echo e($m->reference_no); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['cols' => 6,'text' => 'No movements yet.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['cols' => 6,'text' => 'No movements yet.']); ?>
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
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Details</h5></div>
                <div class="card-body info-list">
                    <div><div class="label">Brand</div><div class="value"><?php echo e($product->brand?->name ?? '-'); ?></div></div>
                    <div><div class="label">Category</div><div class="value"><?php echo e($product->category?->name ?? '-'); ?></div></div>
                    <div><div class="label">Colour</div><div class="value"><?php echo e($product->colour ?? '-'); ?></div></div>
                    <div><div class="label">Gender</div><div class="value"><?php echo e($product->gender ?? '-'); ?></div></div>
                    <div><div class="label">Pairs / carton</div><div class="value"><?php echo e($product->pairs_per_carton); ?></div></div>
                    <div><div class="label">Re-order level</div><div class="value"><?php echo e($product->reorder_level); ?> pairs</div></div>
                    <div><div class="label">Status</div><div class="value"><?php echo status_badge($product->is_active ? 'active' : 'inactive'); ?></div></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Price by customer group</h5></div>
                <ul class="list-group list-group-flush">
                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?php echo e($g->name); ?>

                                <?php if($product->priceListItems->firstWhere('customer_group_id', $g->id)): ?>
                                    <span class="badge bg-info-subtle text-info">price list</span>
                                <?php endif; ?>
                            </span>
                            <strong class="money"><?php echo e(money($product->priceForGroup($g))); ?></strong>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Import history</h5></div>
                <ul class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <a href="<?php echo e(route('purchase-orders.show', $row->purchase_order_id)); ?>" class="fw-semibold"><?php echo e($row->purchaseOrder->number); ?></a>
                                <span class="small text-muted"><?php echo e($row->purchaseOrder->order_date->format('d M Y')); ?></span>
                            </div>
                            <div class="small text-muted"><?php echo e($row->received); ?> / <?php echo e($row->qty); ?> pairs &middot; <?php echo e($row->purchaseOrder->currency); ?> <?php echo e(number_format($row->fx, 2)); ?> &rarr; landed <?php echo e(money($row->landed)); ?></div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item text-muted small">Not imported yet.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => $product->code], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/masters/products/show.blade.php ENDPATH**/ ?>
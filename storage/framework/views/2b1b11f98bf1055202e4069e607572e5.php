<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $po->number . ' - ' . $po->supplier->name,'crumbs' => ['Purchase Orders' => route('purchase-orders.index'), $po->number]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($po->number . ' - ' . $po->supplier->name),'crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Purchase Orders' => route('purchase-orders.index'), $po->number])]); ?>
        <?php if($po->isEditable()): ?>
            <a href="<?php echo e(route('purchase-orders.edit', $po)); ?>" class="btn btn-light">Edit</a>
        <?php endif; ?>
        <?php if($po->status === 'draft'): ?>
            <form method="POST" action="<?php echo e(route('purchase-orders.status', $po)); ?>"><?php echo csrf_field(); ?><input type="hidden" name="status" value="ordered"><button class="btn btn-soft-primary">Mark as ordered</button></form>
        <?php endif; ?>
        <a href="<?php echo e(route('purchase-orders.costing', $po)); ?>" class="btn btn-soft-info"><iconify-icon icon="solar:calculator-outline" class="align-middle me-1"></iconify-icon>Import costing</a>
        <?php if($po->canReceive()): ?>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#receiveModal"><iconify-icon icon="solar:box-outline" class="align-middle me-1"></iconify-icon>Receive goods (GRN)</button>
        <?php endif; ?>
        <?php if($po->payable_amount > 0 && $po->balance() > 0): ?>
            <a href="<?php echo e(route('supplier-payments.create', ['supplier_id' => $po->supplier_id])); ?>" class="btn btn-soft-success">Pay supplier</a>
        <?php endif; ?>
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

    <?php echo $__env->make('purchasing.partials.workflow', ['po' => $po], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Supplier value','value' => $po->currency . ' ' . number_format($po->subtotal_fx, 2),'icon' => 'solar:dollar-outline','color' => 'secondary','sub' => '@ ' . $po->exchange_rate . ' = ' . money($po->supplierCostLkr())]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Supplier value','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($po->currency . ' ' . number_format($po->subtotal_fx, 2)),'icon' => 'solar:dollar-outline','color' => 'secondary','sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('@ ' . $po->exchange_rate . ' = ' . money($po->supplierCostLkr()))]); ?>
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
        <div class="col-6 col-lg-3"><?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Import charges','value' => money($po->charges_total),'icon' => 'solar:globus-outline','color' => 'warning','sub' => $po->supplierCostLkr() > 0 ? number_format($po->charges_total / $po->supplierCostLkr() * 100, 1) . '% of goods value' : '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Import charges','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($po->charges_total)),'icon' => 'solar:globus-outline','color' => 'warning','sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($po->supplierCostLkr() > 0 ? number_format($po->charges_total / $po->supplierCostLkr() * 100, 1) . '% of goods value' : '')]); ?>
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
        <div class="col-6 col-lg-3"><?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Landed total','value' => money($po->landed_total),'icon' => 'solar:calculator-outline','color' => 'primary','sub' => 'Allocated by ' . ($po->allocation_method === 'qty' ? 'quantity' : 'value')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Landed total','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($po->landed_total)),'icon' => 'solar:calculator-outline','color' => 'primary','sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Allocated by ' . ($po->allocation_method === 'qty' ? 'quantity' : 'value'))]); ?>
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
        <div class="col-6 col-lg-3"><?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Payable / paid','value' => money($po->payable_amount),'icon' => 'solar:wallet-money-outline','color' => 'success','sub' => 'Paid ' . money($po->paid_amount) . ($po->due_date ? ' &middot; due ' . $po->due_date->format('d M') : '')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Payable / paid','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($po->payable_amount)),'icon' => 'solar:wallet-money-outline','color' => 'success','sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Paid ' . money($po->paid_amount) . ($po->due_date ? ' &middot; due ' . $po->due_date->format('d M') : ''))]); ?>
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
                <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title mb-0">Products & landed cost</h5><?php echo status_badge($po->status); ?></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Product</th><th class="text-end">Ordered</th><th class="text-end">Received</th><th class="text-end">Unit (<?php echo e($po->currency); ?>)</th><th class="text-end">Landed / pair</th><th class="text-end">Landed value</th></tr></thead>
                            <tbody>
                                <?php $__currentLoopData = $byProduct; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $first = $rows->first(); ?>
                                    <tr>
                                        <td class="td-title">
                                            <a href="<?php echo e(route('products.show', $first->product)); ?>" class="doc-link"><?php echo e($first->product->code); ?></a>
                                            <span class="small text-muted"><?php echo e($first->product->brand?->name); ?> <?php echo e($first->product->name); ?> <?php echo e($first->product->colour); ?></span>
                                            <div class="small text-muted mt-1">
                                                <?php $__currentLoopData = $rows->sortBy(fn ($r) => (float) $r->variant->size); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="badge bg-light text-dark fw-normal"><?php echo e($r->variant->size); ?>: <?php echo e($r->received_qty); ?>/<?php echo e($r->qty); ?></span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </td>
                                        <td data-label="Ordered" class="text-end"><?php echo e(qty($rows->sum('qty'))); ?></td>
                                        <td data-label="Received" class="text-end <?php echo e($rows->sum('received_qty') < $rows->sum('qty') && $rows->sum('received_qty') > 0 ? 'text-warning fw-semibold' : ''); ?>"><?php echo e(qty($rows->sum('received_qty'))); ?></td>
                                        <td data-label="Unit FX" class="text-end money"><?php echo e(number_format($first->unit_price_fx, 2)); ?></td>
                                        <td data-label="Landed / pair" class="text-end money fw-semibold"><?php echo e(money($first->landed_unit_cost)); ?></td>
                                        <td data-label="Landed value" class="text-end money"><?php echo e(money($rows->sum(fn ($r) => $r->qty * $r->landed_unit_cost))); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot class="table-light fw-semibold">
                                <tr><td>Total</td><td class="text-end"><?php echo e(qty($po->items->sum('qty'))); ?></td><td class="text-end"><?php echo e(qty($po->items->sum('received_qty'))); ?></td><td></td><td></td><td class="text-end money"><?php echo e(money($po->landed_total)); ?></td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Order info</h5></div>
                <div class="card-body info-list">
                    <div><div class="label">Supplier</div><div class="value"><a href="<?php echo e(route('suppliers.show', $po->supplier)); ?>"><?php echo e($po->supplier->name); ?></a></div></div>
                    <div><div class="label">Order date</div><div class="value"><?php echo e($po->order_date->format('d M Y')); ?></div></div>
                    <div><div class="label">Expected</div><div class="value"><?php echo e($po->expected_date?->format('d M Y') ?? '-'); ?></div></div>
                    <div><div class="label">Shipment</div><div class="value"><?php echo e($po->shipment_ref ?: '-'); ?></div></div>
                    <div><div class="label">Exchange rate</div><div class="value"><?php echo e($po->currency); ?> 1 = Rs <?php echo e($po->exchange_rate); ?></div></div>
                    <div><div class="label">Created by</div><div class="value"><?php echo e($po->user?->name ?? '-'); ?></div></div>
                </div>
                <?php if($po->notes): ?><div class="card-footer small"><?php echo e($po->notes); ?></div><?php endif; ?>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Import charges</h5></div>
                <ul class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $po->charges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item d-flex justify-content-between"><span><?php echo e(\App\Models\PurchaseOrder::CHARGE_TYPES[$c->type] ?? $c->type); ?> <span class="small text-muted"><?php echo e($c->description); ?></span></span><span class="money"><?php echo e(money($c->amount)); ?></span></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item text-muted small">No charges yet. <a href="<?php echo e(route('purchase-orders.costing', $po)); ?>">Add import costs</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Goods received notes</h5></div>
                <ul class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $po->grns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><a href="<?php echo e(route($g->status === 'draft' ? 'grns.edit' : 'grns.show', $g)); ?>" class="fw-semibold"><?php echo e($g->number); ?></a><br><span class="small text-muted"><?php echo e($g->received_date->format('d M Y')); ?> &middot; <?php echo e($g->warehouse->code); ?></span></span>
                            <?php echo status_badge($g->status); ?>

                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item text-muted small">Nothing received yet.</li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php if($allocations->count()): ?>
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Payments</h5></div>
                    <ul class="list-group list-group-flush">
                        <?php $__currentLoopData = $allocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item d-flex justify-content-between"><span><a href="<?php echo e(route('payments.show', $a->payment)); ?>"><?php echo e($a->payment->number); ?></a> <span class="small text-muted"><?php echo e($a->payment->methodSummary()); ?></span></span><span class="money"><?php echo e(money($a->amount)); ?></span></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if($po->canReceive()): ?>
        <div class="modal fade" id="receiveModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="POST" action="<?php echo e(route('grns.store', $po)); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header"><h5 class="modal-title">Receive goods</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <p class="text-muted small">A GRN will be created with all outstanding quantities. Check the cartons, then adjust received and damaged pairs per size before posting.</p>
                        <div class="mb-3"><label class="form-label">Receive into</label>
                            <select name="warehouse_id" class="form-select"><?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($w->id); ?>" <?php if($w->is_default): echo 'selected'; endif; ?>><?php echo e($w->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                        <div><label class="form-label">Received date</label><input type="date" name="received_date" value="<?php echo e(now()->format('Y-m-d')); ?>" class="form-control"></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-success">Start GRN</button></div>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => $po->number], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/purchasing/orders/show.blade.php ENDPATH**/ ?>
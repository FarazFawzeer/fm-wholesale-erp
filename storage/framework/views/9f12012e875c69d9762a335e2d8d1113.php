<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Import Costing - ' . $po->number,'crumbs' => ['Purchase Orders' => route('purchase-orders.index'), $po->number => route('purchase-orders.show', $po), 'Costing']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Import Costing - ' . $po->number),'crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Purchase Orders' => route('purchase-orders.index'), $po->number => route('purchase-orders.show', $po), 'Costing'])]); ?>
        <a href="<?php echo e(route('purchase-orders.show', $po)); ?>" class="btn btn-light">Back to PO</a>
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

    <?php
        $editable = $po->isEditable();
        $charges = old('charges', $po->charges->map(fn ($c) => $c->only('type', 'description', 'amount'))->all());
        foreach (array_keys(\App\Models\PurchaseOrder::CHARGE_TYPES) as $type) {
            if (! collect($charges)->firstWhere('type', $type)) {
                $charges[] = ['type' => $type, 'description' => '', 'amount' => ''];
            }
        }
        $goodsLkr = $po->supplierCostLkr();
        $byProduct = $po->items->groupBy('product_id');
    ?>

    <div class="row">
        <div class="col-xl-5">
            <form method="POST" action="<?php echo e(route('purchase-orders.costing.save', $po)); ?>">
                <?php echo csrf_field(); ?>
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Costs for this shipment</h5></div>
                    <div class="card-body">
                        <?php if (! ($editable)): ?><div class="alert alert-warning small">Goods have been received, so costing is locked.</div><?php endif; ?>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><label class="form-label">Exchange rate (<?php echo e($po->currency); ?> &rarr; Rs)</label><input type="number" step="0.0001" name="exchange_rate" value="<?php echo e(old('exchange_rate', $po->exchange_rate)); ?>" class="form-control" <?php if(! $editable): echo 'disabled'; endif; ?>></div>
                            <div class="col-6"><label class="form-label">Share charges by</label>
                                <select name="allocation_method" class="form-select" <?php if(! $editable): echo 'disabled'; endif; ?>>
                                    <option value="value" <?php if($po->allocation_method === 'value'): echo 'selected'; endif; ?>>Value (price)</option>
                                    <option value="qty" <?php if($po->allocation_method === 'qty'): echo 'selected'; endif; ?>>Quantity (pairs)</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mb-1"><span>Supplier invoice</span><span><?php echo e($po->currency); ?> <?php echo e(number_format($po->subtotal_fx, 2)); ?> = <strong><?php echo e(money($goodsLkr)); ?></strong></span></div>
                        <hr>
                        <?php $__currentLoopData = array_values($charges); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="row g-2 align-items-center mb-2">
                                <div class="col-5 small fw-semibold"><?php echo e(\App\Models\PurchaseOrder::CHARGE_TYPES[$c['type']] ?? $c['type']); ?><input type="hidden" name="charges[<?php echo e($i); ?>][type]" value="<?php echo e($c['type']); ?>"></div>
                                <div class="col-7"><div class="input-group input-group-sm"><span class="input-group-text">Rs</span><input type="number" step="0.01" min="0" name="charges[<?php echo e($i); ?>][amount]" value="<?php echo e($c['amount']); ?>" class="form-control text-end" <?php if(! $editable): echo 'disabled'; endif; ?>></div></div>
                                <div class="col-12"><input name="charges[<?php echo e($i); ?>][description]" value="<?php echo e($c['description']); ?>" class="form-control form-control-sm" placeholder="Note (agent, CUSDEC no...)" <?php if(! $editable): echo 'disabled'; endif; ?>></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <div class="totals-box border-top mt-3 pt-2">
                            <div class="row-total"><span>Goods (Rs)</span><span class="money"><?php echo e(money($goodsLkr)); ?></span></div>
                            <div class="row-total"><span>Import charges</span><span class="money"><?php echo e(money($po->charges_total)); ?></span></div>
                            <div class="row-total grand"><span>Landed total</span><span class="money"><?php echo e(money($po->landed_total)); ?></span></div>
                        </div>
                    </div>
                    <?php if($editable): ?>
                        <div class="card-footer text-end sticky-actions"><button class="btn btn-primary">Save &amp; recalculate</button></div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <div class="col-xl-7">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Landed cost per pair</h5></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Product</th><th class="text-end">Pairs</th><th class="text-end">Supplier price</th><th class="text-end">+ Charges</th><th class="text-end">Landed / pair</th><th class="text-end">Suggested price</th></tr></thead>
                            <tbody>
                                <?php $__currentLoopData = $byProduct; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $f = $rows->first(); $base = $f->unit_price_fx * $po->exchange_rate; ?>
                                    <tr>
                                        <td class="td-title"><strong><?php echo e($f->product->code); ?></strong> <span class="small text-muted"><?php echo e($f->product->name); ?> <?php echo e($f->product->colour); ?></span></td>
                                        <td data-label="Pairs" class="text-end"><?php echo e(qty($rows->sum('qty'))); ?></td>
                                        <td data-label="Supplier price" class="text-end money"><?php echo e(money($base)); ?><div class="small text-muted"><?php echo e($po->currency); ?> <?php echo e(number_format($f->unit_price_fx, 2)); ?></div></td>
                                        <td data-label="+ Charges" class="text-end money text-warning"><?php echo e(money($f->landed_unit_cost - $base)); ?></td>
                                        <td data-label="Landed / pair" class="text-end money fw-bold"><?php echo e(money($f->landed_unit_cost)); ?></td>
                                        <td data-label="Suggested" class="text-end money text-success"><?php echo e(money(round($f->landed_unit_cost * (1 + $f->product->effectiveMarkup() / 100), -1))); ?><div class="small text-muted">+<?php echo e($f->product->effectiveMarkup()); ?>%</div></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer small text-muted">
                    When the GRN is posted, these landed costs are blended into each product's average cost and used for profit on every sale.
                    Update selling prices afterwards from <a href="<?php echo e(route('products.bulk-price')); ?>">Bulk Price Update</a>.
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Import Costing ' . $po->number], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/purchasing/orders/costing.blade.php ENDPATH**/ ?>
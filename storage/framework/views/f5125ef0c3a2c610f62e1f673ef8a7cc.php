<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $doc->label() . ' ' . $doc->number,'crumbs' => [$doc->label() . 's' => route($doc->type === 'quotation' ? 'quotations.index' : 'sales-orders.index'), $doc->number]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($doc->label() . ' ' . $doc->number),'crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([$doc->label() . 's' => route($doc->type === 'quotation' ? 'quotations.index' : 'sales-orders.index'), $doc->number])]); ?>
        <button onclick="window.print()" class="btn btn-light"><iconify-icon icon="solar:printer-outline" class="align-middle me-1"></iconify-icon>Print</button>
        <?php if($doc->status === 'open'): ?>
            <a href="<?php echo e(route('sales-docs.edit', $doc)); ?>" class="btn btn-light">Edit</a>
        <?php endif; ?>
        <?php if($doc->type === 'quotation' && in_array($doc->status, ['open', 'accepted'])): ?>
            <form method="POST" action="<?php echo e(route('sales-docs.convert', $doc)); ?>"><?php echo csrf_field(); ?><button class="btn btn-soft-primary">Convert to sales order</button></form>
        <?php endif; ?>
        <?php if(in_array($doc->status, ['open', 'accepted'])): ?>
            <form method="POST" action="<?php echo e(route('sales-docs.convert', $doc)); ?>"><?php echo csrf_field(); ?><input type="hidden" name="to" value="invoice"><button class="btn btn-primary">Create invoice</button></form>
        <?php endif; ?>
        <?php if($doc->type === 'quotation' && $doc->status === 'open'): ?>
            <form method="POST" action="<?php echo e(route('sales-docs.status', $doc)); ?>"><?php echo csrf_field(); ?><input type="hidden" name="status" value="rejected"><button class="btn btn-soft-danger">Mark rejected</button></form>
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

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0">Items</h5><?php echo status_badge($doc->status); ?></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Product / sizes</th><th class="text-end">Pairs</th><th class="text-end">Price</th><th class="text-end">Disc</th><th class="text-end">Amount</th></tr></thead>
                            <tbody>
                                <?php $__currentLoopData = $byProduct; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $f = $rows->first(); ?>
                                    <tr>
                                        <td class="td-title"><strong><?php echo e($f->product->code); ?></strong> <span class="small text-muted"><?php echo e($f->product->name); ?> <?php echo e($f->product->colour); ?></span>
                                            <div class="mt-1"><?php $__currentLoopData = $rows->sortBy(fn ($r) => (float) $r->variant->size); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="badge bg-light text-dark fw-normal me-1"><?php echo e($r->variant->size); ?> &times; <?php echo e($r->qty); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></td>
                                        <td data-label="Pairs" class="text-end"><?php echo e($rows->sum('qty')); ?></td>
                                        <td data-label="Price" class="text-end money"><?php echo e(money($f->unit_price)); ?></td>
                                        <td data-label="Disc" class="text-end"><?php echo e($f->discount_percent > 0 ? $f->discount_percent . '%' : '-'); ?></td>
                                        <td data-label="Amount" class="text-end money fw-semibold"><?php echo e(money($rows->sum('line_total'))); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot class="table-light">
                                <?php if($doc->discount_amount > 0): ?><tr><td colspan="4" class="text-end">Discount</td><td class="text-end money text-danger">-<?php echo e(money($doc->discount_amount)); ?></td></tr><?php endif; ?>
                                <tr class="fw-bold"><td colspan="4" class="text-end">Total</td><td class="text-end money"><?php echo e(money($doc->total)); ?></td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body info-list">
                    <div><div class="label">Customer</div><div class="value"><a href="<?php echo e(route('customers.show', $doc->customer)); ?>"><?php echo e($doc->customer->name); ?></a></div></div>
                    <div><div class="label">Date</div><div class="value"><?php echo e($doc->date->format('d M Y')); ?></div></div>
                    <?php if($doc->valid_until): ?><div><div class="label">Valid until</div><div class="value"><?php echo e($doc->valid_until->format('d M Y')); ?></div></div><?php endif; ?>
                    <div><div class="label">Created by</div><div class="value"><?php echo e($doc->user?->name); ?></div></div>
                    <?php if($doc->convertedFrom): ?><div><div class="label">From quotation</div><div class="value"><a href="<?php echo e(route('sales-docs.show', $doc->convertedFrom)); ?>"><?php echo e($doc->convertedFrom->number); ?></a></div></div><?php endif; ?>
                    <?php $__currentLoopData = $doc->invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><div class="label">Invoice</div><div class="value"><a href="<?php echo e(route('invoices.show', $inv)); ?>"><?php echo e($inv->number); ?></a></div></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php if($doc->notes): ?><div class="card-footer small"><?php echo e($doc->notes); ?></div><?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => $doc->number], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/sales/orders/show.blade.php ENDPATH**/ ?>
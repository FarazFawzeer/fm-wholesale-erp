<?php $__env->startSection('content'); ?>
    <?php $c = $invoice->customer; $wa = $c->whatsappNumber(); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Invoice ' . $invoice->number,'crumbs' => ['Invoices' => route('invoices.index'), $invoice->number]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Invoice ' . $invoice->number),'crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Invoices' => route('invoices.index'), $invoice->number])]); ?>
        <a href="<?php echo e(route('invoices.print', $invoice)); ?>" class="btn btn-light" target="_blank"><iconify-icon icon="solar:printer-outline" class="align-middle me-1"></iconify-icon>Print</a>
        <?php if($invoice->delivery_status !== 'delivered'): ?>
            <a href="<?php echo e(route('deliveries.create', $invoice)); ?>" class="btn btn-soft-primary"><iconify-icon icon="solar:delivery-outline" class="align-middle me-1"></iconify-icon>Deliver</a>
        <?php endif; ?>
        <?php if($invoice->balance() > 0): ?>
            <a href="<?php echo e(route('receipts.create', ['customer_id' => $c->id, 'invoice_id' => $invoice->id])); ?>" class="btn btn-success">Receive payment</a>
        <?php endif; ?>
        <a href="<?php echo e(route('credit-notes.create', $invoice)); ?>" class="btn btn-soft-danger">Return / credit note</a>
        <?php if($wa): ?>
            <a target="_blank" rel="noopener" class="btn btn-soft-success" href="https://wa.me/<?php echo e($wa); ?>?text=<?php echo e(rawurlencode('Invoice ' . $invoice->number . ' from ' . setting('company_name') . ': ' . money($invoice->total) . ', due ' . $invoice->due_date->format('d M Y') . '. Balance ' . money($invoice->balance()) . '.')); ?>"><iconify-icon icon="ic:baseline-whatsapp" class="align-middle"></iconify-icon></a>
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

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3"><?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Invoice total','value' => money($invoice->total),'icon' => 'solar:bill-list-outline','sub' => $invoice->discount_amount > 0 ? 'Discount ' . money($invoice->discount_amount) : '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Invoice total','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($invoice->total)),'icon' => 'solar:bill-list-outline','sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoice->discount_amount > 0 ? 'Discount ' . money($invoice->discount_amount) : '')]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Balance due','value' => money($invoice->balance()),'icon' => 'solar:wallet-money-outline','color' => $invoice->balance() > 0 ? 'danger' : 'success','sub' => 'Due ' . $invoice->due_date->format('d M Y') . ($invoice->daysOverdue() ? ' &middot; <span class=\'text-danger\'>' . $invoice->daysOverdue() . ' days overdue</span>' : '')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Balance due','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($invoice->balance())),'icon' => 'solar:wallet-money-outline','color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoice->balance() > 0 ? 'danger' : 'success'),'sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Due ' . $invoice->due_date->format('d M Y') . ($invoice->daysOverdue() ? ' &middot; <span class=\'text-danger\'>' . $invoice->daysOverdue() . ' days overdue</span>' : ''))]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Gross profit','value' => money($invoice->profit()),'icon' => 'solar:graph-up-outline','color' => 'success','sub' => ($invoice->total > 0 ? number_format($invoice->profit() / $invoice->total * 100, 1) : 0) . '% margin']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Gross profit','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($invoice->profit())),'icon' => 'solar:graph-up-outline','color' => 'success','sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($invoice->total > 0 ? number_format($invoice->profit() / $invoice->total * 100, 1) : 0) . '% margin')]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Delivered','value' => qty($invoice->lines->sum('delivered_qty')) . ' / ' . qty($invoice->lines->sum('qty')),'icon' => 'solar:delivery-outline','color' => 'info','sub' => 'pairs &middot; ' . ucfirst($invoice->delivery_status)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Delivered','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(qty($invoice->lines->sum('delivered_qty')) . ' / ' . qty($invoice->lines->sum('qty'))),'icon' => 'solar:delivery-outline','color' => 'info','sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('pairs &middot; ' . ucfirst($invoice->delivery_status))]); ?>
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
                <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Items</h5>
                    <span><?php echo status_badge($invoice->sale_type); ?> <?php echo status_badge($invoice->daysOverdue() ? 'overdue' : $invoice->payment_status); ?> <?php echo status_badge($invoice->delivery_status); ?></span>
                </div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Product / sizes</th><th class="text-end">Pairs</th><th class="text-end">Price</th><th class="text-end">Disc</th><th class="text-end">Amount</th><th class="text-end">Profit</th></tr></thead>
                            <tbody>
                                <?php $__currentLoopData = $byProduct; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $f = $rows->first(); $amount = $rows->sum('line_total'); $cost = $rows->sum(fn ($r) => $r->qty * $r->unit_cost); ?>
                                    <tr>
                                        <td class="td-title">
                                            <strong><?php echo e($f->product->code); ?></strong> <span class="small text-muted"><?php echo e($f->product->name); ?> <?php echo e($f->product->colour); ?></span>
                                            <div class="mt-1"><?php $__currentLoopData = $rows->sortBy(fn ($r) => (float) $r->variant->size); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="badge bg-light text-dark fw-normal me-1"><?php echo e($r->variant->size); ?> &times; <?php echo e($r->qty); ?><?php if($r->delivered_qty < $r->qty): ?> <span class="text-warning">(<?php echo e($r->delivered_qty); ?> sent)</span><?php endif; ?> <?php if($r->returned_qty): ?><span class="text-danger">-<?php echo e($r->returned_qty); ?></span><?php endif; ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
                                        </td>
                                        <td data-label="Pairs" class="text-end"><?php echo e($rows->sum('qty')); ?></td>
                                        <td data-label="Price" class="text-end money"><?php echo e(money($f->unit_price)); ?></td>
                                        <td data-label="Discount" class="text-end"><?php echo e($f->discount_percent > 0 ? $f->discount_percent . '%' : '-'); ?></td>
                                        <td data-label="Amount" class="text-end money fw-semibold"><?php echo e(money($amount)); ?></td>
                                        <td data-label="Profit" class="text-end money text-success"><?php echo e(money($amount - $cost)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr><td colspan="4" class="text-end">Gross</td><td class="text-end money"><?php echo e(money($invoice->subtotal)); ?></td><td></td></tr>
                                <?php if($invoice->discount_amount > 0): ?><tr><td colspan="4" class="text-end">Discount</td><td class="text-end money text-danger">-<?php echo e(money($invoice->discount_amount)); ?></td><td></td></tr><?php endif; ?>
                                <tr class="fw-bold"><td colspan="4" class="text-end">Total</td><td class="text-end money"><?php echo e(money($invoice->total)); ?></td><td class="text-end money text-success"><?php echo e(money($invoice->profit())); ?></td></tr>
                                <?php if($invoice->credited_amount > 0): ?><tr><td colspan="4" class="text-end">Credit notes</td><td class="text-end money text-danger">-<?php echo e(money($invoice->credited_amount)); ?></td><td></td></tr><?php endif; ?>
                                <tr><td colspan="4" class="text-end">Paid</td><td class="text-end money text-success">-<?php echo e(money($invoice->paid_amount)); ?></td><td></td></tr>
                                <tr class="fw-bold"><td colspan="4" class="text-end">Balance</td><td class="text-end money <?php echo e($invoice->balance() > 0 ? 'text-danger' : ''); ?>"><?php echo e(money($invoice->balance())); ?></td><td></td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body info-list">
                    <div><div class="label">Customer</div><div class="value"><a href="<?php echo e(route('customers.show', $c)); ?>"><?php echo e($c->name); ?></a><div class="small text-muted"><?php echo e($c->group?->name); ?></div></div></div>
                    <div><div class="label">Date</div><div class="value"><?php echo e($invoice->date->format('d M Y')); ?></div></div>
                    <div><div class="label">Store</div><div class="value"><?php echo e($invoice->warehouse->name); ?></div></div>
                    <div><div class="label">Sales person</div><div class="value"><?php echo e($invoice->user?->name ?? '-'); ?></div></div>
                    <?php if($invoice->salesOrder): ?><div><div class="label">From</div><div class="value"><a href="<?php echo e(route('sales-docs.show', $invoice->salesOrder)); ?>"><?php echo e($invoice->salesOrder->number); ?></a></div></div><?php endif; ?>
                </div>
                <?php if($invoice->notes): ?><div class="card-footer small"><?php echo e($invoice->notes); ?></div><?php endif; ?>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Payments</h5></div>
                <ul class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $invoice->allocations->where('amount', '>', 0); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><a href="<?php echo e(route('payments.show', $a->payment)); ?>"><?php echo e($a->payment->number); ?></a><br><span class="small text-muted"><?php echo e($a->payment->date->format('d M Y')); ?> &middot; <?php echo e($a->payment->methodSummary()); ?></span></span>
                            <strong class="money"><?php echo e(money($a->amount)); ?></strong>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item small text-muted">No payments yet.</li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Deliveries & returns</h5></div>
                <ul class="list-group list-group-flush">
                    <?php $__currentLoopData = $invoice->deliveryNotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item d-flex justify-content-between"><a href="<?php echo e(route('deliveries.show', $d)); ?>"><?php echo e($d->number); ?></a><span class="small text-muted"><?php echo e($d->date->format('d M Y')); ?></span></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $invoice->creditNotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item d-flex justify-content-between"><a href="<?php echo e(route('credit-notes.show', $cn)); ?>" class="text-danger"><?php echo e($cn->number); ?></a><span class="money text-danger">-<?php echo e(money($cn->total)); ?></span></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($invoice->deliveryNotes->isEmpty() && $invoice->creditNotes->isEmpty()): ?>
                        <li class="list-group-item small text-muted">Not delivered yet.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => $invoice->number], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/sales/invoices/show.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Good ' . (now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')) . ', ' . explode(' ', auth()->user()->name)[0]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Good ' . (now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')) . ', ' . explode(' ', auth()->user()->name)[0])]); ?>
        <span class="text-muted small align-self-center"><?php echo e(now()->format('l, d M Y')); ?></span>
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

    <div class="row g-2 mb-3 quick-actions d-md-none">
        <?php $__currentLoopData = [['New Invoice', 'invoices.create', 'solar:bill-list-outline', 'primary'], ['Receive', 'receipts.create', 'solar:wallet-money-outline', 'success'], ['New PO', 'purchase-orders.create', 'solar:globus-outline', 'info'], ['Stock', 'stock.index', 'solar:box-outline', 'secondary']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$l, $r, $i, $c]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-3"><a href="<?php echo e(route($r)); ?>" class="btn btn-soft-<?php echo e($c); ?> w-100"><iconify-icon icon="<?php echo e($i); ?>"></iconify-icon><?php echo e($l); ?></a></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3"><?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Sales today','value' => money($todaySales),'icon' => 'solar:cart-large-2-outline','color' => 'primary','href' => route('invoices.index', ['from' => now()->toDateString()]),'sub' => 'Month: ' . money($month->total)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Sales today','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($todaySales)),'icon' => 'solar:cart-large-2-outline','color' => 'primary','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('invoices.index', ['from' => now()->toDateString()])),'sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Month: ' . money($month->total))]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Gross profit (month)','value' => money($month->total - $month->cost),'icon' => 'solar:graph-up-outline','color' => 'success','href' => route('reports.invoice-profit'),'sub' => ($month->total > 0 ? number_format(($month->total - $month->cost) / $month->total * 100, 1) : 0) . '% margin &middot; expenses ' . money($monthExpenses)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Gross profit (month)','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($month->total - $month->cost)),'icon' => 'solar:graph-up-outline','color' => 'success','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.invoice-profit')),'sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($month->total > 0 ? number_format(($month->total - $month->cost) / $month->total * 100, 1) : 0) . '% margin &middot; expenses ' . money($monthExpenses))]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Receivable','value' => money($receivable),'icon' => 'solar:hand-money-outline','color' => 'warning','href' => route('reports.outstanding'),'sub' => '<span class=\'text-danger fw-semibold\'>' . money($overdue->sum(fn ($i) => $i->balance())) . ' overdue</span>']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Receivable','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($receivable)),'icon' => 'solar:hand-money-outline','color' => 'warning','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.outstanding')),'sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('<span class=\'text-danger fw-semibold\'>' . money($overdue->sum(fn ($i) => $i->balance())) . ' overdue</span>')]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Stock value','value' => money($stockValue),'icon' => 'solar:box-outline','color' => 'info','href' => route('reports.stock-valuation'),'sub' => qty($stockPairs) . ' pairs at landed cost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Stock value','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($stockValue)),'icon' => 'solar:box-outline','color' => 'info','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.stock-valuation')),'sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(qty($stockPairs) . ' pairs at landed cost')]); ?>
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
                <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title mb-0">Sales & gross profit - last 30 days</h5><a href="<?php echo e(route('reports.index')); ?>" class="small">Reports</a></div>
                <div class="card-body"><div id="sales-chart" data-chart='<?php echo json_encode($chart, 15, 512) ?>' style="min-height: 280px"></div></div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center"><h5 class="card-title mb-0">Cash & bank</h5><a href="<?php echo e(route('cashbook')); ?>" class="small">Cashbook</a></div>
                <ul class="list-group list-group-flush">
                    <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><iconify-icon icon="<?php echo e($a['type'] === 'cash' ? 'solar:wallet-outline' : ($a['type'] === 'card' ? 'solar:card-outline' : 'solar:buildings-outline')); ?>" class="align-middle me-1 text-muted"></iconify-icon><?php echo e($a['name']); ?></span>
                            <strong class="money"><?php echo e(money($a['balance'])); ?></strong>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <li class="list-group-item d-flex justify-content-between bg-light"><span class="fw-semibold">Total</span><strong class="money"><?php echo e(money($accounts->sum('balance'))); ?></strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Owed to suppliers</span><strong class="money text-danger"><?php echo e(money($payable)); ?></strong></li>
                </ul>
            </div>
            <?php if($alerts->count()): ?>
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Needs attention</h5></div>
                    <div class="list-group list-group-flush">
                        <?php $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e($a['url']); ?>" class="list-group-item list-group-item-action d-flex gap-2 align-items-center">
                                <iconify-icon icon="<?php echo e($a['icon']); ?>" class="fs-20 text-<?php echo e($a['color']); ?>"></iconify-icon>
                                <span><span class="fw-semibold"><?php echo e($a['title']); ?></span><br><span class="small text-muted"><?php echo e($a['text']); ?></span></span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-xl-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0">Overdue customers</h5><a href="<?php echo e(route('credit-control.index')); ?>" class="small">Credit control</a></div>
                <ul class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $overdueCustomers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><a href="<?php echo e(route('customers.show', $row['customer'])); ?>" class="fw-semibold"><?php echo e($row['customer']->name); ?></a><br><span class="small text-danger"><?php echo e($row['days']); ?> days overdue</span></span>
                            <strong class="money text-danger"><?php echo e(money($row['amount'])); ?></strong>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item text-muted small">No overdue invoices.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="col-lg-6 col-xl-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0">Cheques due (14 days)</h5><a href="<?php echo e(route('cheques.index')); ?>" class="small">All cheques</a></div>
                <ul class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $chequesDue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <span class="badge <?php echo e($ch->direction === 'received' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'); ?>"><?php echo e($ch->direction === 'received' ? 'IN' : 'OUT'); ?></span>
                                #<?php echo e($ch->cheque_no); ?> <span class="small text-muted"><?php echo e($ch->party()?->name); ?></span><br>
                                <span class="small <?php echo e($ch->cheque_date->isPast() ? 'text-danger' : 'text-muted'); ?>"><?php echo e($ch->cheque_date->format('d M Y')); ?> &middot; <?php echo e($ch->bank); ?></span>
                            </span>
                            <strong class="money"><?php echo e(money($ch->amount)); ?></strong>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item text-muted small">No cheques due.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="col-lg-6 col-xl-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0">Shipments in progress</h5><a href="<?php echo e(route('purchase-orders.index')); ?>" class="small">Imports</a></div>
                <ul class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $inTransit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><a href="<?php echo e(route('purchase-orders.show', $po)); ?>" class="fw-semibold"><?php echo e($po->number); ?></a> <?php echo status_badge($po->status); ?><br><span class="small text-muted"><?php echo e($po->supplier->name); ?> &middot; ETA <?php echo e($po->expected_date?->format('d M')); ?></span></span>
                            <strong class="money"><?php echo e(money($po->landed_total)); ?></strong>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item text-muted small">No open shipments.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0">Recent invoices</h5><a href="<?php echo e(route('invoices.index')); ?>" class="small">All invoices</a></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Payment</th><th>Delivery</th><th class="text-end">Total</th></tr></thead>
                            <tbody>
                                <?php $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="td-title"><a href="<?php echo e(route('invoices.show', $inv)); ?>" class="doc-link"><?php echo e($inv->number); ?></a></td>
                                        <td data-label="Customer"><?php echo e($inv->customer->name); ?></td>
                                        <td data-label="Date"><?php echo e($inv->date->format('d M')); ?></td>
                                        <td data-label="Payment"><?php echo status_badge($inv->payment_status); ?></td>
                                        <td data-label="Delivery"><?php echo status_badge($inv->delivery_status); ?></td>
                                        <td data-label="Total" class="text-end money fw-semibold"><?php echo e(money($inv->total)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Top products (30 days)</h5></div>
                <ul class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><a href="<?php echo e(route('products.show', $tp->id)); ?>" class="fw-semibold"><?php echo e($tp->code); ?></a> <span class="small text-muted"><?php echo e($tp->name); ?> <?php echo e($tp->colour); ?></span><br><span class="small text-muted"><?php echo e(qty($tp->pairs)); ?> pairs</span></span>
                            <strong class="money"><?php echo e(money($tp->sales)); ?></strong>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item text-muted small">No sales yet.</li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between"><h5 class="card-title mb-0">Low stock</h5><a href="<?php echo e(route('reports.low-stock')); ?>" class="small">View all</a></div>
                <ul class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $lowStock; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item d-flex justify-content-between"><span><a href="<?php echo e(route('products.show', $p)); ?>"><?php echo e($p->code); ?></a> <span class="small text-muted"><?php echo e($p->name); ?> <?php echo e($p->colour); ?></span></span><span class="text-danger fw-semibold"><?php echo e(qty($p->on_hand)); ?> / <?php echo e($p->reorder_level); ?></span></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item text-muted small">Stock levels are healthy.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/pages/erp-dashboard.js']); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Dashboard'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/dashboard.blade.php ENDPATH**/ ?>
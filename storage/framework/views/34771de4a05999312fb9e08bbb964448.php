<?php $__env->startSection('content'); ?>
    <?php
        $used = $customer->credit_limit > 0 ? min(100, $balance / $customer->credit_limit * 100) : 0;
        $wa = $customer->whatsappNumber();
    ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $customer->name,'crumbs' => ['Customers' => route('customers.index'), $customer->code]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($customer->name),'crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Customers' => route('customers.index'), $customer->code])]); ?>
        <a href="<?php echo e(route('invoices.create', ['customer_id' => $customer->id])); ?>" class="btn btn-primary">New Invoice</a>
        <a href="<?php echo e(route('receipts.create', ['customer_id' => $customer->id])); ?>" class="btn btn-soft-success">Receive Payment</a>
        <a href="<?php echo e(route('reports.statement', ['customer_id' => $customer->id])); ?>" class="btn btn-soft-secondary">Statement</a>
        <a href="<?php echo e(route('customers.edit', $customer)); ?>" class="btn btn-light">Edit</a>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Outstanding','value' => money($balance),'icon' => 'solar:wallet-money-outline','color' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Outstanding','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($balance)),'icon' => 'solar:wallet-money-outline','color' => 'primary']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Overdue','value' => money($overdue),'icon' => 'solar:alarm-outline','color' => $overdue > 0 ? 'danger' : 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Overdue','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($overdue)),'icon' => 'solar:alarm-outline','color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overdue > 0 ? 'danger' : 'success')]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Pending cheques','value' => money($pendingCheques),'icon' => 'solar:card-recive-outline','color' => 'info','sub' => 'Not yet cleared']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Pending cheques','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($pendingCheques)),'icon' => 'solar:card-recive-outline','color' => 'info','sub' => 'Not yet cleared']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Available credit','value' => money(max(0, $customer->credit_limit - $balance)),'icon' => 'solar:shield-check-outline','color' => $used >= 90 ? 'danger' : 'success','sub' => 'Limit ' . money($customer->credit_limit)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Available credit','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money(max(0, $customer->credit_limit - $balance))),'icon' => 'solar:shield-check-outline','color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($used >= 90 ? 'danger' : 'success'),'sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Limit ' . money($customer->credit_limit))]); ?>
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

    <?php if($customer->credit_limit > 0): ?>
        <div class="card"><div class="card-body py-3">
            <div class="d-flex justify-content-between small mb-1"><span>Credit used</span><span class="fw-semibold"><?php echo e(number_format($used, 0)); ?>%</span></div>
            <div class="progress" style="height: 8px"><div class="progress-bar bg-<?php echo e($used >= 90 ? 'danger' : ($used >= 70 ? 'warning' : 'success')); ?>" style="width: <?php echo e($used); ?>%"></div></div>
        </div></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Invoices</h5></div>
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>Invoice</th><th>Date</th><th>Due</th><th>Status</th><th class="text-end">Total</th><th class="text-end">Balance</th></tr></thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="td-title"><a href="<?php echo e(route('invoices.show', $inv)); ?>" class="doc-link"><?php echo e($inv->number); ?></a> <?php echo status_badge($inv->sale_type); ?></td>
                                        <td data-label="Date"><?php echo e($inv->date->format('d M Y')); ?></td>
                                        <td data-label="Due" class="<?php echo e($inv->daysOverdue() ? 'text-danger fw-semibold' : ''); ?>"><?php echo e($inv->due_date->format('d M Y')); ?><?php if($inv->daysOverdue()): ?><div class="small"><?php echo e($inv->daysOverdue()); ?> days overdue</div><?php endif; ?></td>
                                        <td data-label="Status"><?php echo status_badge($inv->payment_status); ?></td>
                                        <td data-label="Total" class="text-end money"><?php echo e(money($inv->total)); ?></td>
                                        <td data-label="Balance" class="text-end money fw-semibold"><?php echo e(money($inv->balance())); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['cols' => 6,'text' => 'No invoices yet.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['cols' => 6,'text' => 'No invoices yet.']); ?>
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Contact</h5>
                    <?php if($wa): ?>
                        <a target="_blank" rel="noopener" class="btn btn-sm btn-success" href="https://wa.me/<?php echo e($wa); ?>?text=<?php echo e(rawurlencode('Dear ' . $customer->name . ', your outstanding balance with ' . setting('company_name') . ' is ' . money($balance) . ($overdue > 0 ? ' (overdue ' . money($overdue) . ')' : '') . '. Thank you.')); ?>">
                            <iconify-icon icon="ic:baseline-whatsapp" class="align-middle"></iconify-icon> Send reminder</a>
                    <?php endif; ?>
                </div>
                <div class="card-body info-list">
                    <div><div class="label">Group</div><div class="value"><?php echo e($customer->group?->name ?? '-'); ?></div></div>
                    <div><div class="label">Terms</div><div class="value"><?php echo e($customer->payment_terms_days); ?> days</div></div>
                    <div><div class="label">Phone</div><div class="value"><?php echo e($customer->phone ?: '-'); ?></div></div>
                    <div><div class="label">City</div><div class="value"><?php echo e($customer->city ?: '-'); ?></div></div>
                    <div><div class="label">Address</div><div class="value"><?php echo e($customer->address ?: '-'); ?></div></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Cheques</h5></div>
                <ul class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $cheques; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>#<?php echo e($ch->cheque_no); ?> <span class="small text-muted"><?php echo e($ch->bank); ?></span><br><span class="small text-muted"><?php echo e($ch->cheque_date->format('d M Y')); ?></span></span>
                            <span class="text-end"><strong class="money"><?php echo e(money($ch->amount)); ?></strong><br><?php echo status_badge($ch->status); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item text-muted small">No cheques.</li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Recent receipts</h5></div>
                <ul class="list-group list-group-flush">
                    <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><a href="<?php echo e(route('payments.show', $p)); ?>"><?php echo e($p->number); ?></a><br><span class="small text-muted"><?php echo e($p->date->format('d M Y')); ?> &middot; <?php echo e($p->methodSummary()); ?></span></span>
                            <strong class="money"><?php echo e(money($p->total)); ?></strong>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="list-group-item text-muted small">No receipts yet.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => $customer->name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/masters/customers/show.blade.php ENDPATH**/ ?>
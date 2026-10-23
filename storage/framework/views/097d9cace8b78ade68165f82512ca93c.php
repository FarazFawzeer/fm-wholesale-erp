<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Credit Control & Reminders','crumbs' => ['Credit Control', 'Overdue']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Credit Control & Reminders','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Credit Control', 'Overdue'])]); ?>
        <a href="<?php echo e(route('reports.outstanding')); ?>" class="btn btn-light">Ageing report</a>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Overdue','value' => money($overdue->sum(fn ($i) => $i->balance())),'icon' => 'solar:alarm-outline','color' => 'danger','sub' => $overdue->count() . ' invoices']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Overdue','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($overdue->sum(fn ($i) => $i->balance()))),'icon' => 'solar:alarm-outline','color' => 'danger','sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($overdue->count() . ' invoices')]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Due in next '.e(setting('reminder_days_before_due')).' days','value' => money($dueSoon->sum(fn ($i) => $i->balance())),'icon' => 'solar:calendar-outline','color' => 'warning','sub' => $dueSoon->count() . ' invoices']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Due in next '.e(setting('reminder_days_before_due')).' days','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(money($dueSoon->sum(fn ($i) => $i->balance()))),'icon' => 'solar:calendar-outline','color' => 'warning','sub' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dueSoon->count() . ' invoices')]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Over credit limit','value' => $customers->where('used', '>', 100)->count(),'icon' => 'solar:shield-warning-outline','color' => 'danger','sub' => 'customers']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Over credit limit','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($customers->where('used', '>', 100)->count()),'icon' => 'solar:shield-warning-outline','color' => 'danger','sub' => 'customers']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['label' => 'Near limit (80%+)','value' => $customers->whereBetween('used', [80, 100])->count(),'icon' => 'solar:shield-outline','color' => 'warning','sub' => 'customers']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Near limit (80%+)','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($customers->whereBetween('used', [80, 100])->count()),'icon' => 'solar:shield-outline','color' => 'warning','sub' => 'customers']); ?>
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

    <?php $__currentLoopData = ['Overdue invoices' => $overdue, 'Coming due - send a friendly reminder' => $dueSoon]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $title => $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0"><?php echo e($title); ?></h5></div>
            <div class="card-body table-wrap p-0">
                <div class="table-responsive">
                    <table class="table erp-table stack mb-0">
                        <thead><tr><th>Invoice</th><th>Customer</th><th>Due date</th><th class="text-end">Balance</th><th></th></tr></thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $c = $inv->customer; $wa = $c->whatsappNumber(); $days = $inv->daysOverdue();
                                    $msg = 'Dear ' . $c->name . ', this is a reminder from ' . setting('company_name') . ' that invoice ' . $inv->number . ' for ' . money($inv->balance())
                                        . ($days ? ' is overdue by ' . $days . ' days' : ' is due on ' . $inv->due_date->format('d M Y')) . '. Kindly arrange payment. Thank you.';
                                ?>
                                <tr>
                                    <td class="td-title"><a href="<?php echo e(route('invoices.show', $inv)); ?>" class="doc-link"><?php echo e($inv->number); ?></a> <span class="small text-muted"><?php echo e($inv->date->format('d M')); ?></span></td>
                                    <td data-label="Customer"><a href="<?php echo e(route('customers.show', $c)); ?>"><?php echo e($c->name); ?></a><div class="small text-muted"><?php echo e($c->phone); ?></div></td>
                                    <td data-label="Due" class="<?php echo e($days ? 'text-danger fw-semibold' : 'text-warning'); ?>"><?php echo e($inv->due_date->format('d M Y')); ?><div class="small"><?php echo e($days ? $days . ' days overdue' : 'in ' . now()->startOfDay()->diffInDays($inv->due_date) . ' days'); ?></div></td>
                                    <td data-label="Balance" class="text-end money fw-semibold"><?php echo e(money($inv->balance())); ?></td>
                                    <td class="td-actions text-end text-nowrap">
                                        <?php if($wa): ?><a target="_blank" rel="noopener" href="https://wa.me/<?php echo e($wa); ?>?text=<?php echo e(rawurlencode($msg)); ?>" class="btn btn-sm btn-success" title="WhatsApp reminder"><iconify-icon icon="ic:baseline-whatsapp" class="align-middle"></iconify-icon> Remind</a><?php endif; ?>
                                        <?php if($c->phone): ?><a href="tel:<?php echo e($c->phone); ?>" class="btn btn-sm btn-light" title="Call"><iconify-icon icon="solar:phone-outline" class="align-middle"></iconify-icon></a><?php endif; ?>
                                        <a href="<?php echo e(route('receipts.create', ['customer_id' => $c->id, 'invoice_id' => $inv->id])); ?>" class="btn btn-sm btn-soft-primary">Receive</a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['cols' => 5,'text' => 'Nothing here.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['cols' => 5,'text' => 'Nothing here.']); ?>
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
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Credit limit usage</h5></div>
        <div class="card-body table-wrap p-0">
            <div class="table-responsive">
                <table class="table erp-table stack mb-0">
                    <thead><tr><th>Customer</th><th>Terms</th><th class="text-end">Limit</th><th class="text-end">Balance</th><th style="min-width: 160px">Used</th><th class="text-end">Overdue</th></tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $u = $row['used']; ?>
                            <tr>
                                <td class="td-title"><a href="<?php echo e(route('customers.show', $row['customer'])); ?>" class="doc-link"><?php echo e($row['customer']->name); ?></a> <span class="small text-muted"><?php echo e($row['customer']->group?->name); ?></span></td>
                                <td data-label="Terms"><?php echo e($row['customer']->payment_terms_days); ?> days</td>
                                <td data-label="Limit" class="text-end money"><?php echo e(money($row['customer']->credit_limit)); ?></td>
                                <td data-label="Balance" class="text-end money"><?php echo e(money($row['balance'])); ?></td>
                                <td data-label="Used"><div class="d-flex align-items-center gap-2 w-100"><div class="progress flex-grow-1" style="height: 6px"><div class="progress-bar bg-<?php echo e($u > 100 ? 'danger' : ($u >= 80 ? 'warning' : 'success')); ?>" style="width: <?php echo e(min(100, $u)); ?>%"></div></div><span class="small fw-semibold"><?php echo e(number_format($u)); ?>%</span></div></td>
                                <td data-label="Overdue" class="text-end money <?php echo e($row['overdue'] > 0 ? 'text-danger fw-semibold' : 'text-muted'); ?>"><?php echo e(money($row['overdue'])); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Credit Control'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/finance/credit-control/index.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Users & Roles','crumbs' => ['Settings', 'Users']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Users & Roles','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Settings', 'Users'])]); ?>
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
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th>User</th><th>Role</th><th>Status</th><th>New password</th><th></th></tr></thead>
                            <tbody>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="td-title"><strong><?php echo e($u->name); ?></strong><div class="small text-muted"><?php echo e($u->email); ?></div></td>
                                        <td data-label="Role"><select form="u<?php echo e($u->id); ?>" name="type" class="form-select form-select-sm"><?php $__currentLoopData = \App\Models\User::ROLES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option <?php if($u->type === $r): echo 'selected'; endif; ?>><?php echo e($r); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></td>
                                        <td data-label="Status"><select form="u<?php echo e($u->id); ?>" name="active" class="form-select form-select-sm"><option value="1" <?php if($u->active): echo 'selected'; endif; ?>>Active</option><option value="0" <?php if(! $u->active): echo 'selected'; endif; ?>>Inactive</option></select></td>
                                        <td data-label="Password"><input form="u<?php echo e($u->id); ?>" type="password" name="password" class="form-control form-control-sm" placeholder="Leave blank" autocomplete="new-password"></td>
                                        <td class="td-actions text-end"><form method="POST" action="<?php echo e(route('users.update', $u)); ?>" id="u<?php echo e($u->id); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?><button class="btn btn-sm btn-light">Save</button></form></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">What each role is for</h5></div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item"><strong>Super Admin / Manager</strong> - everything, including price and discount overrides, credit-limit overrides, settings and users.</li>
                    <li class="list-group-item"><strong>Sales</strong> - quotations, invoices and receipts. Limited to the max discount and minimum price rules.</li>
                    <li class="list-group-item"><strong>Store</strong> - GRNs, deliveries, transfers and stock adjustments.</li>
                    <li class="list-group-item"><strong>Accounts</strong> - receipts, supplier payments, cheques, expenses and reports.</li>
                </ul>
                <div class="card-footer small text-muted">In this demo, menu access is not yet restricted by role. Pricing and credit overrides already are.</div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Add user</h5></div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('users.store')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
                        <div class="mb-2"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                        <div class="mb-2"><label class="form-label">Role</label><select name="type" class="form-select"><?php $__currentLoopData = \App\Models\User::ROLES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option><?php echo e($r); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                        <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required autocomplete="new-password"></div>
                        <button class="btn btn-primary w-100">Add user</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Users & Roles'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/admin/users.blade.php ENDPATH**/ ?>
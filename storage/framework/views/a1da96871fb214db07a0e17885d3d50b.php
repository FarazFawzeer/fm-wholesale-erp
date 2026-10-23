<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Cash, Bank & Card Accounts','crumbs' => ['Cash & Bank', 'Accounts']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Cash, Bank & Card Accounts','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Cash & Bank', 'Accounts'])]); ?>
        <button class="btn btn-soft-primary" data-bs-toggle="modal" data-bs-target="#transferModal">Transfer / deposit</button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#accountModal">New account</button>
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

    <div class="row g-3">
        <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-sm-6 col-xl-3">
                <a href="<?php echo e(route('accounts.show', $a)); ?>" class="card h-100 text-reset mb-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <span class="badge bg-<?php echo e($a->type === 'cash' ? 'success' : ($a->type === 'card' ? 'info' : 'primary')); ?>-subtle text-<?php echo e($a->type === 'cash' ? 'success' : ($a->type === 'card' ? 'info' : 'primary')); ?>"><?php echo e(ucfirst($a->type)); ?></span>
                            <iconify-icon icon="<?php echo e($a->type === 'cash' ? 'solar:wallet-outline' : ($a->type === 'card' ? 'solar:card-outline' : 'solar:buildings-outline')); ?>" class="fs-24 text-muted"></iconify-icon>
                        </div>
                        <div class="fw-semibold mt-2"><?php echo e($a->name); ?></div>
                        <div class="small text-muted"><?php echo e($a->bank_name); ?> <?php echo e($a->account_no); ?></div>
                        <div class="fs-4 fw-bold mt-2 money"><?php echo e(money($a->balance())); ?></div>
                    </div>
                </a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="modal fade" id="transferModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="<?php echo e(route('accounts.transfer')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header"><h5 class="modal-title">Transfer between accounts</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-2"><label class="form-label">From</label><select name="from_account_id" class="form-select"><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($a->id); ?>"><?php echo e($a->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                    <div class="mb-2"><label class="form-label">To</label><select name="to_account_id" class="form-select"><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($a->id); ?>" <?php if($loop->index === 1): echo 'selected'; endif; ?>><?php echo e($a->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                    <div class="row g-2"><div class="col-6"><label class="form-label">Amount</label><input type="number" step="0.01" name="amount" class="form-control" required></div><div class="col-6"><label class="form-label">Date</label><input type="date" name="date" value="<?php echo e(now()->format('Y-m-d')); ?>" class="form-control"></div></div>
                    <div class="mt-2"><label class="form-label">Description</label><input name="description" class="form-control" placeholder="e.g. Cash deposit"></div>
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Save transfer</button></div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="accountModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="<?php echo e(route('accounts.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header"><h5 class="modal-title">New account</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
                    <div class="mb-2"><label class="form-label">Type</label><select name="type" class="form-select"><option value="bank">Bank</option><option value="cash">Cash</option><option value="card">Card</option></select></div>
                    <div class="row g-2"><div class="col-6"><label class="form-label">Bank</label><input name="bank_name" class="form-control"></div><div class="col-6"><label class="form-label">Account no</label><input name="account_no" class="form-control"></div></div>
                    <div class="mt-2"><label class="form-label">Opening balance</label><input type="number" step="0.01" name="opening_balance" value="0" class="form-control"></div>
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Add account</button></div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Accounts & Balances'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/finance/accounts/index.blade.php ENDPATH**/ ?>
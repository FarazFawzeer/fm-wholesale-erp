<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'New Expense','crumbs' => ['Expenses' => route('expenses.index'), 'New']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'New Expense','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Expenses' => route('expenses.index'), 'New'])]); ?>
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
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <form method="POST" action="<?php echo e(route('expenses.store')); ?>" class="card">
                <?php echo csrf_field(); ?>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6"><label class="form-label">Date</label><input type="date" name="date" value="<?php echo e(old('date', now()->format('Y-m-d'))); ?>" class="form-control" required></div>
                        <div class="col-6"><label class="form-label">Amount (Rs)</label><input type="number" step="0.01" name="amount" value="<?php echo e(old('amount')); ?>" class="form-control" required inputmode="decimal"></div>
                        <div class="col-md-6"><label class="form-label">Category</label><select name="expense_category_id" class="form-select"><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(old('expense_category_id') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                        <div class="col-md-6"><label class="form-label">Paid from</label><select name="account_id" class="form-select"><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($a->id); ?>" <?php if(old('account_id') == $a->id): echo 'selected'; endif; ?>><?php echo e($a->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                        <div class="col-12"><label class="form-label">Paid to</label><input name="payee" value="<?php echo e(old('payee')); ?>" class="form-control"></div>
                        <div class="col-12"><label class="form-label">Description</label><input name="description" value="<?php echo e(old('description')); ?>" class="form-control"></div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2 sticky-actions">
                    <a href="<?php echo e(route('expenses.index')); ?>" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Save expense</button>
                </div>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'New Expense'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/finance/expenses/create.blade.php ENDPATH**/ ?>
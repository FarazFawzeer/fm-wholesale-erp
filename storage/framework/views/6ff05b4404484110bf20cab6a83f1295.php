<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Bulk Product Import','crumbs' => ['Products' => route('products.index'), 'Import']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Bulk Product Import','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Products' => route('products.index'), 'Import'])]); ?>
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

    <?php if(session('import_errors')): ?>
        <div class="alert alert-warning">
            <div class="fw-semibold">Some rows were skipped:</div>
            <ul class="mb-0"><?php $__currentLoopData = session('import_errors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">1. Download the template</h5>
                    <p class="text-muted">Fill one row per model and colour. Sizes can be a range (<code>39-44</code>) or a list (<code>36|37|38</code>). Brands and categories that don't exist yet are created automatically. Existing product codes are updated.</p>
                    <a href="<?php echo e(route('products.template')); ?>" class="btn btn-soft-primary mb-4"><iconify-icon icon="solar:download-outline" class="align-middle me-1"></iconify-icon>Download CSV template</a>

                    <h5 class="mb-3">2. Upload the file</h5>
                    <form method="POST" action="<?php echo e(route('products.import.store')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="file" name="file" accept=".csv,text/csv" class="form-control mb-3" required>
                        <div class="form-text mb-3">In Excel use <strong>File &rarr; Save As &rarr; CSV (Comma delimited)</strong>.</div>
                        <button class="btn btn-primary">Import products</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Columns</h5></div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <tbody>
                            <?php $__currentLoopData = ['code' => 'Article / product code (required, unique)', 'name' => 'Model name (required)', 'brand' => 'Brand name', 'category' => 'Category name', 'colour' => 'Colour', 'gender' => 'Men / Ladies / Kids / Unisex', 'sizes' => '39-44 or 36|37|38 (required)', 'pairs_per_carton' => 'Default 12', 'selling_price' => 'Wholesale price per pair (Rs)', 'min_price' => 'Optional minimum price', 'reorder_level' => 'Low-stock alert level in pairs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col => $desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr><td><code><?php echo e($col); ?></code></td><td class="text-muted"><?php echo e($desc); ?></td></tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Bulk Product Import'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/masters/products/import.blade.php ENDPATH**/ ?>
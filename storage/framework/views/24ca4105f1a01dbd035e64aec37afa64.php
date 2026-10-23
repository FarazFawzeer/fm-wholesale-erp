<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Products','crumbs' => ['Inventory', 'Products']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Products','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Inventory', 'Products'])]); ?>
        <a href="<?php echo e(route('products.import')); ?>" class="btn btn-soft-secondary"><iconify-icon icon="solar:upload-outline" class="align-middle me-1"></iconify-icon>Import</a>
        <a href="<?php echo e(route('products.create')); ?>" class="btn btn-primary"><iconify-icon icon="solar:add-circle-outline" class="align-middle me-1"></iconify-icon>New Product</a>
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

    <div class="card">
        <div class="card-body border-bottom">
            <form class="filter-bar" method="GET">
                <input type="search" name="q" value="<?php echo e(request('q')); ?>" class="form-control filter-grow" placeholder="Search code, model or colour">
                <select name="brand_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All brands</option>
                    <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($b->id); ?>" <?php if(request('brand_id') == $b->id): echo 'selected'; endif; ?>><?php echo e($b->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="category_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All categories</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php if(request('category_id') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button class="btn btn-light">Filter</button>
            </form>
        </div>
        <div class="card-body table-wrap p-0 p-md-0">
            <div class="table-responsive">
                <table class="table table-hover erp-table stack">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Sizes</th>
                            <th class="text-end">Stock (pairs)</th>
                            <th class="text-end">Landed cost</th>
                            <th class="text-end">Wholesale price</th>
                            <th class="text-end">Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php $low = (int) $p->on_hand <= $p->reorder_level; ?>
                            <tr>
                                <td class="td-title">
                                    <a href="<?php echo e(route('products.show', $p)); ?>" class="doc-link"><?php echo e($p->code); ?></a>
                                    <div class="small text-muted"><?php echo e($p->brand?->name); ?> <?php echo e($p->name); ?> &middot; <?php echo e($p->colour); ?></div>
                                </td>
                                <td data-label="Category"><?php echo e($p->category?->name); ?></td>
                                <td data-label="Sizes"><?php echo e(str_replace(',', ', ', $p->sizes)); ?></td>
                                <td data-label="Stock" class="text-end">
                                    <span class="fw-semibold <?php echo e($low ? 'text-danger' : ''); ?>"><?php echo e(qty($p->on_hand)); ?></span>
                                    <div class="small text-muted"><?php echo e(number_format($p->on_hand / max(1, $p->pairs_per_carton), 1)); ?> ctn</div>
                                </td>
                                <td data-label="Landed cost" class="text-end money"><?php echo e(money($p->cost_price)); ?></td>
                                <td data-label="Wholesale" class="text-end money fw-semibold"><?php echo e(money($p->selling_price)); ?></td>
                                <td data-label="Margin" class="text-end">
                                    <?php $m = $p->marginPercent(); ?>
                                    <span class="badge <?php echo e($m < setting('min_margin_percent') ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success'); ?>"><?php echo e(number_format($m, 1)); ?>%</span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['cols' => 7,'text' => 'No products found.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['cols' => 7,'text' => 'No products found.']); ?>
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
        <?php if($products->hasPages()): ?>
            <div class="card-footer"><?php echo e($products->links()); ?></div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Products'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/masters/products/index.blade.php ENDPATH**/ ?>
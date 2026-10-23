<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Bulk Price Update','crumbs' => ['Pricing', 'Bulk Price Update']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Bulk Price Update','crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Pricing', 'Bulk Price Update'])]); ?>
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
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label small">Brand</label>
                    <select name="brand_id" class="form-select">
                        <option value="">All</option>
                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($b->id); ?>" <?php if(request('brand_id') == $b->id): echo 'selected'; endif; ?>><?php echo e($b->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('category_id') == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small">Update method</label>
                    <select name="action" class="form-select" required>
                        <option value="markup" <?php if(request('action') == 'markup'): echo 'selected'; endif; ?>>Landed cost + markup %</option>
                        <option value="percent" <?php if(request('action') == 'percent'): echo 'selected'; endif; ?>>Change current price by %</option>
                        <option value="amount" <?php if(request('action') == 'amount'): echo 'selected'; endif; ?>>Change current price by Rs</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small">Value</label>
                    <input type="number" step="0.01" name="value" value="<?php echo e(request('value')); ?>" class="form-control" placeholder="Rule default">
                </div>
                <div class="col-6 col-md-1">
                    <label class="form-label small">Round to</label>
                    <select name="round" class="form-select">
                        <?php $__currentLoopData = [1, 10, 50, 100]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($r); ?>" <?php if(request('round', 10) == $r): echo 'selected'; endif; ?>><?php echo e($r); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-12 col-md-2"><button class="btn btn-light w-100">Preview</button></div>
            </form>
            <div class="form-text mt-2">Markup with an empty value uses each product's rule (product override &rarr; category &rarr; company default <?php echo e(setting('default_markup_percent')); ?>%).</div>
        </div>
    </div>

    <?php if($preview): ?>
        <form method="POST" action="<?php echo e(route('products.bulk-price.store')); ?>" data-confirm="Update the selling price of the selected products?">
            <?php echo csrf_field(); ?>
            <?php $__currentLoopData = ['action', 'value', 'round']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><input type="hidden" name="<?php echo e($f); ?>" value="<?php echo e(request($f)); ?>"><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div class="card">
                <div class="card-body table-wrap p-0">
                    <div class="table-responsive">
                        <table class="table erp-table stack mb-0">
                            <thead><tr><th><input type="checkbox" class="form-check-input" checked onclick="document.querySelectorAll('.js-pick').forEach(c => c.checked = this.checked)"></th><th>Product</th><th class="text-end">Landed cost</th><th class="text-end">Current price</th><th class="text-end">New price</th><th class="text-end">New margin</th></tr></thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $preview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php $p = $row['product']; $margin = $row['new'] > 0 ? ($row['new'] - $p->cost_price) / $row['new'] * 100 : 0; ?>
                                    <tr>
                                        <td class="td-actions"><input type="checkbox" class="form-check-input js-pick" name="products[]" value="<?php echo e($p->id); ?>" checked></td>
                                        <td class="td-title"><strong><?php echo e($p->code); ?></strong> <span class="text-muted small"><?php echo e($p->name); ?> <?php echo e($p->colour); ?></span></td>
                                        <td data-label="Cost" class="text-end money"><?php echo e(money($p->cost_price)); ?></td>
                                        <td data-label="Current" class="text-end money"><?php echo e(money($p->selling_price)); ?></td>
                                        <td data-label="New" class="text-end money fw-bold <?php echo e($row['new'] > $p->selling_price ? 'text-success' : ($row['new'] < $p->selling_price ? 'text-danger' : '')); ?>"><?php echo e(money($row['new'])); ?></td>
                                        <td data-label="Margin" class="text-end"><span class="badge <?php echo e($margin < setting('min_margin_percent') ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success'); ?>"><?php echo e(number_format($margin, 1)); ?>%</span></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <?php if (isset($component)) { $__componentOriginal4f22a152e0729cd34293e65bd200d933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f22a152e0729cd34293e65bd200d933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty','data' => ['cols' => 6,'text' => 'No products match the filter.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['cols' => 6,'text' => 'No products match the filter.']); ?>
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
                <div class="card-footer text-end sticky-actions"><button class="btn btn-primary">Apply new prices</button></div>
            </div>
        </form>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Bulk Price Update'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/masters/products/bulk-price.blade.php ENDPATH**/ ?>
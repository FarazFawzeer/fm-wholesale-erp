<?php $__env->startSection('content'); ?>
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => $product->exists ? 'Edit ' . $product->code : 'New Product','crumbs' => ['Products' => route('products.index'), $product->exists ? 'Edit' : 'New']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->exists ? 'Edit ' . $product->code : 'New Product'),'crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Products' => route('products.index'), $product->exists ? 'Edit' : 'New'])]); ?>
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

    <form method="POST" action="<?php echo e($product->exists ? route('products.update', $product) : route('products.store')); ?>">
        <?php echo csrf_field(); ?>
        <?php if($product->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Product details</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Product code / Article no *</label>
                                <input name="code" class="form-control" value="<?php echo e(old('code', $product->code)); ?>" required placeholder="e.g. WM-1021">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Model name *</label>
                                <input name="name" class="form-control" value="<?php echo e(old('name', $product->name)); ?>" required placeholder="e.g. Canvas Loafer">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Brand</label>
                                <select name="brand_id" class="form-select">
                                    <option value="">-</option>
                                    <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($b->id); ?>" <?php if(old('brand_id', $product->brand_id) == $b->id): echo 'selected'; endif; ?>><?php echo e($b->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">-</option>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($c->id); ?>" <?php if(old('category_id', $product->category_id) == $c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Colour</label>
                                <input name="colour" class="form-control" value="<?php echo e(old('colour', $product->colour)); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <?php $__currentLoopData = ['', 'Men', 'Ladies', 'Kids', 'Unisex']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($g); ?>" <?php if(old('gender', $product->gender) == $g): echo 'selected'; endif; ?>><?php echo e($g ?: '-'); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Size run *</label>
                                <input name="sizes" id="sizes" class="form-control" value="<?php echo e(old('sizes', $product->sizes)); ?>" required placeholder="39-44 or 36,37,38,39">
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                    <?php $__currentLoopData = ['Men 39-44' => '39-44', 'Ladies 36-41' => '36-41', 'Kids 28-33' => '28-33', 'Sports 39-45' => '39-45']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $run): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <button type="button" class="btn btn-sm btn-light" onclick="document.getElementById('sizes').value='<?php echo e($run); ?>'"><?php echo e($label); ?></button>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <div class="form-text">Each size gets its own SKU (e.g. <?php echo e($product->code ?: 'WM-1021'); ?>-40) so stock is tracked per size.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pairs per carton *</label>
                                <input type="number" name="pairs_per_carton" class="form-control" value="<?php echo e(old('pairs_per_carton', $product->pairs_per_carton)); ?>" min="1" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2"><?php echo e(old('description', $product->description)); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Pricing & stock rules</h5></div>
                    <div class="card-body">
                        <?php if($product->exists): ?>
                            <div class="alert alert-light small mb-3">
                                Landed cost: <strong><?php echo e(money($product->cost_price)); ?></strong><br>
                                Markup rule: <strong><?php echo e($product->effectiveMarkup()); ?>%</strong> &rarr; suggested <strong><?php echo e(money($product->suggestedPrice())); ?></strong>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label">Wholesale price per pair (Rs) *</label>
                            <input type="number" step="0.01" name="selling_price" class="form-control" value="<?php echo e(old('selling_price', $product->selling_price)); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Minimum selling price (Rs)</label>
                            <input type="number" step="0.01" name="min_price" class="form-control" value="<?php echo e(old('min_price', $product->min_price)); ?>" placeholder="Auto: cost + <?php echo e(setting('min_margin_percent')); ?>%">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Markup override %</label>
                            <input type="number" step="0.01" name="markup_percent" class="form-control" value="<?php echo e(old('markup_percent', $product->markup_percent)); ?>" placeholder="Use category / default rule">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Re-order level (pairs) *</label>
                            <input type="number" name="reorder_level" class="form-control" value="<?php echo e(old('reorder_level', $product->reorder_level)); ?>" required>
                        </div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" <?php if(old('is_active', $product->is_active)): echo 'checked'; endif; ?>>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-actions d-flex gap-2 justify-content-end mb-4">
            <a href="<?php echo e($product->exists ? route('products.show', $product) : route('products.index')); ?>" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">Save product</button>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => $product->exists ? 'Edit Product' : 'New Product'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/masters/products/form.blade.php ENDPATH**/ ?>
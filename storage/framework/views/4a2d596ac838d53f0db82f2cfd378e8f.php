<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['label', 'value', 'icon' => 'solar:chart-2-outline', 'color' => 'primary', 'sub' => null, 'href' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['label', 'value', 'icon' => 'solar:chart-2-outline', 'color' => 'primary', 'sub' => null, 'href' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="card kpi-card h-100 mb-0">
    <?php if($href): ?><a href="<?php echo e($href); ?>" class="stretched-link" aria-label="<?php echo e($label); ?>"></a><?php endif; ?>
    <div class="card-body">
        <div class="kpi-icon bg-<?php echo e($color); ?>-subtle text-<?php echo e($color); ?>">
            <iconify-icon icon="<?php echo e($icon); ?>"></iconify-icon>
        </div>
        <div class="min-w-0">
            <div class="kpi-label"><?php echo e($label); ?></div>
            <div class="kpi-value"><?php echo e($value); ?></div>
            <?php if($sub): ?><div class="kpi-sub"><?php echo $sub; ?></div><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/components/kpi.blade.php ENDPATH**/ ?>
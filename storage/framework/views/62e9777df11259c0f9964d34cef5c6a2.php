<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['text' => 'Nothing here yet.', 'cols' => 1]));

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

foreach (array_filter((['text' => 'Nothing here yet.', 'cols' => 1]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<tr>
    <td colspan="<?php echo e($cols); ?>" class="text-center text-muted py-5">
        <iconify-icon icon="solar:box-minimalistic-outline" class="fs-32 d-block mx-auto mb-2"></iconify-icon>
        <?php echo e($text); ?>

    </td>
</tr>
<?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/components/empty.blade.php ENDPATH**/ ?>
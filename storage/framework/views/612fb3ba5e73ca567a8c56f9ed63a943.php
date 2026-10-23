<?php if (isset($component)) { $__componentOriginaldfe4359e059d4729aaaea417a6046e0e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldfe4359e059d4729aaaea417a6046e0e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.print-doc','data' => ['title' => 'DELIVERY NOTE','back' => route('invoices.show', $note->invoice)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('print-doc'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'DELIVERY NOTE','back' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('invoices.show', $note->invoice))]); ?>
     <?php $__env->slot('meta', null, []); ?> 
        <div class="b"><?php echo e($note->number); ?></div>
        <div class="muted">Date: <?php echo e($note->date->format('d M Y')); ?></div>
        <div class="muted">Invoice: <?php echo e($note->invoice->number); ?></div>
     <?php $__env->endSlot(); ?>

    <div class="parties">
        <div>
            <div class="muted">Deliver to</div>
            <div class="b"><?php echo e($note->customer->business_name ?: $note->customer->name); ?></div>
            <div><?php echo e($note->customer->address); ?> <?php echo e($note->customer->city); ?></div>
            <div><?php echo e($note->customer->phone); ?></div>
        </div>
        <div class="r">
            <div class="muted">From store</div><div class="b"><?php echo e($note->warehouse->name); ?></div>
            <div class="muted">Vehicle</div><div><?php echo e($note->vehicle_no ?: '-'); ?></div>
        </div>
    </div>

    <table>
        <thead><tr><th>#</th><th>Item</th><th>Sizes</th><th class="r">Pairs</th></tr></thead>
        <tbody>
            <?php $__currentLoopData = $byProduct->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $p = $items->first()->line->product; ?>
                <tr>
                    <td><?php echo e($n + 1); ?></td>
                    <td><span class="b"><?php echo e($p->code); ?></span> <?php echo e($p->brand?->name); ?> <?php echo e($p->name); ?> <?php echo e($p->colour); ?></td>
                    <td class="sizes"><?php $__currentLoopData = $items->sortBy(fn ($i) => (float) $i->variant->size); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo e($i->variant->size); ?>×<?php echo e($i->qty); ?><?php echo e(! $loop->last ? ', ' : ''); ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></td>
                    <td class="r b"><?php echo e($items->sum('qty')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <table class="totals"><tr class="grand"><td>Total pairs</td><td class="r"><?php echo e($note->items->sum('qty')); ?></td></tr></table>

    <?php if($note->notes): ?><p class="muted">Note: <?php echo e($note->notes); ?></p><?php endif; ?>
    <div class="foot">
        <div class="sign">Issued by <?php echo e($note->user?->name); ?></div>
        <div class="sign">Received in good condition<?php echo e($note->received_by ? ' - ' . $note->received_by : ''); ?></div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldfe4359e059d4729aaaea417a6046e0e)): ?>
<?php $attributes = $__attributesOriginaldfe4359e059d4729aaaea417a6046e0e; ?>
<?php unset($__attributesOriginaldfe4359e059d4729aaaea417a6046e0e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldfe4359e059d4729aaaea417a6046e0e)): ?>
<?php $component = $__componentOriginaldfe4359e059d4729aaaea417a6046e0e; ?>
<?php unset($__componentOriginaldfe4359e059d4729aaaea417a6046e0e); ?>
<?php endif; ?>
<?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/sales/deliveries/show.blade.php ENDPATH**/ ?>
<?php if (isset($component)) { $__componentOriginaldfe4359e059d4729aaaea417a6046e0e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldfe4359e059d4729aaaea417a6046e0e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.print-doc','data' => ['title' => 'CREDIT NOTE','back' => route('invoices.show', $note->invoice)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('print-doc'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'CREDIT NOTE','back' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('invoices.show', $note->invoice))]); ?>
     <?php $__env->slot('meta', null, []); ?> 
        <div class="b"><?php echo e($note->number); ?></div>
        <div class="muted">Date: <?php echo e($note->date->format('d M Y')); ?></div>
        <div class="muted">Against invoice: <?php echo e($note->invoice->number); ?></div>
     <?php $__env->endSlot(); ?>

    <div class="parties">
        <div>
            <div class="muted">Customer</div>
            <div class="b"><?php echo e($note->customer->business_name ?: $note->customer->name); ?></div>
            <div><?php echo e($note->customer->address); ?> <?php echo e($note->customer->city); ?></div>
        </div>
        <div class="r">
            <div class="muted">Reason</div><div class="b"><?php echo e($note->reason); ?></div>
            <div class="muted"><?php echo e($note->restock ? 'Goods returned to stock' : 'Price adjustment'); ?></div>
        </div>
    </div>

    <table>
        <thead><tr><th>Item</th><th>Size</th><th class="r">Pairs</th><th class="r">Price</th><th class="r">Amount</th></tr></thead>
        <tbody>
            <?php $__currentLoopData = $note->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><span class="b"><?php echo e($i->line->product->code); ?></span> <?php echo e($i->line->product->name); ?> <?php echo e($i->line->product->colour); ?></td>
                    <td><?php echo e($i->variant->size); ?></td>
                    <td class="r"><?php echo e($i->qty); ?></td>
                    <td class="r"><?php echo e(money($i->unit_price, false)); ?></td>
                    <td class="r"><?php echo e(money($i->line_total, false)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <table class="totals"><tr class="grand"><td>Credit (Rs)</td><td class="r"><?php echo e(money($note->total, false)); ?></td></tr></table>
    <div class="foot"><div class="sign">Authorised by <?php echo e($note->user?->name); ?></div><div class="sign">Customer</div></div>
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
<?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/sales/credit-notes/show.blade.php ENDPATH**/ ?>
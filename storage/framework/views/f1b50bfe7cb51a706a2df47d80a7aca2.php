<?php if (isset($component)) { $__componentOriginaldfe4359e059d4729aaaea417a6046e0e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldfe4359e059d4729aaaea417a6046e0e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.print-doc','data' => ['title' => $invoice->sale_type === 'cash' ? 'CASH INVOICE' : 'INVOICE','back' => route('invoices.show', $invoice)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('print-doc'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoice->sale_type === 'cash' ? 'CASH INVOICE' : 'INVOICE'),'back' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('invoices.show', $invoice))]); ?>
     <?php $__env->slot('meta', null, []); ?> 
        <div class="b"><?php echo e($invoice->number); ?></div>
        <div class="muted">Date: <?php echo e($invoice->date->format('d M Y')); ?></div>
        <div class="muted">Due: <?php echo e($invoice->due_date->format('d M Y')); ?></div>
     <?php $__env->endSlot(); ?>

    <div class="parties">
        <div>
            <div class="muted">Bill to</div>
            <div class="b"><?php echo e($invoice->customer->business_name ?: $invoice->customer->name); ?></div>
            <div><?php echo e($invoice->customer->address); ?> <?php echo e($invoice->customer->city); ?></div>
            <div><?php echo e($invoice->customer->phone); ?></div>
        </div>
        <div class="r">
            <div class="muted">Customer code</div><div class="b"><?php echo e($invoice->customer->code); ?></div>
            <div class="muted">Sales person</div><div><?php echo e($invoice->user?->name); ?></div>
        </div>
    </div>

    <table>
        <thead><tr><th>#</th><th>Item</th><th class="r">Pairs</th><th class="r">Price</th><th class="r">Disc</th><th class="r">Amount</th></tr></thead>
        <tbody>
            <?php $__currentLoopData = $byProduct->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n => $rows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $f = $rows->first(); ?>
                <tr>
                    <td><?php echo e($n + 1); ?></td>
                    <td><span class="b"><?php echo e($f->product->code); ?></span> <?php echo e($f->product->brand?->name); ?> <?php echo e($f->product->name); ?> <?php echo e($f->product->colour); ?>

                        <div class="sizes">Sizes: <?php $__currentLoopData = $rows->sortBy(fn ($r) => (float) $r->variant->size); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo e($r->variant->size); ?>×<?php echo e($r->qty); ?><?php echo e(! $loop->last ? ', ' : ''); ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></td>
                    <td class="r"><?php echo e($rows->sum('qty')); ?></td>
                    <td class="r"><?php echo e(money($f->unit_price, false)); ?></td>
                    <td class="r"><?php echo e($f->discount_percent > 0 ? $f->discount_percent . '%' : ''); ?></td>
                    <td class="r"><?php echo e(money($rows->sum('line_total'), false)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Total pairs</td><td class="r"><?php echo e($invoice->lines->sum('qty')); ?></td></tr>
        <tr><td>Gross</td><td class="r"><?php echo e(money($invoice->subtotal, false)); ?></td></tr>
        <?php if($invoice->discount_amount > 0): ?><tr><td>Discount</td><td class="r">-<?php echo e(money($invoice->discount_amount, false)); ?></td></tr><?php endif; ?>
        <tr class="grand"><td>Total (Rs)</td><td class="r"><?php echo e(money($invoice->total, false)); ?></td></tr>
        <?php if($invoice->paid_amount > 0): ?><tr><td>Paid</td><td class="r"><?php echo e(money($invoice->paid_amount, false)); ?></td></tr><?php endif; ?>
        <?php if($invoice->credited_amount > 0): ?><tr><td>Credit notes</td><td class="r">-<?php echo e(money($invoice->credited_amount, false)); ?></td></tr><?php endif; ?>
        <tr><td class="b">Balance</td><td class="r b"><?php echo e(money($invoice->balance(), false)); ?></td></tr>
    </table>

    <?php if($invoice->notes): ?><p class="muted">Note: <?php echo e($invoice->notes); ?></p><?php endif; ?>
    <p class="muted">Goods once sold will be exchanged only with the original invoice within 7 days. Cheques to be written in favour of <?php echo e(setting('company_name')); ?>.</p>

    <div class="foot">
        <div class="sign">Prepared by</div>
        <div class="sign">Customer signature</div>
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
<?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/sales/invoices/print.blade.php ENDPATH**/ ?>
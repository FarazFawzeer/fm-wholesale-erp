<?php if (isset($component)) { $__componentOriginaldfe4359e059d4729aaaea417a6046e0e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldfe4359e059d4729aaaea417a6046e0e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.print-doc','data' => ['title' => $payment->direction === 'in' ? 'RECEIPT' : 'PAYMENT VOUCHER','back' => route($payment->direction === 'in' ? 'receipts.index' : 'supplier-payments.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('print-doc'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($payment->direction === 'in' ? 'RECEIPT' : 'PAYMENT VOUCHER'),'back' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route($payment->direction === 'in' ? 'receipts.index' : 'supplier-payments.index'))]); ?>
     <?php $__env->slot('meta', null, []); ?> 
        <div class="b"><?php echo e($payment->number); ?></div>
        <div class="muted">Date: <?php echo e($payment->date->format('d M Y')); ?></div>
     <?php $__env->endSlot(); ?>

    <?php if(session('success')): ?><p style="background:#ecfdf5;color:#047857;padding:8px 10px;border-radius:6px"><?php echo e(session('success')); ?></p><?php endif; ?>

    <div class="parties">
        <div>
            <div class="muted"><?php echo e($payment->direction === 'in' ? 'Received from' : 'Paid to'); ?></div>
            <div class="b"><?php echo e($party?->name); ?></div>
            <div><?php echo e($party?->address); ?></div>
        </div>
        <div class="r"><div class="muted">Amount</div><div class="b" style="font-size:20px"><?php echo e(money($payment->total)); ?></div></div>
    </div>

    <table>
        <thead><tr><th>Method</th><th>Details</th><th class="r">Amount</th></tr></thead>
        <tbody>
            <?php $__currentLoopData = $payment->lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e(\App\Models\Payment::METHODS[$l->method] ?? $l->method); ?></td>
                    <td>
                        <?php if($l->cheque): ?>
                            Cheque #<?php echo e($l->cheque->cheque_no); ?> <?php echo e($l->cheque->bank); ?> dated <?php echo e($l->cheque->cheque_date->format('d M Y')); ?>

                            <span class="muted">(<?php echo e(ucfirst($l->cheque->status)); ?>)</span>
                        <?php else: ?>
                            <?php echo e($l->account?->name); ?> <?php echo e($l->reference ? '· ' . $l->reference : ''); ?>

                        <?php endif; ?>
                    </td>
                    <td class="r"><?php echo e(money($l->amount, false)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <?php if($payment->allocations->count()): ?>
        <h4 style="margin:18px 0 6px">Applied to</h4>
        <table>
            <tbody>
                <?php $__currentLoopData = $payment->allocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr><td><?php echo e($a->invoice?->number ?? $a->purchaseOrder?->number); ?></td><td class="r"><?php echo e(money($a->amount, false)); ?></td></tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php if($payment->notes): ?><p class="muted"><?php echo nl2br(e($payment->notes)); ?></p><?php endif; ?>
    <div class="foot"><div class="sign"><?php echo e($payment->user?->name); ?></div><div class="sign">Signature</div></div>
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
<?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/finance/payments/show.blade.php ENDPATH**/ ?>

<select name="customer_id" id="customer_id" class="form-select" required>
    <option value="">Select customer</option>
    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($c->id); ?>" <?php if($selected == $c->id): echo 'selected'; endif; ?>
            data-group="<?php echo e($c->customer_group_id); ?>" data-group-name="<?php echo e($c->group?->name); ?>"
            data-limit="<?php echo e($c->credit_limit); ?>" data-balance="<?php echo e($c->ui_balance); ?>" data-overdue="<?php echo e($c->ui_overdue); ?>"
            data-terms="<?php echo e($c->payment_terms_days); ?>"><?php echo e($c->name); ?><?php echo e($c->city ? ' - ' . $c->city : ''); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
<div id="credit-panel" class="small mt-2 d-none">
    <div class="d-flex flex-wrap gap-2">
        <span class="badge bg-light text-dark" data-k="group"></span>
        <span class="badge bg-light text-dark" data-k="terms"></span>
    </div>
    <div class="row g-1 mt-1">
        <div class="col-4"><div class="text-muted">Balance</div><div class="fw-semibold" data-k="balance"></div></div>
        <div class="col-4"><div class="text-muted">Limit</div><div class="fw-semibold" data-k="limit"></div></div>
        <div class="col-4"><div class="text-muted">Overdue</div><div class="fw-semibold" data-k="overdue"></div></div>
    </div>
    <div class="alert alert-danger py-1 px-2 mt-2 mb-0 d-none" data-k="warn"></div>
</div>
<?php $__env->startPush('page-js'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sel = document.getElementById('customer_id');
            const panel = document.getElementById('credit-panel');
            const fmt = (n) => 'Rs ' + window.erpFmt(parseFloat(n) || 0);
            const show = () => {
                const o = sel.options[sel.selectedIndex];
                if (!o || !o.value) return panel.classList.add('d-none');
                const d = o.dataset, k = (n) => panel.querySelector(`[data-k="${n}"]`);
                panel.classList.remove('d-none');
                k('group').textContent = d.groupName || 'No group';
                k('terms').textContent = (d.terms || 0) + ' days credit';
                k('balance').textContent = fmt(d.balance);
                k('limit').textContent = parseFloat(d.limit) > 0 ? fmt(d.limit) : 'No limit';
                k('overdue').textContent = fmt(d.overdue);
                k('overdue').classList.toggle('text-danger', parseFloat(d.overdue) > 0);
                const total = parseFloat(document.querySelector('[data-line-editor]')?.dataset.total || 0);
                const over = parseFloat(d.limit) > 0 && parseFloat(d.balance) + total > parseFloat(d.limit);
                const w = k('warn');
                const msgs = [];
                if (over) msgs.push('This sale will exceed the credit limit.');
                if (parseFloat(d.overdue) > 0) msgs.push('Customer has overdue invoices.');
                w.textContent = msgs.join(' ');
                w.classList.toggle('d-none', !msgs.length);
            };
            sel.addEventListener('change', show);
            document.addEventListener('erp:total', show);
            show();
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH F:\Nexas\FM Wholesale ERP\resources\views/sales/partials/customer-select.blade.php ENDPATH**/ ?>
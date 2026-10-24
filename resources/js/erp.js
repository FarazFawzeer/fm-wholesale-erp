/**
 * FM Wholesale ERP - shared front-end behaviour:
 * searchable selects, the size-grid line editor, split payment lines, confirms.
 */
import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css';

const fmt = (n) => new Intl.NumberFormat('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n || 0);
const num = (v) => parseFloat(v) || 0;
const el = (html) => {
    const t = document.createElement('template');
    t.innerHTML = html.trim();
    return t.content.firstElementChild;
};
const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

window.erpFmt = fmt;

// ---------- Searchable selects ----------
function initChoices(root = document) {
    root.querySelectorAll('select[data-choices]:not(.choices__input)').forEach((select) => {
        select._choices = new Choices(select, {
            searchEnabled: true,
            shouldSort: false,
            itemSelectText: '',
            searchResultLimit: 30,
            fuseOptions: { threshold: 0.25, ignoreLocation: true },
            placeholder: true,
            placeholderValue: select.dataset.placeholder || 'Select...',
        });
    });
}

// ---------- Confirm before submitting dangerous / posting forms ----------
function initConfirms() {
    document.addEventListener('submit', (e) => {
        const form = e.target;
        const msg = form.dataset.confirm;
        if (!msg || form.dataset.confirmed) return;
        e.preventDefault();
        const go = () => {
            form.dataset.confirmed = '1';
            form.submit();
        };
        if (window.Swal) {
            window.Swal.fire({
                title: 'Are you sure?',
                text: msg,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, continue',
                confirmButtonColor: '#1a80f8',
            }).then((r) => r.isConfirmed && go());
        } else if (confirm(msg)) {
            go();
        }
    });
}

// ---------- Item line editor: product, size range, pairs, price ----------
const productLabel = (p) =>
    `${p.code} - ${p.name}${p.colour ? ' - ' + p.colour : ''}${p.size && p.size !== '-' ? ' (' + p.size + ')' : ''}`;

class LineEditor {
    constructor(root) {
        this.root = root;
        this.mode = root.dataset.mode; // purchase | sales | stock
        this.products = JSON.parse(root.dataset.products || '[]');
        this.byId = Object.fromEntries(this.products.map((p) => [p.id, p]));
        this.lines = root.querySelector('.lines');
        this.counter = 0;
        this.groupId = null;
        this.warehouseId = root.dataset.warehouse || null;
        this.priceLabel = root.dataset.priceLabel || 'Price per pair (Rs)';

        const picker = root.querySelector('.product-picker');
        picker.innerHTML = '<option value="">Search product by name or code...</option>' +
            this.products.map((p) => `<option value="${p.id}">${esc(productLabel(p))}</option>`).join('');
        this.choices = new Choices(picker, {
            searchEnabled: true,
            shouldSort: false,
            itemSelectText: '',
            searchResultLimit: 40,
            placeholder: true,
            searchPlaceholderValue: 'Type product name or code',
            fuseOptions: { threshold: 0.25, ignoreLocation: true },
        });
        // 'choice' fires only for user picks (mouse or keyboard), not for programmatic resets
        picker.addEventListener('choice', (e) => {
            const value = e.detail?.choice?.value ?? e.detail?.value;
            if (!value) return;
            setTimeout(() => {
                this.addLine({ product_id: parseInt(value, 10) });
                this.choices.removeActiveItems();
                this.choices.setChoiceByValue('');
            }, 0);
        });

        // "+ New product" next to the search box
        const choicesBox = picker.closest('.choices');
        const bar = el('<div class="picker-bar"></div>');
        choicesBox.parentNode.insertBefore(bar, choicesBox);
        bar.appendChild(choicesBox);
        bar.appendChild(el('<button type="button" class="btn btn-soft-primary" data-quick-add="product" title="New product">+ New<span class="d-none d-sm-inline">&nbsp;product</span></button>'));
        document.addEventListener('erp:product-created', (e) => {
            this.addProduct(e.detail.product);
            if (e.detail.editor === this.root) this.addLine({ product_id: e.detail.product.id });
        });

        const customer = document.querySelector(root.dataset.customerSelect || '#customer_id');
        if (customer && this.mode === 'sales') {
            const readGroup = () => {
                const opt = customer.options[customer.selectedIndex];
                this.groupId = opt ? opt.dataset.group : null;
                document.dispatchEvent(new CustomEvent('erp:customer', { detail: opt ? opt.dataset : {} }));
            };
            customer.addEventListener('change', () => {
                readGroup();
                this.repriceAll();
            });
            readGroup();
        }
        const wh = document.querySelector(root.dataset.warehouseSelect || '#warehouse_id');
        if (wh) {
            wh.addEventListener('change', () => {
                this.warehouseId = wh.value;
                this.lines.querySelectorAll('.line-card').forEach((c) => this.renderStock(c));
            });
            this.warehouseId = wh.value;
        }

        JSON.parse(root.dataset.initial || '[]').forEach((l) => this.addLine(l));
        this.updateTotals();
    }

    addProduct(p) {
        if (this.byId[p.id]) return;
        this.products.push(p);
        this.byId[p.id] = p;
        this.choices.setChoices([{ value: String(p.id), label: productLabel(p) }], 'value', 'label', false);
    }

    priceFor(p) {
        if (this.mode === 'purchase') return p.last_fx_price || p.cost || '';
        if (this.mode === 'stock') return p.cost;
        if (this.groupId && p.group_prices && p.group_prices[this.groupId] !== undefined) return p.group_prices[this.groupId];
        return p.price;
    }

    addLine(data) {
        const p = this.byId[data.product_id];
        if (!p || !p.variants.length) return;
        const existing = this.lines.querySelector(`.line-card[data-product="${p.id}"]`);
        if (existing && !data.qty) {
            existing.scrollIntoView({ behavior: 'smooth', block: 'center' });
            existing.querySelector('.js-qty').focus();
            return;
        }
        const variant = p.variants[0];
        const qty = data.qty ? Object.values(data.qty).reduce((a, b) => a + num(b), 0) : '';
        const i = this.counter++;
        const name = `lines[${i}]`;
        const price = data.unit_price ?? this.priceFor(p);
        const showPrice = this.mode !== 'stock';
        const meta = [p.code, p.size && p.size !== '-' ? 'Size ' + p.size : null, p.colour, p.brand].filter(Boolean).map(esc).join(' &middot; ');
        const card = el(`
            <div class="line-card" data-product="${p.id}" data-variant="${variant.id}">
                <input type="hidden" name="${name}[product_id]" value="${p.id}">
                <div class="line-head">
                    <div class="min-w-0">
                        <div class="line-title">${esc(p.name)}</div>
                        <div class="line-meta">${meta}</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-soft-danger js-remove" title="Remove"><iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon></button>
                </div>
                <div class="line-fields">
                    <div><label class="form-label small mb-1">Pairs</label>
                        <input type="number" min="0" inputmode="numeric" class="form-control js-qty" name="${name}[qty][${variant.id}]" value="${qty || ''}" placeholder="0">
                        <small class="js-stock text-muted"></small></div>
                    ${showPrice ? `<div><label class="form-label small mb-1">${esc(this.priceLabel)}</label>
                        <input type="number" step="0.01" min="0" inputmode="decimal" class="form-control js-price" name="${name}[unit_price]" value="${price}"></div>` : ''}
                    ${this.mode === 'sales' ? `<div><label class="form-label small mb-1">Discount %</label>
                        <input type="number" step="0.01" min="0" max="100" inputmode="decimal" class="form-control js-disc" name="${name}[discount_percent]" value="${data.discount_percent ?? 0}"></div>` : ''}
                    ${showPrice ? `<div class="line-total"><span class="small text-muted">Total</span><strong class="js-total money">0.00</strong></div>` : ''}
                </div>
                <div class="js-warn small text-danger mt-1"></div>
            </div>`);
        this.lines.appendChild(card);
        card.addEventListener('input', () => this.updateTotals());
        card.querySelector('.js-remove').addEventListener('click', () => {
            card.remove();
            this.updateTotals();
        });
        this.renderStock(card);
        this.updateTotals();
        if (!qty) card.querySelector('.js-qty').focus();
    }

    stockOf(card) {
        const p = this.byId[card.dataset.product];
        const v = p.variants[0];
        return v && v.stock ? num(v.stock[this.warehouseId]) : 0;
    }

    renderStock(card) {
        if (this.mode === 'purchase') return;
        const stock = this.stockOf(card);
        const small = card.querySelector('.js-stock');
        small.textContent = `${stock} pairs in stock`;
        small.classList.toggle('text-danger', stock <= 0);
    }

    repriceAll() {
        this.lines.querySelectorAll('.line-card').forEach((card) => {
            const inp = card.querySelector('.js-price');
            if (inp) inp.value = this.priceFor(this.byId[card.dataset.product]);
        });
        this.updateTotals();
    }

    updateTotals() {
        let pairs = 0;
        let gross = 0;
        let net = 0;
        this.lines.querySelectorAll('.line-card').forEach((card) => {
            const p = this.byId[card.dataset.product];
            const qty = num(card.querySelector('.js-qty').value);
            const price = num(card.querySelector('.js-price')?.value);
            const disc = num(card.querySelector('.js-disc')?.value);
            const netPrice = price * (1 - disc / 100);
            const lineNet = qty * netPrice;
            const totalEl = card.querySelector('.js-total');
            if (totalEl) totalEl.textContent = fmt(lineNet);
            const msgs = [];
            if (this.mode !== 'purchase' && this.root.dataset.checkStock === '1' && qty > this.stockOf(card)) msgs.push('Not enough stock.');
            if (this.mode === 'sales' && price > 0 && netPrice < p.min_price) msgs.push(`Price is below the lowest allowed (Rs ${fmt(p.min_price)}).`);
            if (this.mode === 'sales' && disc > num(this.root.dataset.maxDiscount) && this.root.dataset.manager !== '1') msgs.push(`Discount is above the allowed ${this.root.dataset.maxDiscount}%.`);
            card.querySelector('.js-warn').textContent = msgs.join(' ');
            pairs += qty;
            gross += qty * price;
            net += lineNet;
        });
        const set = (sel, v) => document.querySelectorAll(sel).forEach((e) => (e.textContent = v));
        set('.js-sum-pairs', pairs);
        set('.js-sum-gross', fmt(gross));
        set('.js-sum-discount', fmt(gross - net));
        set('.js-sum-total', fmt(net));
        this.root.dataset.total = net;
        document.dispatchEvent(new CustomEvent('erp:total', { detail: { total: net, pairs } }));
        this.root.querySelector('.empty-lines')?.classList.toggle('d-none', this.lines.children.length > 0);
    }
}

// ---------- Quick add pop-ups: new product / customer / supplier ----------
function addOption(select, item) {
    if (select._choices) {
        select._choices.setChoices([{ value: String(item.id), label: item.label, selected: true }], 'value', 'label', false);
    } else {
        const o = new Option(item.label, item.id, true, true);
        Object.entries(item.data || {}).forEach(([k, v]) => (o.dataset[k] = v ?? ''));
        select.add(o);
        select.value = String(item.id);
    }
    select.dispatchEvent(new Event('change', { bubbles: true }));
}

function initQuickAdd() {
    let target = null;
    let sourceEditor = null;
    let reloadParam = null;

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-quick-add]');
        if (!btn) return;
        e.preventDefault();
        const modal = document.getElementById('qa-' + btn.dataset.quickAdd);
        if (!modal) return;
        target = btn.dataset.target ? document.querySelector(btn.dataset.target) : null;
        sourceEditor = btn.closest('[data-line-editor]');
        reloadParam = btn.dataset.reload || null;
        const form = modal.querySelector('form');
        form.reset();
        form.querySelector('.js-errors').classList.add('d-none');
        window.bootstrap.Modal.getOrCreateInstance(modal).show();
        setTimeout(() => form.querySelector('input[name=name]')?.focus(), 300);
    });

    document.querySelectorAll('.js-gen-code').forEach((b) =>
        b.addEventListener('click', async () => {
            const r = await fetch(b.dataset.url, { headers: { Accept: 'application/json' } });
            b.closest('.input-group').querySelector('input').value = (await r.json()).code;
        }),
    );

    document.querySelectorAll('.js-quick-form').forEach((form) =>
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submit = form.querySelector('button:not([type=button])');
            const errors = form.querySelector('.js-errors');
            submit.disabled = true;
            try {
                const r = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const json = await r.json();
                if (!r.ok) {
                    errors.innerHTML = Object.values(json.errors || { e: [json.message || 'Could not save.'] }).flat().map(esc).join('<br>');
                    errors.classList.remove('d-none');
                    return;
                }
                window.bootstrap.Modal.getInstance(form.closest('.modal')).hide();
                const kind = form.closest('.modal').id.replace('qa-', '');
                if (kind === 'product') {
                    document.dispatchEvent(new CustomEvent('erp:product-created', { detail: { product: json, editor: sourceEditor } }));
                } else if (reloadParam) {
                    const url = new URL(window.location.href);
                    url.searchParams.set(reloadParam, json.id);
                    window.location.href = url.toString();
                } else if (target) {
                    addOption(target, json);
                }
            } catch (err) {
                errors.textContent = 'Could not save. Check your connection and try again.';
                errors.classList.remove('d-none');
            } finally {
                submit.disabled = false;
            }
        }),
    );
}

// ---------- Split payment lines (cash + cheque + card ...) ----------
class PaymentLines {
    constructor(root) {
        this.root = root;
        this.accounts = JSON.parse(root.dataset.accounts || '[]');
        this.body = root.querySelector('.pay-lines');
        this.i = 0;
        root.querySelector('.js-add-pay').addEventListener('click', () => this.add());
        JSON.parse(root.dataset.initial || '[]').forEach((l) => this.add(l));
        if (!this.body.children.length && root.dataset.startEmpty !== '1') this.add();
        document.addEventListener('erp:total', (e) => {
            this.target = e.detail.total;
            // Keep a single untouched payment line in step with the invoice total
            const rows = this.body.querySelectorAll('.pay-row');
            if (rows.length === 1 && !rows[0].dataset.touched) {
                rows[0].querySelector('.js-amount').value = this.target ? this.target.toFixed(2) : '';
            }
            this.update();
        });
        this.target = num(root.dataset.target || document.querySelector('[data-line-editor]')?.dataset.total);
        const rows = this.body.querySelectorAll('.pay-row');
        if (rows.length === 1 && !rows[0].querySelector('.js-amount').value && this.target) {
            rows[0].querySelector('.js-amount').value = this.target.toFixed(2);
        }
        this.update();
    }

    accountOptions(method, selected) {
        const type = method === 'cash' ? 'cash' : method === 'card' ? 'card' : 'bank';
        return this.accounts
            .filter((a) => a.type === type)
            .map((a) => `<option value="${a.id}" ${a.id == selected ? 'selected' : ''}>${esc(a.name)}</option>`)
            .join('');
    }

    add(data = {}) {
        const i = this.i++;
        const n = `payments[${i}]`;
        const method = data.method || 'cash';
        const row = el(`
            <div class="border rounded-3 p-2 mb-2 pay-row">
                <div class="row g-2 align-items-end">
                    <div class="col-5"><label class="form-label small mb-1">Method</label>
                        <select class="form-select form-select-sm js-method" name="${n}[method]">
                            <option value="cash">Cash</option><option value="bank_transfer">Bank transfer</option>
                            <option value="card">Card</option><option value="cheque">Cheque</option>
                        </select></div>
                    <div class="col-5"><label class="form-label small mb-1">Amount</label>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm js-amount" name="${n}[amount]" value="${data.amount || ''}"></div>
                    <div class="col-2 text-end align-self-end"><button type="button" class="btn btn-sm btn-soft-danger js-del" title="Remove"><iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon></button></div>
                    <div class="col-12"><label class="form-label small mb-1 js-acc-label">Account</label>
                        <select class="form-select form-select-sm js-account" name="${n}[account_id]"></select></div>
                    <div class="col-12 js-ref-wrap"><input type="text" class="form-control form-control-sm" name="${n}[reference]" placeholder="Reference (slip no, card last 4 digits...)" value="${esc(data.reference || '')}"></div>
                    <div class="col-12 js-cheque d-none"><div class="row g-2">
                        <div class="col-6 col-md-4"><input type="text" class="form-control form-control-sm" name="${n}[cheque_no]" placeholder="Cheque no"></div>
                        <div class="col-6 col-md-4"><input type="text" class="form-control form-control-sm" name="${n}[bank]" placeholder="Bank"></div>
                        <div class="col-12 col-md-4"><input type="date" class="form-control form-control-sm" name="${n}[cheque_date]" title="Cheque date (post-dated allowed)"></div>
                    </div></div>
                </div>
            </div>`);
        const sel = row.querySelector('.js-method');
        sel.value = method;
        const sync = () => {
            const m = sel.value;
            row.querySelector('.js-account').innerHTML = this.accountOptions(m, data.account_id);
            row.querySelector('.js-cheque').classList.toggle('d-none', m !== 'cheque');
            row.querySelector('.js-ref-wrap').classList.toggle('d-none', m === 'cheque');
            row.querySelector('.js-acc-label').textContent = m === 'cheque' ? 'Bank account (for clearing)' : 'Account';
        };
        sel.addEventListener('change', sync);
        sync();
        row.querySelector('.js-amount').addEventListener('input', () => (row.dataset.touched = '1'));
        row.addEventListener('input', () => this.update());
        row.querySelector('.js-del').addEventListener('click', () => {
            row.remove();
            this.update();
        });
        this.body.appendChild(row);
        if (!data.amount && this.target) {
            const remaining = Math.max(0, this.target - this.paid());
            row.querySelector('.js-amount').value = remaining ? remaining.toFixed(2) : '';
        }
        this.update();
    }

    paid() {
        return [...this.body.querySelectorAll('.js-amount')].reduce((s, i) => s + num(i.value), 0);
    }

    update() {
        const paid = this.paid();
        const set = (sel, v) => this.root.querySelectorAll(sel).forEach((e) => (e.textContent = v));
        set('.js-paid', fmt(paid));
        set('.js-balance', fmt(Math.max(0, (this.target || 0) - paid)));
        document.dispatchEvent(new CustomEvent('erp:paid', { detail: { paid } }));
    }
}

// ---------- Allocation table (receipts / supplier payments) ----------
function initAllocations() {
    const box = document.querySelector('[data-allocations]');
    if (!box) return;
    const recalc = () => {
        const total = [...box.querySelectorAll('.js-alloc')].reduce((s, i) => s + num(i.value), 0);
        box.querySelectorAll('.js-alloc-total').forEach((e) => (e.textContent = fmt(total)));
    };
    box.addEventListener('input', recalc);
    document.addEventListener('erp:paid', (e) => {
        if (!box.querySelector('.js-auto')?.checked) return;
        let left = e.detail.paid;
        box.querySelectorAll('.js-alloc').forEach((inp) => {
            const due = num(inp.dataset.due);
            const take = Math.min(due, left);
            inp.value = take > 0 ? take.toFixed(2) : '';
            left -= take;
        });
        recalc();
    });
    recalc();
}

document.addEventListener('DOMContentLoaded', () => {
    initChoices();
    initConfirms();
    document.querySelectorAll('[data-line-editor]').forEach((r) => (r._editor = new LineEditor(r)));
    document.querySelectorAll('[data-payment-lines]').forEach((r) => (r._pay = new PaymentLines(r)));
    initAllocations();
    initQuickAdd();

    // Keep the sidebar section open for the current page
    const here = window.location.pathname;
    document.querySelectorAll('#navbar-nav a[href]').forEach((a) => {
        try {
            const path = new URL(a.href).pathname;
            if (path !== '/' && (here === path || here.startsWith(path + '/'))) {
                a.classList.add('active');
                const collapse = a.closest('.collapse');
                if (collapse) {
                    collapse.classList.add('show');
                    collapse.previousElementSibling?.setAttribute('aria-expanded', 'true');
                }
            }
        } catch (e) {}
    });
});

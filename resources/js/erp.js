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

// ---------- Size grid line editor ----------
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
        this.currency = root.dataset.currency || 'Rs';
        this.priceLabel = root.dataset.priceLabel || 'Unit price';

        const picker = root.querySelector('.product-picker');
        picker.innerHTML = '<option value="">Search product by name, code or brand...</option>' +
            this.products.map((p) => `<option value="${p.id}">${esc(p.code)} - ${esc(p.brand || '')} ${esc(p.name)}</option>`).join('');
        this.choices = new Choices(picker, {
            searchEnabled: true,
            shouldSort: false,
            itemSelectText: '',
            searchResultLimit: 40,
            placeholder: true,
            searchPlaceholderValue: 'Type code, model, colour or brand',
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

    priceFor(p) {
        if (this.mode === 'purchase') return p.last_fx_price || 0;
        if (this.mode === 'stock') return p.cost;
        if (this.groupId && p.group_prices && p.group_prices[this.groupId] !== undefined) return p.group_prices[this.groupId];
        return p.price;
    }

    addLine(data) {
        const p = this.byId[data.product_id];
        if (!p) return;
        const existing = this.lines.querySelector(`.line-card[data-product="${p.id}"]`);
        if (existing && !data.qty) {
            existing.scrollIntoView({ behavior: 'smooth', block: 'center' });
            existing.classList.add('border-primary');
            setTimeout(() => existing.classList.remove('border-primary'), 1200);
            return;
        }
        const i = this.counter++;
        const name = `lines[${i}]`;
        const price = data.unit_price ?? this.priceFor(p);
        const showPrice = this.mode !== 'stock';
        const salesFields = this.mode === 'sales' ? `
            <div><label class="form-label small mb-1">Discount %</label>
                <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm js-disc" name="${name}[discount_percent]" value="${data.discount_percent ?? 0}"></div>` : '';
        const card = el(`
            <div class="line-card" data-product="${p.id}">
                <input type="hidden" name="${name}[product_id]" value="${p.id}">
                <div class="line-head">
                    <div>
                        <div class="line-title">${esc(p.name)}</div>
                        <div class="line-meta">${esc(p.code)} &middot; ${esc(p.brand || '')} &middot; ${p.ppc} pairs/carton
                            ${this.mode === 'sales' ? `&middot; Min ${this.currency} ${fmt(p.min_price)}` : ''}
                            ${this.mode !== 'purchase' ? `&middot; Cost ${this.currency} ${fmt(p.cost)}` : ''}</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-soft-danger js-remove" title="Remove"><iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon></button>
                </div>
                <div class="line-fields">
                    ${showPrice ? `<div><label class="form-label small mb-1">${esc(this.priceLabel)}</label>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm js-price" name="${name}[unit_price]" value="${price}"></div>` : ''}
                    ${salesFields}
                    <div><label class="form-label small mb-1">Quick fill (cartons)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" min="0" step="0.5" class="form-control js-cartons" placeholder="0">
                            <button type="button" class="btn btn-light js-fill">Fill</button>
                        </div></div>
                </div>
                <div class="size-grid">
                    ${p.variants.map((v) => `
                        <div class="size-cell" data-variant="${v.id}">
                            <label>${esc(v.size)}</label>
                            <input type="number" min="0" inputmode="numeric" class="form-control form-control-sm js-qty"
                                name="${name}[qty][${v.id}]" value="${(data.qty && data.qty[v.id]) || ''}" placeholder="0">
                            <small class="js-stock"></small>
                        </div>`).join('')}
                </div>
                <div class="line-foot">
                    <span><strong class="js-pairs">0</strong> pairs <span class="text-muted js-ctn"></span></span>
                    ${showPrice ? `<span>Line total: <strong class="js-total money">0.00</strong></span>` : ''}
                </div>
                <div class="js-warn small text-danger mt-1"></div>
            </div>`);
        this.lines.appendChild(card);
        card.addEventListener('input', () => this.updateTotals());
        card.querySelector('.js-remove').addEventListener('click', () => {
            card.remove();
            this.updateTotals();
        });
        card.querySelector('.js-fill').addEventListener('click', () => this.fillCartons(card));
        this.renderStock(card);
        this.updateTotals();
        this.root.querySelector('.empty-lines')?.classList.add('d-none');
    }

    fillCartons(card) {
        const p = this.byId[card.dataset.product];
        const cartons = num(card.querySelector('.js-cartons').value);
        const inputs = [...card.querySelectorAll('.js-qty')];
        const pairs = Math.round(cartons * p.ppc);
        inputs.forEach((inp, i) => {
            inp.value = Math.floor(pairs / inputs.length) + (i < pairs % inputs.length ? 1 : 0) || '';
        });
        this.updateTotals();
    }

    renderStock(card) {
        if (this.mode === 'purchase') return;
        const p = this.byId[card.dataset.product];
        card.querySelectorAll('.size-cell').forEach((cell) => {
            const v = p.variants.find((x) => x.id == cell.dataset.variant);
            const stock = v && v.stock ? (v.stock[this.warehouseId] || 0) : 0;
            const small = cell.querySelector('.js-stock');
            small.textContent = `${stock} in stock`;
            small.classList.toggle('low', stock <= 0);
            cell.dataset.stock = stock;
        });
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
            const price = num(card.querySelector('.js-price')?.value);
            const disc = num(card.querySelector('.js-disc')?.value);
            let linePairs = 0;
            const warn = [];
            card.querySelectorAll('.size-cell').forEach((cell) => {
                const q = num(cell.querySelector('.js-qty').value);
                linePairs += q;
                if (this.mode !== 'purchase' && this.root.dataset.checkStock === '1' && q > num(cell.dataset.stock)) {
                    warn.push(`size ${cell.querySelector('label').textContent}`);
                }
            });
            const netPrice = price * (1 - disc / 100);
            const lineNet = linePairs * netPrice;
            card.querySelector('.js-pairs').textContent = linePairs;
            card.querySelector('.js-ctn').textContent = linePairs ? `(${(linePairs / p.ppc).toFixed(1)} ctn)` : '';
            const totalEl = card.querySelector('.js-total');
            if (totalEl) totalEl.textContent = fmt(lineNet);
            const msgs = [];
            if (warn.length) msgs.push(`Not enough stock for ${warn.join(', ')}.`);
            if (this.mode === 'sales' && price > 0 && netPrice < p.min_price) msgs.push(`Price is below the minimum selling price (${fmt(p.min_price)}).`);
            if (this.mode === 'sales' && disc > num(this.root.dataset.maxDiscount) && this.root.dataset.manager !== '1') msgs.push(`Discount is above the allowed ${this.root.dataset.maxDiscount}%.`);
            card.querySelector('.js-warn').textContent = msgs.join(' ');
            pairs += linePairs;
            gross += linePairs * price;
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

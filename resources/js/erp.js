/**
 * FM Shoes - front-end behaviour:
 * searchable selects, product picker with pairs per colour, split payments,
 * photo preview, colour rows, quick-add pop-ups, share / copy buttons.
 */
import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css';
import { PosSell } from './pos.js';

const fmt = (n) => new Intl.NumberFormat('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n || 0);
const num = (v) => parseFloat(v) || 0;
const el = (html) => {
    const t = document.createElement('template');
    t.innerHTML = html.trim();
    return t.content.firstElementChild;
};
const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

const COLOURS = {
    black: '#111827', white: '#ffffff', brown: '#7c4a1e', tan: '#d2a26b', blue: '#2563eb', navy: '#1e3a8a', grey: '#9ca3af',
    gray: '#9ca3af', red: '#dc2626', maroon: '#7f1d1d', green: '#16a34a', olive: '#65743a', beige: '#e8d9b5', cream: '#f5f0dc',
    pink: '#ec4899', purple: '#7c3aed', yellow: '#facc15', orange: '#f97316', gold: '#d4a017', silver: '#c0c0c0', khaki: '#c3b091',
    camel: '#c19a6b', coffee: '#6f4e37', wine: '#722f37', peach: '#ffcba4',
};
const colourHex = (name) => {
    const key = String(name || '').trim().toLowerCase();
    for (const [word, hex] of Object.entries(COLOURS)) if (key === word || key.includes(word)) return hex;
    return '#cbd5e1';
};

window.erpFmt = fmt;

// ---------- Searchable selects ----------
function initChoices(root = document) {
    root.querySelectorAll('select[data-choices]:not(.choices__input)').forEach((select) => {
        // Choices removes the <option>s, so keep their data-* first
        select._meta = {};
        [...select.options].forEach((o) => (select._meta[o.value] = { ...o.dataset }));
        select._choices = new Choices(select, {
            searchEnabled: true,
            shouldSort: false,
            itemSelectText: '',
            searchResultLimit: 30,
            fuseOptions: { threshold: 0.3, ignoreLocation: true },
            allowHTML: false,
            placeholder: true,
            placeholderValue: select.dataset.placeholder || 'Select...',
            noResultsText: 'Not found - use + New',
        });
    });
}

// Warn when the chosen customer already owes money
function initDueWarning() {
    document.querySelectorAll('select[data-due-warning]').forEach((select) => {
        const box = document.querySelector(select.dataset.dueWarning);
        const check = () => {
            const due = num(select._meta?.[select.value]?.due);
            box.classList.toggle('d-none', due <= 0);
            box.querySelector('.js-due').textContent = 'Rs ' + fmt(due);
        };
        select.addEventListener('change', check);
        check();
    });
}

// ---------- Confirm before submitting ----------
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
            window.Swal.fire({ title: 'Are you sure?', text: msg, icon: 'question', showCancelButton: true, confirmButtonText: 'Yes', confirmButtonColor: '#e31e24' })
                .then((r) => r.isConfirmed && go());
        } else if (confirm(msg)) go();
    });

    // Stop double submits on big forms
    document.querySelectorAll('form.doc-form').forEach((f) =>
        f.addEventListener('submit', () => setTimeout(() => f.querySelectorAll('button:not([type=button])').forEach((b) => (b.disabled = true)), 0)),
    );
}

// ---------- Small helpers on forms ----------
function initFormHelpers() {
    // Photo preview
    document.querySelectorAll('input[data-photo-preview]').forEach((input) =>
        input.addEventListener('change', () => {
            const img = document.querySelector(input.dataset.photoPreview);
            const file = input.files[0];
            if (!file || !img) return;
            img.src = URL.createObjectURL(file);
            img.classList.remove('d-none');
            img.parentElement.querySelector('.photo-hint')?.classList.add('d-none');
        }),
    );

    // Dropdown with "+ New" that turns into a text box
    document.addEventListener('click', (e) => {
        const add = e.target.closest('.js-add-new');
        const cancel = e.target.closest('.js-cancel-new');
        if (add) {
            const box = add.closest('.pick-or-add');
            box.classList.add('adding');
            box.querySelector('.js-new input')?.focus();
        }
        if (cancel) {
            const box = cancel.closest('.pick-or-add');
            box.classList.remove('adding');
            box.querySelector('.js-new input').value = '';
        }
    });

    // Auto product code
    document.querySelectorAll('.js-gen-code').forEach((b) =>
        b.addEventListener('click', async () => {
            const r = await fetch(b.dataset.url, { headers: { Accept: 'application/json' } });
            b.closest('.input-group').querySelector('input').value = (await r.json()).code;
        }),
    );

    // Copy link
    document.querySelectorAll('[data-copy]').forEach((b) =>
        b.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(b.dataset.copy);
            } catch (e) {
                window.prompt('Copy this link:', b.dataset.copy);
                return;
            }
            const label = b.querySelector('.js-copy-label');
            if (label) {
                label.textContent = 'Copied!';
                setTimeout(() => (label.textContent = 'Copy link'), 2000);
            }
        }),
    );

    // Phone share sheet (shown only where supported)
    document.querySelectorAll('.js-native-share').forEach((b) => {
        if (!navigator.share) return;
        b.classList.remove('d-none');
        b.addEventListener('click', () => navigator.share({ title: b.dataset.shareTitle, text: b.dataset.shareText }).catch(() => {}));
    });

    // Correct-stock pop-up on the product page
    document.getElementById('correct-stock')?.addEventListener('show.bs.modal', (e) => {
        const btn = e.relatedTarget;
        const m = e.target;
        m.querySelector('.js-cs-id').value = btn.dataset.colourId;
        m.querySelector('.js-cs-name').textContent = btn.dataset.colourName;
        m.querySelector('.js-cs-qty').textContent = btn.dataset.colourQty;
        m.querySelector('.js-cs-count').value = btn.dataset.colourQty;
        setTimeout(() => m.querySelector('.js-cs-count').select(), 300);
    });
}

// ---------- Product form: colour rows with opening pairs ----------
function initColourRows() {
    const box = document.querySelector('[data-colour-rows]');
    if (!box) return;
    const rows = box.querySelector('.js-rows');
    const tpl = box.querySelector('.js-row-template').innerHTML;
    let i = rows.children.length + 100;

    const total = () => {
        let t = 0;
        rows.querySelectorAll('.js-qty').forEach((q) => (t += num(q.dataset.qty)));
        rows.querySelectorAll('.js-qty-input').forEach((q) => (t += num(q.value)));
        box.querySelector('.js-colour-total').textContent = t;
        const hasNewPairs = [...rows.querySelectorAll('.js-qty-input')].some((q) => num(q.value) > 0);
        const cost = box.querySelector('.js-opening-cost');
        if (cost && hasNewPairs) cost.classList.remove('d-none');
    };
    const paint = (row) => {
        const name = row.querySelector('.js-name').value;
        row.querySelector('.dot').style.background = colourHex(name);
    };
    const add = (name = '') => {
        const existing = [...rows.querySelectorAll('.js-name')].find((n) => n.value.trim().toLowerCase() === name.toLowerCase() && name);
        if (existing) {
            (existing.closest('.colour-row').querySelector('.js-qty-input') || existing).focus();
            return;
        }
        // Reuse an empty row before adding another one
        const blank = name && [...rows.querySelectorAll('.colour-row:not([data-existing])')].find((r) => !r.querySelector('.js-name').value.trim());
        const row = blank || el(tpl.replaceAll('__i', i++));
        row.querySelector('.js-name').value = name;
        if (!blank) rows.appendChild(row);
        paint(row);
        (name ? row.querySelector('.js-qty-input') : row.querySelector('.js-name')).focus();
        total();
    };

    box.querySelector('.js-add-row').addEventListener('click', () => add());
    box.querySelectorAll('[data-add-colour]').forEach((b) => b.addEventListener('click', () => add(b.dataset.addColour)));
    rows.addEventListener('input', (e) => {
        const row = e.target.closest('.colour-row');
        if (e.target.classList.contains('js-name')) paint(row);
        total();
    });
    rows.addEventListener('click', (e) => {
        if (e.target.closest('.js-remove')) {
            e.target.closest('.colour-row').remove();
            total();
        }
    });
    rows.addEventListener('change', (e) => {
        if (e.target.classList.contains('js-del-check')) {
            e.target.closest('.colour-row').classList.toggle('removing', e.target.checked);
        }
    });
    if (!rows.children.length) add();
    total();
}

// ---------- Buy / Sell: product picker with pairs per colour ----------
class ItemEditor {
    constructor(root) {
        this.root = root;
        this.mode = root.dataset.mode; // buy | sell
        this.products = JSON.parse(root.dataset.products || '[]');
        this.lines = root.querySelector('.js-lines');
        this.search = root.querySelector('.js-search');
        this.results = root.querySelector('.js-results');
        this.i = 0;

        this.search.addEventListener('input', () => this.showResults());
        this.search.addEventListener('focus', () => this.showResults());
        this.search.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.results.querySelector('.item-result')?.click();
            }
            if (e.key === 'Escape') this.hideResults();
        });
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.item-search')) this.hideResults();
        });
        this.results.addEventListener('click', (e) => {
            const r = e.target.closest('.item-result');
            if (!r) return;
            this.add(this.products.find((p) => p.id == r.dataset.id));
            this.search.value = '';
            this.hideResults();
        });
        root.addEventListener('input', () => this.recalc());
        root.addEventListener('click', (e) => this.onClick(e));

        document.addEventListener('erp:product-created', (e) => {
            this.products.push(e.detail.product);
            this.add(e.detail.product);
        });

        JSON.parse(root.dataset.initial || '[]').forEach((it) => {
            const p = this.products.find((x) => x.id == it.product_id);
            if (p) this.add(p, it);
        });
        const start = root.dataset.start && this.products.find((p) => p.id == root.dataset.start);
        if (start && !this.lines.children.length) this.add(start);
        this.recalc();
    }

    stockOf(p) {
        return p.colours.reduce((s, c) => s + c.qty, 0);
    }

    showResults() {
        const q = this.search.value.trim().toLowerCase();
        const words = q.split(/\s+/).filter(Boolean);
        const list = this.products
            .filter((p) => {
                const hay = [p.name, p.code, p.brand, p.size, ...p.colours.map((c) => c.name)].join(' ').toLowerCase();
                return words.every((w) => hay.includes(w));
            })
            .slice(0, q ? 12 : 8);
        if (!list.length) {
            this.results.innerHTML = `<div class="item-empty">Not found. <a href="#" data-quick-add="product">Add "${esc(this.search.value)}" as a new product</a></div>`;
        } else {
            this.results.innerHTML = list
                .map((p) => {
                    const stock = this.stockOf(p);
                    const img = p.image ? `<img src="${esc(p.image)}" alt="">` : `<span class="thumb-empty"><iconify-icon icon="ph:sneaker"></iconify-icon></span>`;
                    return `<button type="button" class="item-result" data-id="${p.id}">
                        ${img}
                        <span class="flex-grow-1 min-w-0"><b class="d-block text-truncate">${esc(p.name)}</b>
                            <small class="text-muted">${esc(p.code)}${p.size ? ' · Size ' + esc(p.size) : ''}</small></span>
                        <span class="text-end small"><span class="d-block fw-semibold">Rs ${fmt(this.mode === 'buy' ? p.cost : p.price)}</span>
                            <span class="${stock <= 0 ? 'text-danger' : 'text-muted'}">${stock} pairs</span></span>
                    </button>`;
                })
                .join('');
        }
        this.results.classList.add('show');
    }

    hideResults() {
        this.results.classList.remove('show');
    }

    add(p, init = null) {
        if (!p) return;
        const existing = this.lines.querySelector(`.item-line[data-product="${p.id}"]`);
        if (existing && !init) {
            existing.scrollIntoView({ behavior: 'smooth', block: 'center' });
            existing.querySelector('.js-cq')?.focus();
            existing.classList.add('flash');
            setTimeout(() => existing.classList.remove('flash'), 900);
            return;
        }
        const i = this.i++;
        const n = `items[${i}]`;
        const price = init ? init.price : this.mode === 'buy' ? p.cost || '' : p.price || '';
        const img = p.image ? `<img src="${esc(p.image)}" class="thumb" style="width:52px;height:52px" alt="">` : `<span class="thumb thumb-empty" style="width:52px;height:52px"><iconify-icon icon="ph:sneaker"></iconify-icon></span>`;

        const line = el(`
            <div class="item-line" data-product="${p.id}">
                <input type="hidden" name="${n}[product_id]" value="${p.id}">
                <div class="item-head">
                    ${img}
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold text-truncate">${esc(p.name)}</div>
                        <div class="small text-muted">${esc(p.code)}${p.size ? ' · Size ' + esc(p.size) : ''}</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-soft-danger js-remove-line" title="Remove"><iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon></button>
                </div>
                <div class="cq-label small text-muted mb-1">${this.mode === 'buy' ? 'Pairs per colour' : 'Pairs per colour (in stock shown below)'}</div>
                <div class="cq-grid js-cq-grid"></div>
                <div class="item-foot">
                    <label class="price-box"><span>Price / pair</span>
                        <input type="number" step="0.01" min="0" name="${n}[price]" value="${price}" class="form-control js-price" inputmode="decimal" required></label>
                    <div class="text-end"><div class="small text-muted"><span class="js-line-pairs">0</span> pairs</div><strong class="js-line-total money">Rs 0.00</strong></div>
                </div>
            </div>`);
        line.dataset.i = i;
        line._j = 0;
        const grid = line.querySelector('.js-cq-grid');

        if (this.mode === 'sell') {
            if (!p.colours.length) grid.innerHTML = '<div class="text-danger small">No colours / stock for this product. Buy stock first.</div>';
            p.colours.forEach((c) => {
                const q = init?.colours?.[c.id] ?? '';
                grid.appendChild(
                    el(`<label class="cq ${c.qty <= 0 ? 'cq-out' : ''}">
                        <span class="cq-name"><i style="background:${c.hex}"></i>${esc(c.name)}</span>
                        <input type="number" min="0" max="${Math.max(0, c.qty)}" name="${n}[colours][${c.id}]" value="${q}" class="form-control js-cq" inputmode="numeric" placeholder="0" ${c.qty <= 0 ? 'disabled' : ''}>
                        <small class="${c.qty <= 0 ? 'text-danger' : ''}">${c.qty <= 0 ? 'none left' : c.qty + ' in stock'}</small>
                    </label>`),
                );
            });
        } else {
            const initRows = init?.colours ? Object.values(init.colours) : [];
            p.colours.forEach((c) => {
                const prev = initRows.find((r) => r.id == c.id);
                this.addBuyColour(line, { id: c.id, name: c.name, hex: c.hex, qty: prev?.qty ?? '', stock: c.qty });
            });
            initRows.filter((r) => !r.id && r.name).forEach((r) => this.addBuyColour(line, { name: r.name, qty: r.qty, isNew: true }));
            grid.appendChild(el(`<button type="button" class="cq cq-add js-add-colour"><iconify-icon icon="solar:add-circle-outline"></iconify-icon><span>New colour</span></button>`));
            if (!p.colours.length && !initRows.length) this.addBuyColour(line, { name: '', qty: '', isNew: true });
        }

        this.lines.prepend(line);
        this.root.querySelector('.empty-lines').classList.add('d-none');
        if (!init) line.querySelector('.js-cq:not([disabled])')?.focus();
        this.recalc();
    }

    addBuyColour(line, c) {
        const n = `items[${line.dataset.i}][colours][${line._j++}]`;
        const grid = line.querySelector('.js-cq-grid');
        const box = el(`<label class="cq ${c.isNew ? 'cq-new' : ''}">
            ${c.isNew
                ? `<input type="text" name="${n}[name]" value="${esc(c.name)}" class="form-control form-control-sm js-new-colour" placeholder="Colour name" list="colour-names">`
                : `<input type="hidden" name="${n}[id]" value="${c.id}"><span class="cq-name"><i style="background:${c.hex}"></i>${esc(c.name)}</span>`}
            <input type="number" min="0" name="${n}[qty]" value="${c.qty}" class="form-control js-cq" inputmode="numeric" placeholder="0">
            <small>${c.isNew ? 'new colour' : c.stock + ' in stock'}</small>
        </label>`);
        const addBtn = grid.querySelector('.js-add-colour');
        addBtn ? grid.insertBefore(box, addBtn) : grid.appendChild(box);
        return box;
    }

    onClick(e) {
        if (e.target.closest('.js-remove-line')) {
            e.target.closest('.item-line').remove();
            this.root.querySelector('.empty-lines').classList.toggle('d-none', this.lines.children.length > 0);
            this.recalc();
        }
        const addColour = e.target.closest('.js-add-colour');
        if (addColour) {
            e.preventDefault();
            const box = this.addBuyColour(addColour.closest('.item-line'), { name: '', qty: '', isNew: true });
            box.querySelector('.js-new-colour').focus();
        }
    }

    recalc() {
        let pairs = 0;
        let amount = 0;
        this.lines.querySelectorAll('.item-line').forEach((line) => {
            let lp = 0;
            line.querySelectorAll('.js-cq').forEach((inp) => {
                const q = Math.max(0, parseInt(inp.value) || 0);
                const max = inp.hasAttribute('max') ? parseInt(inp.max) : Infinity;
                inp.classList.toggle('is-invalid', q > max);
                lp += q;
            });
            const lt = lp * num(line.querySelector('.js-price').value);
            line.querySelector('.js-line-pairs').textContent = lp;
            line.querySelector('.js-line-total').textContent = 'Rs ' + fmt(lt);
            pairs += lp;
            amount += lt;
        });
        const discount = num(this.root.querySelector('.js-discount')?.value);
        const other = num(this.root.querySelector('.js-other')?.value);
        const total = Math.max(0, amount - discount + other);
        this.root.querySelector('.js-pairs').textContent = pairs;
        this.root.querySelector('.js-subtotal').textContent = fmt(amount);
        this.root.querySelector('.js-total').textContent = fmt(total);
        this.root.dataset.total = total;
        document.dispatchEvent(new CustomEvent('erp:total', { detail: { total } }));
    }
}

// ---------- Split payment: cash + card + online + cheque ----------
const METHODS = {
    cash: ['Cash', 'solar:wad-of-money-outline'],
    card: ['Card', 'solar:card-outline'],
    online: ['Online / Bank', 'solar:smartphone-2-outline'],
    cheque: ['Cheque', 'solar:document-text-outline'],
};

class PaymentLines {
    constructor(root) {
        this.root = root;
        this.body = root.querySelector('.pay-lines');
        this.i = 0;
        this.fixed = root.dataset.target !== '';
        this.target = num(this.fixed ? root.dataset.target : document.querySelector('[data-items], [data-pos]')?.dataset.total);
        this.balanceLabel = root.querySelector('.js-balance-row span').textContent;

        root.querySelectorAll('[data-add-method]').forEach((b) => b.addEventListener('click', () => this.add({ method: b.dataset.addMethod })));
        root.querySelector('.js-pay-full').addEventListener('click', () => {
            this.body.innerHTML = '';
            this.add({ method: 'cash', amount: this.target.toFixed(2), follow: true });
        });
        root.querySelector('.js-pay-none').addEventListener('click', () => {
            this.body.innerHTML = '';
            this.update();
        });
        document.addEventListener('erp:total', (e) => {
            if (this.fixed) return;
            this.target = e.detail.total;
            const follow = this.body.querySelector('.pay-row[data-follow]');
            if (follow && this.body.children.length === 1) follow.querySelector('.js-amount').value = this.target ? this.target.toFixed(2) : '';
            this.update();
        });

        const initial = JSON.parse(root.dataset.initial || '[]');
        initial.forEach((l) => this.add(l));
        if (!initial.length && root.dataset.startEmpty !== '1' && this.target > 0) this.add({ method: 'cash', amount: this.target.toFixed(2), follow: true });
        this.update();
    }

    add(data = {}) {
        const i = this.i++;
        const n = `payments[${i}]`;
        const m = METHODS[data.method] ? data.method : 'cash';
        const amount = data.amount ?? (Math.max(0, this.target - this.paid()) || '');
        const row = el(`
            <div class="pay-row" data-method="${m}">
                <input type="hidden" name="${n}[method]" value="${m}">
                <div class="pay-main">
                    <span class="pay-method"><iconify-icon icon="${METHODS[m][1]}"></iconify-icon>${METHODS[m][0]}</span>
                    <div class="input-group">
                        <span class="input-group-text">Rs</span>
                        <input type="number" step="0.01" min="0" class="form-control js-amount" name="${n}[amount]" value="${amount === '' ? '' : num(amount).toFixed(2)}" inputmode="decimal" placeholder="0.00">
                    </div>
                    <button type="button" class="btn btn-sm btn-light js-del" title="Remove"><iconify-icon icon="solar:close-circle-outline" class="fs-18"></iconify-icon></button>
                </div>
                ${m === 'cheque'
                    ? `<div class="pay-extra row g-2">
                        <div class="col-4"><input type="text" class="form-control form-control-sm" name="${n}[cheque_no]" value="${esc(data.cheque_no || '')}" placeholder="Cheque no *" required></div>
                        <div class="col-4"><input type="text" class="form-control form-control-sm" name="${n}[bank]" value="${esc(data.bank || '')}" placeholder="Bank"></div>
                        <div class="col-4"><input type="date" class="form-control form-control-sm" name="${n}[cheque_date]" value="${esc(data.cheque_date || '')}" title="Cheque date"></div>
                      </div>`
                    : m !== 'cash'
                      ? `<div class="pay-extra"><input type="text" class="form-control form-control-sm" name="${n}[reference]" value="${esc(data.reference || '')}" placeholder="Reference (optional)"></div>`
                      : ''}
            </div>`);
        if (data.follow) row.dataset.follow = '1';
        row.querySelector('.js-amount').addEventListener('input', () => {
            delete row.dataset.follow;
            this.update();
        });
        row.querySelector('.js-del').addEventListener('click', () => {
            row.remove();
            this.update();
        });
        this.body.appendChild(row);
        if (!data.amount) row.querySelector('.js-amount').select();
        this.update();
    }

    paid() {
        return [...this.body.querySelectorAll('.js-amount')].reduce((s, i) => s + num(i.value), 0);
    }

    update() {
        const paid = this.paid();
        const balance = this.target - paid;
        this.root.querySelector('.js-paid').textContent = fmt(paid);
        const row = this.root.querySelector('.js-balance-row');
        row.querySelector('.js-balance').textContent = fmt(Math.abs(balance));
        row.querySelector('span').textContent = balance < -0.009 ? (this.fixed ? 'More than owed by' : 'Extra (goes to older bills)') : this.balanceLabel;
        row.querySelector('strong').className = 'money ' + (balance < -0.009 ? 'text-warning' : balance > 0.009 ? 'text-danger' : 'text-success');
        this.root.classList.toggle('not-paid', !this.body.children.length);
    }
}

// ---------- Quick add pop-ups ----------
function addOption(select, item) {
    select._meta = select._meta || {};
    select._meta[String(item.id)] = item.data || {};
    if (select._choices) {
        select._choices.setChoices([{ value: String(item.id), label: item.label, selected: true }], 'value', 'label', false);
    } else {
        select.add(new Option(item.label, item.id, true, true));
        select.value = String(item.id);
    }
    select.dispatchEvent(new Event('change', { bubbles: true }));
}

function initQuickAdd() {
    let target = null;
    let prefill = '';

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-quick-add]');
        if (!btn) return;
        e.preventDefault();
        const modal = document.getElementById('qa-' + btn.dataset.quickAdd);
        if (!modal) return;
        target = btn.dataset.target ? document.querySelector(btn.dataset.target) : null;
        prefill = btn.closest('.item-search')?.querySelector('.js-search')?.value || '';
        const form = modal.querySelector('form');
        form.reset();
        form.querySelector('.js-errors').classList.add('d-none');
        if (prefill) form.querySelector('input[name=name]').value = prefill;
        window.bootstrap.Modal.getOrCreateInstance(modal).show();
        setTimeout(() => form.querySelector('input[name=name]')?.focus(), 300);
    });

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
                if (form.closest('.modal').id === 'qa-product') {
                    document.dispatchEvent(new CustomEvent('erp:product-created', { detail: { product: json } }));
                    document.querySelectorAll('.js-search').forEach((s) => (s.value = ''));
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

// ---------- Check price & stock (top bar, any page) ----------
function initLookup() {
    const box = document.querySelector('[data-lookup]');
    if (!box) return;
    const modal = box.closest('.modal');
    const input = box.querySelector('.js-lookup-q');
    const out = box.querySelector('.js-lookup-results');
    const hint = out.innerHTML;
    let timer = null;
    let seq = 0;

    const card = (p) => {
        const status = p.stock <= 0 ? ['out', 'Out of stock'] : p.low ? ['low', 'Low stock'] : ['ok', 'In stock'];
        const max = Math.max(1, ...p.colours.map((c) => c.qty));
        return `<div class="lookup-card">
            <div class="lookup-img">${p.image ? `<img src="${esc(p.image)}" alt="">` : '<iconify-icon icon="ph:sneaker"></iconify-icon>'}</div>
            <div class="lookup-info">
                <div class="d-flex justify-content-between gap-2 flex-wrap">
                    <div class="min-w-0"><span class="lookup-code">${esc(p.code)}</span><h5 class="mb-0">${esc(p.name)}</h5>
                        <div class="small text-muted">${[p.brand, p.category].filter(Boolean).map(esc).join(' · ') || '&nbsp;'}</div></div>
                    <div class="text-end"><div class="lookup-price">Rs ${fmt(p.price)}</div><div class="small text-muted">per pair</div></div>
                </div>
                <div class="lookup-facts">
                    ${p.size ? `<span><iconify-icon icon="solar:ruler-outline"></iconify-icon>Size ${esc(p.size)}</span>` : ''}
                    <span class="st-${status[0]}"><iconify-icon icon="solar:box-outline"></iconify-icon>${status[1]} · <b>${p.stock}</b> pairs</span>
                </div>
                <div class="lookup-colours">${p.colours.map((c) => `
                    <div class="lc ${c.qty <= 0 ? 'empty' : ''}"><span class="lc-name"><i style="background:${c.hex}"></i>${esc(c.name)}</span>
                        <span class="lc-bar"><span style="width:${Math.round((Math.max(0, c.qty) / max) * 100)}%"></span></span><b>${c.qty}</b></div>`).join('') || '<div class="small text-muted">No colours yet</div>'}
                </div>
                <div class="d-flex gap-2 mt-2">
                    <a href="${p.sell_url}" class="btn btn-sm btn-brand ${p.stock <= 0 ? 'disabled' : ''}"><iconify-icon icon="solar:cart-large-2-outline" class="align-middle me-1"></iconify-icon>Sell</a>
                    <a href="${p.url}" class="btn btn-sm btn-light">Full details</a>
                </div>
            </div>
        </div>`;
    };

    const search = async () => {
        const q = input.value.trim();
        if (!q) {
            out.innerHTML = hint;
            return;
        }
        const my = ++seq;
        try {
            const r = await fetch(box.dataset.url + '?q=' + encodeURIComponent(q), { headers: { Accept: 'application/json' } });
            const list = await r.json();
            if (my !== seq) return;
            out.innerHTML = list.length ? list.map(card).join('') : `<div class="lookup-hint"><iconify-icon icon="solar:sad-circle-outline"></iconify-icon><div>No product found for "<b>${esc(q)}</b>"</div></div>`;
        } catch (e) {
            out.innerHTML = '<div class="lookup-hint">Could not search. Check the connection.</div>';
        }
    };

    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(search, 180);
    });
    modal.addEventListener('shown.bs.modal', () => {
        input.focus();
        input.select();
    });
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            window.bootstrap.Modal.getOrCreateInstance(modal).show();
        }
    });
}


// ---------- Phone app: service worker + "Install app" ----------
function initApp() {
    if ('serviceWorker' in navigator && (location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1')) {
        window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js').catch(() => {}));
    }

    const standalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    if (standalone) {
        document.documentElement.classList.add('is-app');
        return;
    }

    const buttons = document.querySelectorAll('[data-install-app]');
    const card = document.querySelector('[data-install-card]');
    let dismissed = false;
    try { dismissed = localStorage.getItem('fm-install-dismissed') === '1'; } catch (e) { /* private mode */ }
    const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    const isPhone = window.matchMedia('(max-width: 991.98px)').matches;
    let deferred = null;

    const show = () => {
        buttons.forEach((b) => b.classList.remove('d-none'));
        if (card && !dismissed && isPhone) card.classList.remove('d-none');
    };

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferred = e;
        show();
    });
    if (isIos) show();

    buttons.forEach((b) => b.addEventListener('click', async () => {
        if (deferred) {
            deferred.prompt();
            const choice = await deferred.userChoice.catch(() => null);
            deferred = null;
            if (choice && choice.outcome === 'accepted') {
                buttons.forEach((x) => x.classList.add('d-none'));
                card?.classList.add('d-none');
            }
        } else if (isIos && window.bootstrap) {
            window.bootstrap.Modal.getOrCreateInstance(document.getElementById('ios-install')).show();
        }
    }));

    document.querySelector('[data-install-dismiss]')?.addEventListener('click', () => {
        card?.classList.add('d-none');
        try { localStorage.setItem('fm-install-dismissed', '1'); } catch (e) { /* ignore */ }
    });

    window.addEventListener('appinstalled', () => {
        buttons.forEach((x) => x.classList.add('d-none'));
        card?.classList.add('d-none');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initApp();
    initChoices();
    initDueWarning();
    initConfirms();
    initFormHelpers();
    initColourRows();
    document.querySelectorAll('[data-items]').forEach((r) => (r._editor = new ItemEditor(r)));
    document.querySelectorAll('[data-pos]').forEach((r) => (r._pos = new PosSell(r)));
    document.querySelectorAll('[data-payment-lines]').forEach((r) => (r._pay = new PaymentLines(r)));
    initQuickAdd();
    initLookup();
});

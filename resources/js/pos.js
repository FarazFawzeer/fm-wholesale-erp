/**
 * FM Shoes - POS style selling screen.
 * Smart search (item no / name / brand / colour) with suggestions, product tiles,
 * a pop-up to choose pairs per colour, and the bill on the right (bottom sheet on phones).
 */
const fmt = (n) => new Intl.NumberFormat('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n || 0);
const num = (v) => parseFloat(v) || 0;
const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
/** Group colour lines by colour name: [{colour, hex, qty, sized, items: [...]}] */
export const colourGroups = (list) => {
    const out = [];
    list.forEach((c) => {
        const name = c.colour || c.name;
        let g = out.find((x) => x.key === name.toLowerCase());
        if (!g) out.push((g = { key: name.toLowerCase(), colour: name, hex: c.hex, qty: 0, sized: false, items: [] }));
        g.items.push(c);
        g.qty += c.qty;
        g.sized = g.sized || !!c.size;
    });
    return out;
};
const norm = (s) => String(s ?? '').toLowerCase().replace(/[\s\-_.]+/g, '');

export class PosSell {
    constructor(form) {
        this.form = form;
        this.products = JSON.parse(form.dataset.products || '[]');
        this.cart = new Map(); // productId -> { product, price, qty: {colourId: n} }
        this.cat = '';
        this.q = '';
        this.active = -1;

        this.search = form.querySelector('.js-pos-search');
        this.suggest = form.querySelector('.js-suggest');
        this.grid = form.querySelector('.js-grid');
        this.modalEl = document.getElementById('pos-pick');
        this.modal = window.bootstrap.Modal.getOrCreateInstance(this.modalEl);

        this.bind();
        JSON.parse(form.dataset.initial || '[]').forEach((it) => {
            const p = this.products.find((x) => x.id == it.product_id);
            if (p) this.cart.set(p.id, { product: p, price: num(it.price), qty: Object.fromEntries(Object.entries(it.colours || {}).map(([k, v]) => [k, parseInt(v) || 0])) });
        });
        this.renderGrid();
        this.renderCart();
        const start = form.dataset.start && this.products.find((p) => p.id == form.dataset.start);
        if (start) setTimeout(() => this.open(start), 300);
    }

    // ---------- Search ----------
    score(p, q) {
        if (!q) return 1;
        const code = norm(p.code);
        const name = String(p.name).toLowerCase();
        const nq = norm(q);
        let s = 0;
        if (code === nq) s = 1000;
        else if (code.startsWith(nq)) s = 800;
        else if (code.includes(nq)) s = 600;
        const words = q.toLowerCase().split(/\s+/).filter(Boolean);
        const hay = [name, String(p.brand || '').toLowerCase(), p.colours.map((c) => c.name.toLowerCase()).join(' '), String(p.size || '')].join(' ');
        if (words.every((w) => hay.includes(w) || code.includes(norm(w)))) {
            s = Math.max(s, 100);
            if (name.startsWith(words[0])) s += 300;
            else if (name.split(/\s+/).some((w) => w.startsWith(words[0]))) s += 150;
        }
        return s;
    }

    matches() {
        return this.products
            .map((p) => ({ p, s: this.score(p, this.q) }))
            .filter(({ p, s }) => s > 0 && (!this.cat || (this.cat === 'instock' ? this.stock(p) > 0 : p.category_id == this.cat)))
            .sort((a, b) => b.s - a.s)
            .map(({ p }) => p);
    }

    mark(text, q) {
        const t = esc(text);
        if (!q) return t;
        const i = String(text).toLowerCase().indexOf(q.toLowerCase());
        return i < 0 ? t : esc(text.slice(0, i)) + '<mark>' + esc(text.slice(i, i + q.length)) + '</mark>' + esc(text.slice(i + q.length));
    }

    stock(p) {
        return p.colours.reduce((s, c) => s + c.qty, 0);
    }

    img(p, cls = '') {
        return p.image
            ? `<img src="${esc(p.image)}" alt="" class="${cls}" loading="lazy">`
            : `<span class="no-img ${cls}"><iconify-icon icon="ph:sneaker"></iconify-icon></span>`;
    }

    renderSuggest() {
        if (!this.q) {
            this.suggest.classList.remove('show');
            return;
        }
        const list = this.matches().slice(0, 6);
        this.active = list.length ? 0 : -1;
        this.suggest.innerHTML = list.length
            ? list.map((p, i) => {
                  const st = this.stock(p);
                  return `<button type="button" class="sug ${i === 0 ? 'active' : ''}" data-id="${p.id}">
                    ${this.img(p)}
                    <span class="flex-grow-1 min-w-0"><span class="sug-code">${this.mark(p.code, this.q)}</span>
                        <b class="d-block text-truncate">${this.mark(p.name, this.q)}</b>
                        <small class="text-muted">${p.size ? 'Size ' + esc(p.size) + ' · ' : ''}${p.colours.map((c) => esc(c.name)).join(', ')}</small></span>
                    <span class="text-end"><b class="d-block">Rs ${fmt(p.price)}</b><small class="${st > 0 ? 'text-success' : 'text-danger'}">${st > 0 ? st + ' pairs' : 'Out of stock'}</small></span>
                </button>`;
              }).join('') + '<div class="sug-hint">↑ ↓ to move · Enter to add</div>'
            : `<div class="sug-none">No match for "${esc(this.q)}"</div>`;
        this.suggest.classList.add('show');
    }

    renderGrid() {
        const list = this.matches();
        this.form.querySelector('.js-empty').classList.toggle('d-none', list.length > 0);
        this.form.querySelector('.js-empty-q').textContent = this.q;
        this.grid.innerHTML = list.map((p) => {
            const st = this.stock(p);
            const inCart = this.cart.has(p.id);
            return `<button type="button" class="pos-tile ${st <= 0 ? 'out' : ''} ${inCart ? 'in-cart' : ''}" data-id="${p.id}">
                <span class="pos-tile-img">${this.img(p)}
                    ${inCart ? `<span class="pos-tile-badge">${Object.values(this.cart.get(p.id).qty).reduce((a, b) => a + b, 0)} in bill</span>` : ''}
                    ${st <= 0 ? '<span class="pos-tile-out">Out of stock</span>' : ''}</span>
                <span class="pos-tile-body">
                    <span class="pos-tile-code">${esc(p.code)}</span>
                    <span class="pos-tile-name">${esc(p.name)}</span>
                    <span class="pos-tile-dots">${p.colours.slice(0, 6).map((c) => `<i title="${esc(c.name)}: ${c.qty}" style="background:${c.hex}" class="${c.qty <= 0 ? 'empty' : ''}"></i>`).join('')}</span>
                    <span class="pos-tile-foot"><b>Rs ${fmt(p.price)}</b><small>${st} prs</small></span>
                </span>
            </button>`;
        }).join('');
    }

    // ---------- Pick colours ----------
    open(p) {
        if (!p) return;
        this.picking = p;
        const line = this.cart.get(p.id);
        const m = this.modalEl;
        m.querySelector('.js-pick-img').innerHTML = this.img(p);
        m.querySelector('.js-pick-code').textContent = p.code;
        m.querySelector('.js-pick-name').textContent = p.name;
        m.querySelector('.js-pick-meta').textContent = [p.size ? 'Size ' + p.size : '', p.brand, this.stock(p) + ' pairs in stock'].filter(Boolean).join(' · ');
        m.querySelector('.js-pick-price').value = line ? line.price : p.price;
        m.querySelector('.js-pick-add').textContent = line ? 'Update bill' : 'Add to bill';
        const row = (c, label, dot) => {
            const q = line?.qty[c.id] || '';
            const out = c.qty <= 0;
            return `<div class="pick-row ${out ? 'out' : ''} ${dot ? '' : 'pick-row-size'}" data-colour="${c.id}" data-max="${Math.max(0, c.qty)}">
                ${dot ? `<span class="pick-dot" style="background:${c.hex}"></span>` : ''}
                <span class="flex-grow-1"><b>${esc(label)}</b><small class="d-block ${out ? 'text-danger' : 'text-muted'}">${out ? 'None left' : c.qty + ' in stock'}</small></span>
                <span class="pick-quick">${[6, 12].map((n) => `<button type="button" class="js-plus" data-n="${n}" ${out ? 'disabled' : ''}>+${n}</button>`).join('')}</span>
                <span class="stepper"><button type="button" class="js-step" data-d="-1" ${out ? 'disabled' : ''}>−</button>
                    <input type="number" min="0" max="${Math.max(0, c.qty)}" value="${q}" placeholder="0" inputmode="numeric" class="js-pick-qty" ${out ? 'disabled' : ''}>
                    <button type="button" class="js-step" data-d="1" ${out ? 'disabled' : ''}>+</button></span>
            </div>`;
        };
        // one block per colour; a colour with size ranges shows each range under it
        m.querySelector('.js-pick-colours').innerHTML = p.colours.length
            ? colourGroups(p.colours).map((g) => g.sized
                ? `<div class="pick-group"><div class="pick-group-head"><span class="pick-dot" style="background:${g.hex}"></span><b>${esc(g.colour)}</b><small class="text-muted">${g.qty} in stock</small></div>
                    ${g.items.map((c) => row(c, c.size ? 'Size ' + c.size : 'Other', false)).join('')}</div>`
                : g.items.map((c) => row(c, c.name, true)).join('')).join('')
            : '<div class="text-danger small">This product has no colours / stock yet. Buy stock first.</div>';
        this.pickTotal();
        this.modal.show();
        setTimeout(() => m.querySelector('.js-pick-qty:not([disabled])')?.focus(), 350);
    }

    pickTotal() {
        const m = this.modalEl;
        let pairs = 0;
        m.querySelectorAll('.pick-row').forEach((r) => {
            const inp = r.querySelector('.js-pick-qty');
            const v = Math.max(0, parseInt(inp.value) || 0);
            inp.classList.toggle('is-invalid', v > parseInt(r.dataset.max));
            pairs += v;
        });
        m.querySelector('.js-pick-pairs').textContent = pairs;
        m.querySelector('.js-pick-total').textContent = fmt(pairs * num(m.querySelector('.js-pick-price').value));
    }

    addPicked() {
        const m = this.modalEl;
        const p = this.picking;
        const qty = {};
        let bad = false;
        m.querySelectorAll('.pick-row').forEach((r) => {
            const v = Math.max(0, parseInt(r.querySelector('.js-pick-qty').value) || 0);
            if (v > parseInt(r.dataset.max)) bad = true;
            if (v > 0) qty[r.dataset.colour] = v;
        });
        if (bad) {
            m.querySelector('.is-invalid')?.focus();
            return;
        }
        if (Object.keys(qty).length) this.cart.set(p.id, { product: p, price: num(m.querySelector('.js-pick-price').value), qty });
        else this.cart.delete(p.id);
        this.modal.hide();
        this.search.value = '';
        this.q = '';
        this.renderSuggest();
        this.renderCart();
        this.renderGrid();
    }

    // ---------- Bill ----------
    renderCart() {
        const box = this.form.querySelector('.js-cart');
        let i = 0;
        let pairs = 0;
        let amount = 0;
        box.innerHTML = [...this.cart.values()].map(({ product: p, price, qty }) => {
            const n = `items[${i++}]`;
            const lp = Object.values(qty).reduce((a, b) => a + b, 0);
            pairs += lp;
            amount += lp * price;
            const picked = Object.entries(qty).map(([cid, q]) => ({ ...(p.colours.find((x) => x.id == cid) || { name: '?', colour: '?' }), id: cid, qty: q }));
            const chips = colourGroups(picked).map((g) => `<span class="colour-chip ${g.sized ? 'colour-chip-sizes' : ''}"><i style="background:${g.hex}"></i>${esc(g.colour)}${g.sized
                ? g.items.map((c) => `<span class="cc-size">${esc(c.size || 'other')}<b>${c.qty}</b></span>`).join('')
                : `<b>${g.qty}</b>`}</span>`).join('')
                + picked.map((c) => `<input type="hidden" name="${n}[colours][${c.id}]" value="${c.qty}">`).join('');
            return `<div class="pos-line" data-id="${p.id}">
                <input type="hidden" name="${n}[product_id]" value="${p.id}"><input type="hidden" name="${n}[price]" value="${price}">
                ${this.img(p, 'pos-line-img')}
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex justify-content-between gap-2"><b class="text-truncate">${esc(p.name)}</b><b class="money">${fmt(lp * price)}</b></div>
                    <div class="small text-muted">${esc(p.code)} · ${lp} prs × ${fmt(price)}</div>
                    <div class="colour-list mt-1">${chips}</div>
                </div>
                <div class="pos-line-actions">
                    <button type="button" class="js-edit" title="Change"><iconify-icon icon="solar:pen-2-outline"></iconify-icon></button>
                    <button type="button" class="js-remove" title="Remove"><iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon></button>
                </div>
            </div>`;
        }).join('');

        const discount = num(this.form.querySelector('.js-discount').value);
        const total = Math.max(0, amount - discount);
        const set = (sel, v) => this.form.querySelectorAll(sel).forEach((e) => (e.textContent = v));
        set('.js-cart-count', this.cart.size);
        set('.js-cart-pairs', pairs);
        set('.js-subtotal', 'Rs ' + fmt(amount));
        set('.js-total', fmt(total));
        this.form.querySelector('.js-cart-empty').classList.toggle('d-none', this.cart.size > 0);
        this.form.querySelector('.js-save').disabled = this.cart.size === 0;
        this.form.classList.toggle('has-items', this.cart.size > 0);
        this.form.dataset.total = total;
        document.dispatchEvent(new CustomEvent('erp:total', { detail: { total } }));
    }

    // ---------- Events ----------
    bind() {
        const f = this.form;
        this.search.addEventListener('input', () => {
            this.q = this.search.value.trim();
            this.renderSuggest();
            this.renderGrid();
        });
        this.search.addEventListener('keydown', (e) => {
            const items = [...this.suggest.querySelectorAll('.sug')];
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                if (!items.length) return;
                this.active = (this.active + (e.key === 'ArrowDown' ? 1 : -1) + items.length) % items.length;
                items.forEach((b, i) => b.classList.toggle('active', i === this.active));
                items[this.active].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter') {
                e.preventDefault();
                const b = items[this.active] || items[0];
                if (b) this.open(this.products.find((p) => p.id == b.dataset.id));
            } else if (e.key === 'Escape') {
                this.search.value = '';
                this.q = '';
                this.renderSuggest();
                this.renderGrid();
            }
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
                e.preventDefault();
                this.search.focus();
            }
        });
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.pos-search')) this.suggest.classList.remove('show');
        });
        this.search.addEventListener('focus', () => this.q && this.renderSuggest());

        this.suggest.addEventListener('click', (e) => {
            const b = e.target.closest('.sug');
            if (b) this.open(this.products.find((p) => p.id == b.dataset.id));
        });
        this.grid.addEventListener('click', (e) => {
            const t = e.target.closest('.pos-tile');
            if (t) this.open(this.products.find((p) => p.id == t.dataset.id));
        });
        f.querySelector('.js-cats').addEventListener('click', (e) => {
            const b = e.target.closest('.pos-cat');
            if (!b) return;
            f.querySelectorAll('.pos-cat').forEach((x) => x.classList.toggle('active', x === b));
            this.cat = b.dataset.cat;
            this.renderGrid();
        });

        // pick modal
        const m = this.modalEl;
        m.addEventListener('input', () => this.pickTotal());
        m.addEventListener('click', (e) => {
            const row = e.target.closest('.pick-row');
            const step = e.target.closest('.js-step');
            const plus = e.target.closest('.js-plus');
            if (row && (step || plus)) {
                const inp = row.querySelector('.js-pick-qty');
                const max = parseInt(row.dataset.max);
                const v = (parseInt(inp.value) || 0) + (step ? parseInt(step.dataset.d) : parseInt(plus.dataset.n));
                inp.value = Math.min(max, Math.max(0, v)) || '';
                this.pickTotal();
            }
            if (e.target.closest('.js-pick-add')) this.addPicked();
        });
        m.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.addPicked();
            }
        });

        // bill
        f.querySelector('.js-cart').addEventListener('click', (e) => {
            const line = e.target.closest('.pos-line');
            if (!line) return;
            if (e.target.closest('.js-remove')) {
                this.cart.delete(parseInt(line.dataset.id));
                this.renderCart();
                this.renderGrid();
            } else if (e.target.closest('.js-edit')) {
                this.open(this.products.find((p) => p.id == line.dataset.id));
            }
        });
        f.querySelector('.js-discount').addEventListener('input', () => this.renderCart());
        f.querySelector('.js-clear').addEventListener('click', () => {
            if (!this.cart.size || !confirm('Clear all items from this bill?')) return;
            this.cart.clear();
            this.renderCart();
            this.renderGrid();
        });
        f.querySelectorAll('.js-open-cart').forEach((b) => b.addEventListener('click', () => f.classList.add('show-cart')));
        f.querySelectorAll('.js-close-cart').forEach((b) => b.addEventListener('click', () => f.classList.remove('show-cart')));

        // new product from quick add -> straight into the picker
        document.addEventListener('erp:product-created', (e) => {
            this.products.unshift(e.detail.product);
            this.renderGrid();
            this.open(e.detail.product);
        });
    }
}

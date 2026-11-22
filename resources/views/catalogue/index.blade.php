@extends('layouts.vertical', ['subtitle' => 'Send catalogue'])

@section('content')
    <x-page-header title="Send catalogue" :crumbs="['Products' => route('products.index'), 'Send catalogue']" />

    <div class="cat-intro mb-3">
        <iconify-icon icon="solar:gallery-send-outline"></iconify-icon>
        <div>Tick the products, then tap <b>Photos</b> to send each one as a WhatsApp photo with the <b>item no</b> printed on it - the customer can simply send back the photo they want. Or send everything as one <b>PDF</b>. <b>No prices.</b></div>
    </div>

    <div class="cat-tools mb-3" data-cat-tools>
        <div class="pos-search flex-grow-1">
            <iconify-icon icon="solar:magnifer-linear" class="pos-search-icon"></iconify-icon>
            <input type="search" class="form-control js-cat-q" placeholder="Search item no, name, brand or colour..." autocomplete="off">
        </div>
        <div class="pos-cats js-cat-types">
            <button type="button" class="pos-cat active" data-type="">All</button>
            @foreach ($categories as $c)<button type="button" class="pos-cat" data-type="{{ $c->id }}">{{ $c->name }}</button>@endforeach
        </div>
        <div class="cat-btns">
            <button type="button" class="btn btn-sm btn-light js-cat-all"><iconify-icon icon="solar:check-square-outline" class="align-middle me-1"></iconify-icon>Tick all shown</button>
            <button type="button" class="btn btn-sm btn-light js-cat-none">Clear</button>
            <label class="cat-pairs-opt"><input type="checkbox" class="form-check-input js-cat-pairs"> Show pairs</label>
        </div>
    </div>

    @if ($products->isEmpty())
        <div class="card"><div class="card-body text-center text-muted py-5">No products in stock.</div></div>
    @endif

    <div class="cat-grid js-cat-grid">
        @foreach ($products as $p)
            <label class="cat-card" data-id="{{ $p->id }}" data-type="{{ $p->category_id }}"
                data-code="{{ $p->code }}" data-name="{{ $p->name }}" data-image="{{ $p->imageUrl() }}"
                data-lines='@json($p->availableLines())' data-lines-pairs='@json($p->availableLines(true))'
                data-hay="{{ strtolower(implode(' ', [$p->code, $p->name, $p->brand?->name, $p->category?->name, $p->colours->pluck('colour')->implode(' ')])) }}">
                <input type="checkbox" class="js-cat-check" value="{{ $p->id }}" @checked(in_array($p->id, $selected))>
                <span class="cat-tick"><iconify-icon icon="solar:check-read-linear"></iconify-icon></span>
                <span class="cat-img">@if ($p->image_path)<img src="{{ asset($p->image_path) }}" alt="" loading="lazy">@else<iconify-icon icon="ph:sneaker"></iconify-icon>@endif</span>
                <span class="cat-body">
                    <span class="item-no">{{ $p->code }}</span>
                    <span class="cat-name">{{ $p->name }}</span>
                    <span class="cat-avail">{{ implode(' · ', $p->availableLines()) }}</span>
                    <span class="cat-pairs">{{ qty($p->totalStock()) }} pairs</span>
                </span>
            </label>
        @endforeach
    </div>
    <div class="cat-none d-none text-center text-muted py-4">No product matches.</div>

    {{-- Send bar --}}
    <div class="cat-bar" data-cat-bar data-pdf="{{ route('catalogue.pdf') }}" data-max="{{ \App\Http\Controllers\CatalogueController::MAX }}">
        <div class="cat-bar-in">
            <div class="cat-count"><b class="js-cat-count">0</b> <span>selected</span></div>
            <div class="dropup">
                <button type="button" class="btn btn-light js-cat-dl-btn disabled" data-bs-toggle="dropdown" aria-expanded="false" title="Download"><iconify-icon icon="solar:file-download-outline" class="align-middle"></iconify-icon><span class="d-none d-md-inline ms-1">Download</span></button>
                <div class="dropdown-menu cat-dl-menu">
                    <button type="button" class="dropdown-item js-cat-dl-photos"><iconify-icon icon="solar:gallery-outline"></iconify-icon><span><b>Photos</b><small>One JPG per product</small></span></button>
                    <a href="#" class="dropdown-item js-cat-download" download><iconify-icon icon="solar:document-text-outline"></iconify-icon><span><b>PDF</b><small>All in one catalogue</small></span></a>
                </div>
            </div>
            <a href="https://wa.me/" target="_blank" rel="noopener" class="btn btn-outline-success js-cat-send disabled" data-share-pdf="" data-file-name="Catalogue-{{ now()->format('Y-m-d') }}.pdf" data-wa="https://wa.me/" title="Send all as one PDF"><iconify-icon icon="solar:document-text-outline" class="align-middle me-1"></iconify-icon><span>PDF</span></a>
            <button type="button" class="btn btn-success js-cat-photos disabled" title="Send each product as a photo"><i class="bx bxl-whatsapp me-1"></i><span>Photos</span></button>
        </div>
    </div>
@endsection

@push('page-js')
    @include('partials.share-pdf')
    @include('partials.photo-card')
    <script>
    (function () {
        const grid = document.querySelector('.js-cat-grid');
        const bar = document.querySelector('[data-cat-bar]');
        const send = bar.querySelector('.js-cat-send');
        const dl = bar.querySelector('.js-cat-download');
        const pairs = document.querySelector('.js-cat-pairs');
        const q = document.querySelector('.js-cat-q');
        const max = parseInt(bar.dataset.max);
        const photosBtn = bar.querySelector('.js-cat-photos');
        const dlBtn = bar.querySelector('.js-cat-dl-btn');
        const dlPhotos = bar.querySelector('.js-cat-dl-photos');
        const PHOTO_MAX = 30; // WhatsApp sends up to 30 photos at once
        const cards = {};     // "id|pairs" -> Promise<File>
        let type = '';
        let timer = null;
        let photoTimer = null;
        const toast = (m) => { let t = document.getElementById('share-pdf-toast'); if (!t) { t = document.createElement('div'); t.id = 'share-pdf-toast'; t.style.cssText = 'position:fixed;left:50%;bottom:90px;transform:translateX(-50%);background:#111827;color:#fff;padding:10px 16px;border-radius:10px;font:500 14px Inter,sans-serif;z-index:9999;max-width:92vw;text-align:center'; document.body.appendChild(t); } t.textContent = m; t.style.display = 'block'; clearTimeout(t._h); t._h = setTimeout(() => (t.style.display = 'none'), 7000); };
        const cardFor = (id) => {
            const key = id + '|' + (pairs.checked ? 1 : 0);
            if (!cards[key]) {
                const el = grid.querySelector(`.cat-card[data-id="${id}"]`);
                cards[key] = window.fmPhotoCard({ code: el.dataset.code, name: el.dataset.name, image: el.dataset.image,
                    lines: JSON.parse(pairs.checked ? el.dataset.linesPairs : el.dataset.lines) });
                cards[key].then((f) => (cards[key].file = f));
            }
            return cards[key];
        };
        const photoFiles = (ids) => ids.map((id) => cards[id + '|' + (pairs.checked ? 1 : 0)]?.file);
        const setPhotoLabel = (t) => (photosBtn.querySelector('span').textContent = t || 'Photos');

        const checked = () => [...grid.querySelectorAll('.js-cat-check:checked')].map((c) => c.value);
        const update = () => {
            const ids = checked();
            bar.querySelector('.js-cat-count').textContent = ids.length;
            bar.classList.toggle('has', ids.length > 0);
            const over = ids.length > max;
            const url = ids.length && !over ? bar.dataset.pdf + '?ids=' + ids.join(',') + (pairs.checked ? '&pairs=1' : '') : '';
            [send, dl].forEach((b) => b.classList.toggle('disabled', !url));
            dlBtn.classList.toggle('disabled', !ids.length);
            dlPhotos.classList.toggle('disabled', !ids.length || ids.length > PHOTO_MAX);
            photosBtn.classList.toggle('disabled', !ids.length || ids.length > PHOTO_MAX);
            setPhotoLabel(ids.length > PHOTO_MAX ? 'Max ' + PHOTO_MAX + ' photos' : '');
            clearTimeout(photoTimer);
            if (ids.length && ids.length <= PHOTO_MAX) photoTimer = setTimeout(() => ids.forEach(cardFor), 600); // ready before the tap
            bar.querySelector('.cat-count span').textContent = over ? 'selected - max ' + max : 'selected';
            dl.href = url ? url + '&download=1' : '#';
            if (send.dataset.sharePdf !== url) {
                send.dataset.sharePdf = url;
                if (send.dataset.label) send.querySelector('span').textContent = send.dataset.label; // back to "WhatsApp PDF"
                clearTimeout(timer);
                timer = setTimeout(() => window.sharePdfPrepare && window.sharePdfPrepare(url), 1200); // get it ready before the tap
            }
            grid.querySelectorAll('.cat-card').forEach((c) => c.classList.toggle('on', c.querySelector('.js-cat-check').checked));
        };
        const filter = () => {
            const words = q.value.trim().toLowerCase().split(/\s+/).filter(Boolean);
            let shown = 0;
            grid.querySelectorAll('.cat-card').forEach((c) => {
                const ok = (!type || c.dataset.type === type) && words.every((w) => c.dataset.hay.includes(w));
                c.classList.toggle('d-none', !ok);
                if (ok) shown++;
            });
            document.querySelector('.cat-none').classList.toggle('d-none', shown > 0 || !grid.children.length);
        };

        grid.addEventListener('change', update);
        pairs.addEventListener('change', update);
        q.addEventListener('input', filter);
        document.querySelector('.js-cat-types').addEventListener('click', (e) => {
            const b = e.target.closest('.pos-cat');
            if (!b) return;
            document.querySelectorAll('.js-cat-types .pos-cat').forEach((x) => x.classList.toggle('active', x === b));
            type = b.dataset.type;
            filter();
        });
        document.querySelector('.js-cat-all').addEventListener('click', () => {
            grid.querySelectorAll('.cat-card:not(.d-none) .js-cat-check').forEach((c) => (c.checked = true));
            update();
        });
        document.querySelector('.js-cat-none').addEventListener('click', () => {
            grid.querySelectorAll('.js-cat-check').forEach((c) => (c.checked = false));
            update();
        });
        [send, dl].forEach((b) => b.addEventListener('click', (e) => { if (b.classList.contains('disabled')) e.preventDefault(); }, true));

        // Download the photo cards (saved to Downloads / Gallery)
        dlPhotos.addEventListener('click', async () => {
            const ids = checked();
            if (!ids.length || ids.length > PHOTO_MAX) return;
            toast('Saving ' + ids.length + (ids.length === 1 ? ' photo...' : ' photos...'));
            const files = (await Promise.all(ids.map(cardFor))).filter(Boolean);
            window.fmSaveFiles(files);
            toast(files.length + (files.length === 1 ? ' photo' : ' photos') + ' saved to Downloads. If the browser asks, allow downloading several files.');
        });

        // Send each ticked product as a photo (phone share sheet -> WhatsApp -> customer)
        photosBtn.addEventListener('click', async () => {
            const ids = checked();
            if (!ids.length || ids.length > PHOTO_MAX || photosBtn.dataset.busy) return;
            let files = photoFiles(ids);
            if (files.every(Boolean)) {
                setPhotoLabel();
                await window.fmShareFiles(files, toast);
                return;
            }
            // not ready yet: make them, then ask for one more tap (phones need a fresh tap to open the share sheet)
            photosBtn.dataset.busy = '1';
            setPhotoLabel('Preparing...');
            try {
                await Promise.all(ids.map(cardFor));
                setPhotoLabel('Ready - tap to send');
                toast('Photos are ready. Tap the Photos button again to send.');
            } catch (e) {
                setPhotoLabel();
                toast('Could not make the photos. Please try again.');
            } finally {
                delete photosBtn.dataset.busy;
            }
        });
        update();
    })();
    </script>
@endpush

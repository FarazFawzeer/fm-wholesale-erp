@extends('layouts.vertical', ['subtitle' => 'Send catalogue'])

@section('content')
    <x-page-header title="Send catalogue" :crumbs="['Products' => route('products.index'), 'Send catalogue']" />

    <div class="cat-intro mb-3">
        <iconify-icon icon="solar:gallery-send-outline"></iconify-icon>
        <div>Tick the products the customer should see, then tap <b>WhatsApp PDF</b>. The PDF shows the photo, item no and the colours / sizes in stock. <b>No prices.</b></div>
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
        </div>
    </div>

    @if ($products->isEmpty())
        <div class="card"><div class="card-body text-center text-muted py-5">No products in stock.</div></div>
    @endif

    <div class="cat-grid js-cat-grid">
        @foreach ($products as $p)
            <label class="cat-card" data-id="{{ $p->id }}" data-type="{{ $p->category_id }}"
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
            <label class="cat-pairs-opt"><input type="checkbox" class="form-check-input js-cat-pairs"> Show pairs</label>
            <a href="#" class="btn btn-light js-cat-download disabled" download><iconify-icon icon="solar:file-download-outline" class="align-middle"></iconify-icon><span class="d-none d-sm-inline ms-1">Download</span></a>
            <a href="https://wa.me/" target="_blank" rel="noopener" class="btn btn-success js-cat-send disabled" data-share-pdf="" data-file-name="Catalogue-{{ now()->format('Y-m-d') }}.pdf" data-wa="https://wa.me/"><i class="bx bxl-whatsapp me-1"></i><span>WhatsApp PDF</span></a>
        </div>
    </div>
@endsection

@push('page-js')
    @include('partials.share-pdf')
    <script>
    (function () {
        const grid = document.querySelector('.js-cat-grid');
        const bar = document.querySelector('[data-cat-bar]');
        const send = bar.querySelector('.js-cat-send');
        const dl = bar.querySelector('.js-cat-download');
        const pairs = bar.querySelector('.js-cat-pairs');
        const q = document.querySelector('.js-cat-q');
        const max = parseInt(bar.dataset.max);
        let type = '';
        let timer = null;

        const checked = () => [...grid.querySelectorAll('.js-cat-check:checked')].map((c) => c.value);
        const update = () => {
            const ids = checked();
            bar.querySelector('.js-cat-count').textContent = ids.length;
            bar.classList.toggle('has', ids.length > 0);
            const over = ids.length > max;
            const url = ids.length && !over ? bar.dataset.pdf + '?ids=' + ids.join(',') + (pairs.checked ? '&pairs=1' : '') : '';
            [send, dl].forEach((b) => b.classList.toggle('disabled', !url));
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
        update();
    })();
    </script>
@endpush

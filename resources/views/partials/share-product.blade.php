{{-- "Share" one product with a customer: the photo as an image + item no, colours and sizes in stock (no price).
     Phones open the share sheet (WhatsApp -> customer). Elsewhere WhatsApp opens with the text and the photo is saved. --}}
<script>
(function () {
    if (window.__shareProduct) return;
    window.__shareProduct = true;
    const files = {};
    const prepare = (btn) => {
        const url = btn.dataset.image;
        if (!url || files[url]) return;
        files[url] = fetch(url, { credentials: 'same-origin' })
            .then((r) => (r.ok ? r.blob() : null))
            .then((b) => (files[url].file = b ? new File([b], btn.dataset.fileName || 'product.jpg', { type: b.type || 'image/jpeg' }) : null))
            .catch(() => null);
    };
    const ready = () => document.querySelectorAll('[data-share-product]').forEach(prepare);
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', ready); else ready();

    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-share-product]');
        if (!btn) return;
        e.preventDefault();
        const text = btn.dataset.shareText;
        const file = files[btn.dataset.image]?.file;
        try {
            if (file && navigator.canShare && navigator.canShare({ files: [file] })) {
                await navigator.share({ files: [file], text });
                return;
            }
            if (!file && navigator.share) {
                await navigator.share({ text });
                return;
            }
        } catch (err) {
            if (err.name === 'AbortError') return;
        }
        // Computer / older phone: save the photo and open WhatsApp with the details typed in
        if (file) {
            const a = document.createElement('a');
            a.href = URL.createObjectURL(file);
            a.download = file.name;
            document.body.appendChild(a);
            a.click();
            setTimeout(() => { URL.revokeObjectURL(a.href); a.remove(); }, 4000);
        }
        window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank', 'noopener');
    });
})();
</script>

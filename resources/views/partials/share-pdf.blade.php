{{-- WhatsApp / share buttons that send the invoice PDF file itself (no text, no link).
     Phones (Android / iPhone) and Windows open the share sheet -> pick WhatsApp -> pick the customer.
     Where file sharing is not supported, the PDF is downloaded and WhatsApp opens so it can be attached. --}}
<script>
(function () {
    if (window.__sharePdf) return;
    window.__sharePdf = true;
    const toast = (msg) => {
        let t = document.getElementById('share-pdf-toast');
        if (!t) {
            t = document.createElement('div');
            t.id = 'share-pdf-toast';
            t.style.cssText = 'position:fixed;left:50%;bottom:24px;transform:translateX(-50%);background:#111827;color:#fff;padding:10px 16px;border-radius:10px;font:500 14px Inter,sans-serif;z-index:9999;box-shadow:0 8px 24px rgba(0,0,0,.25);max-width:92vw;text-align:center';
            document.body.appendChild(t);
        }
        t.textContent = msg;
        t.style.display = 'block';
        clearTimeout(t._h);
        t._h = setTimeout(() => (t.style.display = 'none'), 4500);
    };
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-share-pdf]');
        if (!btn) return;
        e.preventDefault();
        if (btn.dataset.busy) return;
        btn.dataset.busy = '1';
        const label = btn.querySelector('span');
        const old = label ? label.textContent : '';
        if (label) label.textContent = 'Preparing PDF...';
        try {
            const res = await fetch(btn.dataset.sharePdf, { credentials: 'same-origin' });
            if (!res.ok) throw new Error('PDF ' + res.status);
            const blob = await res.blob();
            const name = btn.dataset.fileName || 'Invoice.pdf';
            const file = new File([blob], name, { type: 'application/pdf' });
            if (navigator.canShare && navigator.canShare({ files: [file] })) {
                try {
                    await navigator.share({ files: [file] });
                } catch (err) {
                    if (err.name !== 'AbortError') throw err;
                }
            } else {
                // Fallback (older browsers): save the PDF and open WhatsApp so it can be attached
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = name;
                document.body.appendChild(a);
                a.click();
                setTimeout(() => { URL.revokeObjectURL(a.href); a.remove(); }, 2000);
                if (btn.dataset.wa) window.open(btn.dataset.wa, '_blank', 'noopener');
                toast('PDF saved. In WhatsApp tap the attach (📎) button and choose ' + name);
            }
        } catch (err) {
            toast('Could not prepare the PDF. Please check the internet and try again.');
        } finally {
            delete btn.dataset.busy;
            if (label) label.textContent = old;
        }
    });
})();
</script>

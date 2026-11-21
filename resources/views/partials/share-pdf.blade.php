{{-- WhatsApp / share buttons that send the invoice PDF file itself (no text, no link).
     Phones (Android / iPhone) and Windows open the share sheet -> pick WhatsApp -> pick the customer.
     The PDF is prepared as soon as the page opens, because phones only allow the share sheet
     straight after a tap - waiting for a slow server first made the share fail.
     Where file sharing is not supported, the PDF is downloaded and WhatsApp opens so it can be attached. --}}
<script>
(function () {
    if (window.__sharePdf) return;
    window.__sharePdf = true;
    const toast = (msg, long) => {
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
        t._h = setTimeout(() => (t.style.display = 'none'), long ? 9000 : 4500);
    };

    // One download per PDF address, shared by all buttons
    const jobs = {};
    const load = (url) => {
        if (!jobs[url] || jobs[url].failed) {
            const job = { failed: false };
            job.promise = fetch(url, { credentials: 'same-origin', cache: 'no-store' })
                .catch(() => { throw new Error('No connection to the server. Check the internet and try again.'); })
                .then(async (res) => {
                    const type = res.headers.get('Content-Type') || '';
                    if (!res.ok || !type.includes('pdf')) {
                        const why = res.headers.get('X-Pdf-Error');
                        throw new Error('The server could not make the PDF (error ' + res.status + ')' + (why ? ': ' + why : '. Please tell your system admin.'));
                    }
                    job.blob = await res.blob();
                    return job.blob;
                })
                .catch((err) => { job.failed = true; throw err; });
            jobs[url] = job;
        }
        return jobs[url].promise;
    };

    const fileOf = (btn) => new File([jobs[btn.dataset.sharePdf].blob], btn.dataset.fileName || 'Invoice.pdf', { type: 'application/pdf' });
    const canShareFiles = (file) => !!(navigator.canShare && navigator.canShare({ files: [file] }));

    const saveAndOpenWhatsApp = (btn, file) => {
        const a = document.createElement('a');
        a.href = URL.createObjectURL(file);
        a.download = file.name;
        document.body.appendChild(a);
        a.click();
        setTimeout(() => { URL.revokeObjectURL(a.href); a.remove(); }, 4000);
        if (btn.dataset.wa) window.open(btn.dataset.wa, '_blank', 'noopener');
        toast('PDF saved. In WhatsApp tap the attach (📎) button and choose ' + file.name, true);
    };

    const setLabel = (btn, text) => {
        const label = btn.querySelector('span');
        if (!label) return;
        if (!btn.dataset.label) btn.dataset.label = label.textContent;
        label.textContent = text || btn.dataset.label;
    };

    // Start preparing the PDF right away
    const warm = () => document.querySelectorAll('[data-share-pdf]').forEach((b) => b.dataset.sharePdf && load(b.dataset.sharePdf).catch(() => {}));
    // pages that change the PDF address (catalogue) call this to prepare the new PDF early
    window.sharePdfPrepare = (url) => url && load(url).catch(() => {});
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', warm); else warm();

    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-share-pdf]');
        if (!btn) return;
        e.preventDefault();
        const url = btn.dataset.sharePdf;
        if (!url) return;
        const job = jobs[url];

        // PDF is ready: share straight away (still inside the tap)
        if (job && job.blob) {
            const file = fileOf(btn);
            if (canShareFiles(file)) {
                try {
                    await navigator.share({ files: [file] });
                } catch (err) {
                    if (err.name === 'AbortError') return; // closed the share sheet
                    saveAndOpenWhatsApp(btn, file);       // share blocked -> save + open WhatsApp
                }
            } else {
                saveAndOpenWhatsApp(btn, file);
            }
            setLabel(btn);
            return;
        }

        // Not ready yet: wait, then ask for one more tap (phones need a fresh tap to open the share sheet)
        if (btn.dataset.busy) return;
        btn.dataset.busy = '1';
        setLabel(btn, 'Preparing PDF...');
        try {
            await load(url);
            setLabel(btn, 'PDF ready - tap to send');
            btn.classList.add('pdf-ready');
            toast('PDF is ready. Tap the WhatsApp button again to send it.');
        } catch (err) {
            setLabel(btn);
            toast(err.message || 'Could not prepare the PDF.', true);
        } finally {
            delete btn.dataset.busy;
        }
    });
})();
</script>

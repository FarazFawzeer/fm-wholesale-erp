{{-- Makes a "photo card" image for WhatsApp: the product photo with the ITEM NO printed on it,
     plus name and the colours / sizes in stock (no price). When the customer sends the photo back,
     the item no is on it, so the shop knows exactly which product they want.
     Drawn in the browser (canvas), so the server does no extra work.
     window.fmPhotoCard({code, name, image, lines: []}) -> Promise<File> --}}
<script>
(function () {
    if (window.fmPhotoCard) return;
    const COMPANY = @json(trim((string) setting('company_name')));
    const PHONE = @json(trim((string) setting('company_phone')));
    const W = 1080, PH = 1080, H = 1350;
    const FONT = 'Inter, "Segoe UI", Roboto, Arial, sans-serif';

    const loadImg = (src) => new Promise((resolve) => {
        if (!src) return resolve(null);
        const img = new Image();
        img.onload = () => resolve(img);
        img.onerror = () => resolve(null);
        img.src = src;
    });
    const rounded = (ctx, x, y, w, h, r) => {
        ctx.beginPath();
        ctx.moveTo(x + r, y);
        ctx.arcTo(x + w, y, x + w, y + h, r);
        ctx.arcTo(x + w, y + h, x, y + h, r);
        ctx.arcTo(x, y + h, x, y, r);
        ctx.arcTo(x, y, x + w, y, r);
        ctx.closePath();
    };
    const fit = (ctx, text, max) => {
        if (ctx.measureText(text).width <= max) return text;
        while (text.length > 1 && ctx.measureText(text + '…').width > max) text = text.slice(0, -1);
        return text + '…';
    };
    const wrap = (ctx, text, max, maxLines) => {
        const words = text.split(' ');
        const lines = [];
        let line = '';
        for (const w of words) {
            const test = line ? line + ' ' + w : w;
            if (ctx.measureText(test).width > max && line) {
                lines.push(line);
                line = w;
            } else line = test;
        }
        if (line) lines.push(line);
        if (lines.length > maxLines) {
            lines.length = maxLines;
            lines[maxLines - 1] = fit(ctx, lines[maxLines - 1] + ' …', max);
        }
        return lines;
    };

    window.fmPhotoCard = async (p) => {
        if (document.fonts && document.fonts.ready) await document.fonts.ready;
        const img = await loadImg(p.image);
        const c = document.createElement('canvas');
        c.width = W;
        c.height = H;
        const ctx = c.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, W, H);

        // photo: same square for every product (centre crop)
        ctx.fillStyle = '#f2f2f0';
        ctx.fillRect(0, 0, W, PH);
        if (img) {
            const side = Math.min(img.naturalWidth, img.naturalHeight);
            ctx.drawImage(img, (img.naturalWidth - side) / 2, (img.naturalHeight - side) / 2, side, side, 0, 0, W, PH);
        } else {
            ctx.fillStyle = '#9ca3af';
            ctx.font = `600 48px ${FONT}`;
            ctx.textAlign = 'center';
            ctx.fillText('No photo', W / 2, PH / 2);
            ctx.textAlign = 'left';
        }

        // item no badge on the photo
        ctx.font = `800 58px ${FONT}`;
        const code = 'Item No ' + p.code;
        const cw = Math.min(ctx.measureText(code).width + 56, W - 80);
        rounded(ctx, 36, 36, cw, 92, 20);
        ctx.fillStyle = 'rgba(17, 24, 39, .92)';
        ctx.fill();
        ctx.fillStyle = '#ffffff';
        ctx.textBaseline = 'middle';
        ctx.fillText(fit(ctx, code, cw - 56), 64, 84);

        // details band
        ctx.textBaseline = 'alphabetic';
        ctx.fillStyle = '#111827';
        ctx.font = `700 54px ${FONT}`;
        ctx.fillText(fit(ctx, p.name, W - 80), 40, PH + 76);
        ctx.fillStyle = '#374151';
        ctx.font = `500 38px ${FONT}`;
        const avail = (p.lines || []).length ? 'Available: ' + p.lines.join('  ·  ') : 'Ask for available sizes';
        wrap(ctx, avail, W - 80, 2).forEach((l, i) => ctx.fillText(l, 40, PH + 136 + i * 48));

        // footer
        ctx.fillStyle = '#e5e7eb';
        ctx.fillRect(40, H - 66, W - 80, 2);
        ctx.font = `600 30px ${FONT}`;
        ctx.fillStyle = '#6b7280';
        ctx.fillText(fit(ctx, COMPANY, W / 2), 40, H - 22);
        if (PHONE) {
            ctx.textAlign = 'right';
            ctx.fillText(PHONE, W - 40, H - 22);
            ctx.textAlign = 'left';
        }

        const blob = await new Promise((r) => c.toBlob(r, 'image/jpeg', 0.86));
        return new File([blob], String(p.code).replace(/[^\w-]+/g, '_') + '.jpg', { type: 'image/jpeg' });
    };

    /** Save image files to the phone / computer (Downloads, then Gallery on phones). */
    window.fmSaveFiles = (files) => files.forEach((f, i) => setTimeout(() => {
        const a = document.createElement('a');
        a.href = URL.createObjectURL(f);
        a.download = f.name;
        document.body.appendChild(a);
        a.click();
        setTimeout(() => { URL.revokeObjectURL(a.href); a.remove(); }, 4000);
    }, i * 400));

    /** Share files on WhatsApp (phone share sheet); otherwise save them and open WhatsApp. */
    window.fmShareFiles = async (files, toast) => {
        if (navigator.canShare && navigator.canShare({ files })) {
            try {
                await navigator.share({ files });
                return 'shared';
            } catch (err) {
                if (err.name === 'AbortError') return 'cancelled';
            }
        }
        window.fmSaveFiles(files);
        window.open('https://wa.me/', '_blank', 'noopener');
        toast && toast(files.length + (files.length === 1 ? ' photo' : ' photos') + ' saved. In WhatsApp tap 📎 > Gallery / Document and choose them.');
        return 'saved';
    };
})();
</script>

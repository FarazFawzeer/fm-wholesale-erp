/* FM Shoes app - service worker.
 * Pages always come fresh from the server (stock and money must be live).
 * Only the look (CSS/JS/fonts/images) is kept on the phone so the app opens fast,
 * and a friendly "no internet" page is shown when offline. */
const VERSION = 'fm-v2';
const STATIC = VERSION + '-static';
const OFFLINE = '/offline.html';

self.addEventListener('install', (e) => {
    e.waitUntil(caches.open(STATIC).then((c) => c.addAll([OFFLINE, '/images/app/icon-192.png'])).then(() => self.skipWaiting()));
});

self.addEventListener('activate', (e) => {
    e.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => !k.startsWith(VERSION)).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (e) => {
    const req = e.request;
    if (req.method !== 'GET') return;
    const url = new URL(req.url);

    // PDF files (invoice download / WhatsApp) go straight to the server, never through the offline page
    if (url.pathname.endsWith('/pdf')) return;

    // Pages: network only, offline page if there is no connection
    if (req.mode === 'navigate') {
        e.respondWith(fetch(req).catch(() => caches.match(OFFLINE)));
        return;
    }

    // Build files have a hash in the name, so keeping them is safe
    const sameOrigin = url.origin === self.location.origin;
    const isStatic = sameOrigin && (url.pathname.startsWith('/build/') || url.pathname.startsWith('/images/'));
    const isFont = /fonts\.(googleapis|gstatic)\.com$/.test(url.hostname) || url.hostname === 'api.iconify.design';

    if (isStatic || isFont) {
        e.respondWith(
            caches.open(STATIC).then((cache) =>
                cache.match(req).then((hit) => {
                    const net = fetch(req).then((res) => {
                        if (res && (res.ok || res.type === 'opaque')) cache.put(req, res.clone());
                        return res;
                    }).catch(() => hit);
                    return hit || net;
                })
            )
        );
    }
});

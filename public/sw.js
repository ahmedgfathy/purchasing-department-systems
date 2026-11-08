const CACHE_NAME = 'ppd-shell-v2';
const SHELL_ASSETS = ['/manifest.webmanifest', '/favicon.svg', '/images/logo.svg', '/icons/icon-192.png', '/icons/icon-512.png'];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE_NAME).then((cache) => cache.addAll(SHELL_ASSETS)));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))))
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;
    const url = new URL(event.request.url);
    if (url.origin !== self.location.origin) return;

    if (SHELL_ASSETS.includes(url.pathname)) {
        event.respondWith(caches.match(event.request).then((cached) => cached || fetch(event.request)));
    }
});

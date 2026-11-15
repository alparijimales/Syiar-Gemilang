// Syiar Gemilang Service Worker (PWA & Push Notification)
const CACHE_NAME = 'syiar-gemilang-v2';
const ASSETS_TO_CACHE = [
  '/',
  '/assets/img/rgi.png',
  '/assets/img/logo.png',
  '/assets/img/icon-96.png',
  '/assets/img/icon-192.png',
  '/assets/img/icon-512.png',
  '/assets/img/icon-maskable-192.png',
  '/assets/img/icon-maskable-512.png',
  '/favicon.png',
  '/favicon.ico',
  '/manifest.json'
];

// Install Event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE).catch((err) => {
        console.warn('PWA Cache addAll warning:', err);
      });
    })
  );
  self.skipWaiting();
});

// Activate Event
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    })
  );
  return self.clients.claim();
});

// Fetch Event (Network First, Fallback to Cache)
self.addEventListener('fetch', (event) => {
  // Hanya tangani GET requests
  if (event.request.method !== 'GET') return;

  // Jangan cache POST atau dynamic admin/api requests jika tidak perlu
  const url = new URL(event.request.url);
  if (url.origin !== location.origin) return;

  event.respondWith(
    fetch(event.request)
      .then((networkResponse) => {
        // Cache static assets dynamically
        if (
          networkResponse.status === 200 &&
          (url.pathname.startsWith('/assets/') || url.pathname.endsWith('.png') || url.pathname.endsWith('.jpg') || url.pathname.endsWith('.css') || url.pathname.endsWith('.js'))
        ) {
          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
        }
        return networkResponse;
      })
      .catch(() => {
        return caches.match(event.request);
      })
  );
});

// =========================================================================
// PUSH NOTIFICATION LISTENER (Untuk Pengumuman Wali Santri & Siswa)
// =========================================================================
self.addEventListener('push', (event) => {
  let data = {};
  if (event.data) {
    try {
      data = event.data.json();
    } catch (e) {
      data = { body: event.data.text() };
    }
  }

  const title = data.title || 'Pengumuman Baru E-RGI Syiar Gemilang';
  const options = {
    body: data.body || 'Ada pengumuman resmi terbaru dari pengurus dan diklat Rumah Gemilang Indonesia.',
    icon: data.icon || '/assets/img/icon-192.png',
    badge: '/assets/img/icon-192.png',
    image: data.image || null,
    vibrate: [200, 100, 200, 100, 200],
    tag: data.tag || 'syiar-announcement',
    renotify: true,
    data: {
      url: data.url || '/wali',
      dateOfArrival: Date.now()
    },
    actions: [
      { action: 'open', title: 'Buka Pengumuman' },
      { action: 'close', title: 'Tutup' }
    ]
  };

  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

// Notification Click Handler
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  if (event.action === 'close') {
    return;
  }

  const targetUrl = (event.notification.data && event.notification.data.url) ? event.notification.data.url : '/';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      // Jika tab sudah terbuka, bawa ke depan dan arahkan
      for (const client of clientList) {
        if (client.url.includes(targetUrl) && 'focus' in client) {
          return client.focus();
        }
      }
      // Jika belum terbuka, buka window baru
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});

// Handle Custom Message dari Web Page (Simulasi Push Pengumuman)
self.addEventListener('message', (event) => {
  if (event.data && event.data.action === 'triggerNotification') {
    const d = event.data.payload || {};
    self.registration.showNotification(d.title || 'Pengumuman E-RGI Syiar Gemilang', {
      body: d.body || 'Pengumuman terbaru telah dipublikasikan untuk santri dan wali.',
      icon: '/assets/img/icon-192.png',
      badge: '/assets/img/icon-192.png',
      vibrate: [150, 80, 150],
      data: { url: d.url || '/wali' }
    });
  }
});

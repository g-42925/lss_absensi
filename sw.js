/**
 * LSS Absensi - Service Worker
 * Strategi: Network First untuk halaman dinamis (PHP/CI3),
 * Cache First untuk aset statis (JS, CSS, fonts, images).
 */

const CACHE_NAME = 'lss-absensi-v1';
const STATIC_CACHE = 'lss-static-v1';

// Aset statis yang di-cache saat instalasi
const PRECACHE_URLS = [
  './',
];

// Ekstensi file yang dianggap aset statis
const STATIC_EXTENSIONS = [
  '.css', '.js', '.woff', '.woff2', '.ttf', '.eot',
  '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.webp',
  '.mp3', '.mp4',
];

// URL yang harus selalu dari jaringan (dinamis / auth)
const NETWORK_ONLY_PATTERNS = [
  /\/auth/,
  /\/api\//,
  /\/attendance\/checkin/,
  /\/attendance\/checkout/,
];

// ─── Install ───────────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(PRECACHE_URLS))
      .then(() => self.skipWaiting())
  );
});

// ─── Activate ──────────────────────────────────────────────────────────────
self.addEventListener('activate', (event) => {
  const validCaches = [CACHE_NAME, STATIC_CACHE];
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => !validCaches.includes(key))
          .map((key) => caches.delete(key))
      )
    ).then(() => self.clients.claim())
  );
});

// ─── Fetch ─────────────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Hanya tangani request dari origin yang sama
  if (url.origin !== location.origin) return;

  // Abaikan request non-GET
  if (request.method !== 'GET') return;

  // Network-only untuk endpoint kritis
  if (NETWORK_ONLY_PATTERNS.some((pattern) => pattern.test(url.pathname))) {
    event.respondWith(fetch(request));
    return;
  }

  const ext = url.pathname.substring(url.pathname.lastIndexOf('.'));
  const isStatic = STATIC_EXTENSIONS.includes(ext.toLowerCase());

  if (isStatic) {
    // Cache First untuk aset statis
    event.respondWith(
      caches.open(STATIC_CACHE).then(async (cache) => {
        const cached = await cache.match(request);
        if (cached) return cached;
        try {
          const response = await fetch(request);
          if (response.ok) cache.put(request, response.clone());
          return response;
        } catch {
          return new Response('Aset tidak tersedia offline.', { status: 503 });
        }
      })
    );
  } else {
    // Network First untuk halaman dinamis (PHP/CI3)
    event.respondWith(
      caches.open(CACHE_NAME).then(async (cache) => {
        try {
          const response = await fetch(request);
          if (response.ok) cache.put(request, response.clone());
          return response;
        } catch {
          const cached = await cache.match(request);
          if (cached) return cached;
          // Offline fallback
          return new Response(
            `<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LSS Absensi - Offline</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Public Sans', system-ui, sans-serif;
      background: linear-gradient(135deg, #696cff 0%, #9155fd 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }
    .card {
      background: rgba(255,255,255,0.15);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.25);
      border-radius: 20px;
      padding: 48px 40px;
      text-align: center;
      max-width: 420px;
      width: 90%;
      box-shadow: 0 25px 50px rgba(0,0,0,0.2);
    }
    .icon { font-size: 72px; margin-bottom: 20px; }
    h1 { font-size: 24px; font-weight: 700; margin-bottom: 12px; }
    p { font-size: 15px; opacity: 0.85; line-height: 1.6; margin-bottom: 28px; }
    button {
      background: white;
      color: #696cff;
      border: none;
      border-radius: 10px;
      padding: 12px 32px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.15s, box-shadow 0.15s;
    }
    button:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
  </style>
</head>
<body>
  <div class="card">
    <div class="icon">📡</div>
    <h1>Tidak Ada Koneksi</h1>
    <p>Anda sedang offline. Periksa koneksi internet Anda, lalu coba lagi.</p>
    <button onclick="window.location.reload()">🔄 Coba Lagi</button>
  </div>
</body>
</html>`,
            { status: 200, headers: { 'Content-Type': 'text/html; charset=utf-8' } }
          );
        }
      })
    );
  }
});

// ─── Push Notification (opsional, siap pakai) ────────────────────────────
self.addEventListener('push', (event) => {
  if (!event.data) return;
  const data = event.data.json();
  self.registration.showNotification(data.title || 'LSS Absensi', {
    body: data.body || '',
    icon: './favicon.png',
    badge: './favicon.png',
    data: data.url || './',
  });
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  event.waitUntil(clients.openWindow(event.notification.data));
});

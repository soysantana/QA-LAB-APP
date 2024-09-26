const CACHE_NAME = 'v1';
const urlsToCache = [
  '/assets/vendor/bootstrap/css/bootstrap.min.css',
  '/assets/vendor/bootstrap-icons/bootstrap-icons.css',
  '/assets/vendor/boxicons/css/boxicons.min.css',
  '/assets/vendor/quill/quill.snow.css',
  '/assets/vendor/quill/quill.bubble.css',
  '/assets/vendor/remixicon/remixicon.css',
  '/assets/vendor/simple-datatables/style.css',
  '/assets/css/style.css',
  '/assets/vendor/echarts/echarts.min.js',
  '/assets/img/favicon.ico'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request)
      .then((response) => {
        return response || fetch(event.request);
      })
  );
});

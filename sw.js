var cacheName = 'MySchoolGH-PWA';
var appUrl = 'http://localhost/myschoolgh/SchoolManager/';

var dc = [
  `${appUrl}`,
  `${appUrl}main`,
  `${appUrl}assets/img/favicon.ico`,
  `${appUrl}assets/js/clock.js`,
  `${appUrl}assets/js/analitics.js`,
  `${appUrl}assets/bundles/datatables/datatables.min.js`,
  `${appUrl}assets/bundles/apexcharts/apexcharts.min.js`,
  `${appUrl}assets/js/app.min.js`,
  `${appUrl}assets/vendors/trix/trix.js`,
  `${appUrl}assets/bundles/fullcalendar/fullcalendar.min.js`,
  `${appUrl}assets/bundles/chartjs/chart.min.js`,
  `${appUrl}assets/bundles/select2/select2.js`,
  `${appUrl}assets/bundles/bootstrap-daterangepicker/daterangepicker.js`,
  `${appUrl}assets/bundles/sweetalert/sweetalert.js`
];

self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(cacheName).then((cache) => {
      return cache.addAll(dc);
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', (evt) => {
  if(evt.request.method === 'GET') {
    evt.respondWith(
      fetch(evt.request).then((response) => {
        return caches.open(cacheName).then((cache) => {
          // cache.put(evt.request, response.clone());
          return response;
        })
      }).catch((err) => {
        return caches.match(evt.request).then((resp) => {
          if(resp === undefined) {
            return caches.match(`${appUrl}assets/offline.html`);
          }
          return resp;
        })
      })
    );
  };
});
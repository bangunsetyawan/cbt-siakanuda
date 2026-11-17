// CBT SIAKANUDA — JS proktor/admin (lokal, tanpa CDN, tanpa dependensi).
// Fase 1: hanya utilitas kecil. Monitor proktor & aksi cepat hadir di Fase 3.
(function () {
    'use strict';

    // Konfirmasi untuk aksi berbahaya (dipakai lewat atribut data-confirm).
    document.addEventListener('click', function (event) {
        var el = event.target.closest('[data-confirm]');
        if (!el) {
            return;
        }
        if (!window.confirm(el.getAttribute('data-confirm'))) {
            event.preventDefault();
        }
    });
})();

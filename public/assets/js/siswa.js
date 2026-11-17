// Ujian SIAKANUDA — JS siswa (lokal, tanpa CDN, tanpa dependensi).
// Fase 1: hanya utilitas kecil. Logika ujian (timer, simpan jawaban,
// peringatan pindah tab) hadir di Fase 3.
(function () {
    'use strict';

    // Cegah klik ganda pada tombol aksi (mis. tombol masuk).
    document.addEventListener('click', function (event) {
        var el = event.target.closest('.btn');
        if (!el || el.dataset.clicked) {
            return;
        }
        el.dataset.clicked = '1';
        window.setTimeout(function () {
            delete el.dataset.clicked;
        }, 1500);
    });
})();

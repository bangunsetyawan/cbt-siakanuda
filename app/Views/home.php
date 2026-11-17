<?= $this->extend('layouts/siswa') ?>

<?= $this->section('content') ?>
<header class="hero">
    <h1>Ujian SIAKANUDA</h1>
    <p class="tagline">Ujian berbasis komputer SMK NU Darussalam</p>
</header>

<div class="actions">
    <!-- Rute /masuk (siswa) & /admin (proktor) hadir di Fase 2. -->
    <a class="btn btn-primary" href="/masuk">Masuk Siswa</a>
    <a class="btn btn-secondary" href="/admin">Masuk Proktor</a>
</div>

<p class="note">Login siswa &amp; proktor hadir di Fase 2.</p>
<?= $this->endSection() ?>

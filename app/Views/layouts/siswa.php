<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Ujian SIAKANUDA') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/siswa.css') ?>">
</head>
<body>
    <main class="container">
        <?= $this->renderSection('content') ?>
    </main>
    <script src="<?= base_url('assets/js/siswa.js') ?>"></script>
</body>
</html>

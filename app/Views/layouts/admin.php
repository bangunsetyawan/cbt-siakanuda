<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'CBT SIAKANUDA — Proktor') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
</head>
<body>
    <header class="topbar">
        <strong>CBT SIAKANUDA</strong>
        <span>Proktor</span>
    </header>
    <main class="container">
        <?= $this->renderSection('content') ?>
    </main>
    <script src="<?= base_url('assets/js/admin.js') ?>"></script>
</body>
</html>

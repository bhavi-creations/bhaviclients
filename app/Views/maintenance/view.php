<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h2><?= esc($record['title']) ?></h2>
<p><b>Description:</b> <?= esc($record['description']) ?></p>
<p><b>Created:</b> <?= esc($record['created_at']) ?></p>
<a href="<?= base_url('maintenance') ?>" class="btn btn-secondary">Back</a>
<?= $this->endSection() ?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h2><?= esc($title) ?></h2>

<?php if ($client): ?>
    <h5>Client: <?= esc($client['name']) ?></h5>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>S.No.</th>
                <th>Title</th>
                <th>Description</th>
                <th>Created</th>
                <th>Updated</th>
            </tr>
        </thead>
        <tbody>
            <?php $sno = 1; ?>
            <?php foreach ($records as $row): ?>
                <tr>
                    <td><?= $sno++ ?></td>
                    <td><?= esc($row['title']) ?></td>
                    <td><?= esc($row['description']) ?></td>
                    <td><?= esc($row['created_at']) ?></td>
                    <td><?= esc($row['updated_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div>No maintenance records found for your account.</div>
<?php endif; ?>
<?= $this->endSection() ?>
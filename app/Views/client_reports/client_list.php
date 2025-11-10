<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container mt-4">
    <h3><?= esc($title) ?></h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Report Name</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $rep): ?>
                <tr>
                    <td><?= esc($rep['title']) ?></td>
                    <td><?= date('d M Y, h:i A', strtotime($rep['created_at'])) ?></td>
                    <td>
                        <a href="<?= base_url('client/reports/view/' . $rep['id']) ?>" class="btn btn-sm btn-primary">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
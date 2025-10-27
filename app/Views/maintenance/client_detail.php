<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h2><?= esc($title) ?></h2>
<h5>Client: <?= esc($client['name']) ?></h5>
<a href="<?= base_url('maintenance/create?client_id=' . $client['id']) ?>" class="btn btn-primary mb-2">Add Maintenance</a>
<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>S.No.</th>
            <th>Title</th>
            <th>Description</th>
            <th>Created</th>
            <th>Updated</th>
            <th>Action</th>
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
                <td>
                    <a href="<?= base_url('maintenance/edit/' . $row['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                    <form action="<?= base_url('maintenance/delete/' . $row['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Delete?');">
                        <?= csrf_field() ?>
                        <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<a href="<?= base_url('maintenance') ?>" class="btn btn-secondary">Back</a>
<?= $this->endSection() ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h2><?= esc($title) ?></h2>
<a href="<?= base_url('maintenance/create') ?>" class="btn btn-primary mb-2">Add Project Details</a>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>S.No.</th>
            <th>Company</th>
            <th>Contact</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Project Records</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php $sno = 1; ?>
        <?php foreach ($clients as $client): ?>
            <tr>
                <td><?= $sno++ ?></td>
                <td><?= esc($client['name']) ?></td>
                <td><?= esc($client['owner_first_name'] . ' ' . $client['owner_last_name']) ?></td>
                <td><?= esc($client['email']) ?></td>
                <td><?= esc($client['phone']) ?></td>
                <td><?= esc($client['record_count']) ?></td>
                <td>
                    <a href="<?= base_url('maintenance/client/' . $client['id']) ?>" class="btn btn-info btn-sm">View</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
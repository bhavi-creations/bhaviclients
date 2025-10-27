<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h2>Edit Maintenance Record</h2>
<?= session()->getFlashdata('error') ? '<div class="alert alert-danger">'.session()->getFlashdata('error').'</div>' : '' ?>
<form method="post" action="<?= base_url('maintenance/update/'.$record['id']) ?>">
    <?= csrf_field() ?>
    <div class="form-group">
        <label>Client <span class="text-danger">*</span></label>
        <select name="client_id" class="form-control" required>
            <?php foreach($clients as $client): ?>
                <option value="<?= $client['id'] ?>" <?= ($client['id'] == $record['client_id'] ? 'selected' : '') ?>>
                    <?= esc($client['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" value="<?= esc($record['title']) ?>" required />
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control"><?= esc($record['description']) ?></textarea>
    </div>
    <button class="btn btn-primary" type="submit">Update</button>
    <a href="<?= base_url('maintenance') ?>" class="btn btn-secondary">Cancel</a>
</form>
<?= $this->endSection() ?>

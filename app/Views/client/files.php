<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Upload Files for: <?= esc($client['name']) ?></h1>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <?= form_open_multipart('client/upload/' . $client['id']) ?>
            <div class="form-group">
                <label for="client_files">Files to Upload (Multiple allowed)</label>
                <input type="file" name="client_files[]" multiple required
                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.xlsx">
            </div>
            <button type="submit" class="btn btn-primary">Upload Files</button>
            <?= form_close() ?>

            <hr>

            <h4>Uploaded Documents</h4>
            <?php if (!empty($clientFiles)): ?>
                <ul class="list-group">
                    <?php foreach ($clientFiles as $file): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <a href="<?= base_url('client/download/' . $file['id']) ?>" target="_blank">
                                    <?= esc($file['original_name']) ?>
                                </a>
                            </div>
                            <div>
                                <a href="<?= base_url('client/download/' . $file['id']) ?>" class="btn btn-sm btn-outline-primary" download>
                                    Download
                                </a>

                                <?= form_open('client/deleteFile/' . $file['id'], ['class' => 'd-inline-block', 'onsubmit' => "return confirm('Are you sure to delete this file?');"]) ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger ml-2">Delete</button>
                                <?= form_close() ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No files uploaded yet.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<?= $this->endSection() ?>

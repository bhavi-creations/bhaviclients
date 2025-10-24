<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= $title ?></h3>
            <div class="card-tools">
                <a href="<?= base_url('department') ?>" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
        </div>
        <div class="card-body">
            
            <?= form_open(base_url('department/store')) ?>

                <div class="form-group mb-3">
                    <label for="name">Department Name*</label>
                    <input type="text" name="name" id="name" class="form-control <?= $validation->hasError('name') ? 'is-invalid' : '' ?>" value="<?= set_value('name') ?>">
                    
                    <?php if ($validation->hasError('name')): ?>
                        <div class="invalid-feedback">
                            <?= $validation->getError('name') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label for="description">Description (Optional)</label>
                    <textarea name="description" id="description" class="form-control"><?= set_value('description') ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Department</button>

            <?= form_close() ?>

        </div>
    </div>
<?= $this->endSection() ?>
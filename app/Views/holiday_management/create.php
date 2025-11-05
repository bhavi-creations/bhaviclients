<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-plus"></i> <?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('holidays') ?>">Holidays</a></li>
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?= view('flash_messages') ?>

            <div class="card card-primary">
                <?= form_open('holidays/store') ?>
                <div class="card-body">
                    
                    <div class="form-group">
                        <label>Holiday Name <span class="text-danger">*</span></label>
                        <input type="text" name="holiday_name" class="form-control" required value="<?= old('holiday_name') ?>">
                        <?php if ($validation && $validation->hasError('holiday_name')): ?>
                            <small class="text-danger"><?= $validation->getError('holiday_name') ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" name="holiday_date" class="form-control" required value="<?= old('holiday_date') ?>">
                        <?php if ($validation && $validation->hasError('holiday_date')): ?>
                            <small class="text-danger"><?= $validation->getError('holiday_date') ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= old('description') ?></textarea>
                        <?php if ($validation && $validation->hasError('description')): ?>
                            <small class="text-danger"><?= $validation->getError('description') ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" name="is_recurring" value="1" class="form-check-input" id="recurringCheck" <?= old('is_recurring') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="recurringCheck">Is this a recurring holiday?</label>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Holiday</button>
                    <a href="<?= base_url('holidays') ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                </div>
                <?= form_close() ?>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>

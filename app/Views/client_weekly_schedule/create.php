<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\client_weekly_schedule\create.php 

$session = \Config\Services::session();
$hasValidationErrors = isset($validation) && is_object($validation);
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('weekly-schedule') ?>">Weekly Schedules</a></li>
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php if ($session->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $session->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if ($hasValidationErrors && $validation->getErrors()): ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Validation Errors:</h5>
                    <ul class="mb-0">
                        <?php foreach ($validation->getErrors() as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <?= form_open('weekly-schedule/store') ?> 

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">Schedule Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Client Selection -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_id">Select Client <span class="text-danger">*</span></label>
                                        <select id="client_id" 
                                                name="client_id" 
                                                class="form-control select2 <?= $hasValidationErrors && $validation->hasError('client_id') ? 'is-invalid' : '' ?>" 
                                                required>
                                            <option value="">-- Select Client --</option>
                                            <?php foreach ($clients as $client): ?>
                                                <option value="<?= esc($client['id']) ?>" <?= (old('client_id') == $client['id']) ? 'selected' : '' ?>>
                                                    <?= esc($client['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if ($hasValidationErrors && $validation->hasError('client_id')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('client_id') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Week Start Date -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="week_start_date">Week Start Date (Monday) <span class="text-danger">*</span></label>
                                        <input type="date" 
                                               class="form-control <?= $hasValidationErrors && $validation->hasError('week_start_date') ? 'is-invalid' : '' ?>" 
                                               id="week_start_date" 
                                               name="week_start_date" 
                                               value="<?= old('week_start_date') ?>" 
                                               required>
                                        <small class="form-text text-muted">Select Monday of the week</small>
                                        <?php if ($hasValidationErrors && $validation->hasError('week_start_date')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('week_start_date') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" required>
                                            <option value="draft" <?= old('status', 'draft') == 'draft' ? 'selected' : '' ?>>Draft</option>
                                            <option value="published" <?= old('status') == 'published' ? 'selected' : '' ?>>Published</option>
                                            <option value="archived" <?= old('status') == 'archived' ? 'selected' : '' ?>>Archived</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Department Columns -->
                            <div class="form-group">
                                <label>Department Columns (Enter up to 7 departments)</label>
                                <div class="row" id="departmentColumns">
                                    <?php foreach ($defaultDepartments as $index => $dept): ?>
                                        <div class="col-md-3 mb-2">
                                            <input type="text" 
                                                   name="dept_<?= $index ?>" 
                                                   class="form-control" 
                                                   placeholder="Department <?= $index + 1 ?>" 
                                                   value="<?= old('dept_' . $index, $dept) ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" 
                                          id="remarks" 
                                          name="remarks" 
                                          rows="2"><?= old('remarks') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weekly Schedule Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title">Weekly Work Schedule</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="120">Day</th>
                                            <?php foreach ($defaultDepartments as $index => $dept): ?>
                                                <th class="dept-header" id="header_<?= $index ?>">
                                                    <span class="dept-name"><?= esc($dept) ?></span>
                                                </th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($days as $dayIndex => $day): ?>
                                            <tr>
                                                <td class="font-weight-bold bg-light"><?= $day ?></td>
                                                <?php for ($deptIndex = 0; $deptIndex < 7; $deptIndex++): ?>
                                                    <td>
                                                        <textarea 
                                                            name="task_<?= $dayIndex ?>_<?= $deptIndex ?>" 
                                                            class="form-control form-control-sm"
                                                            rows="2"
                                                            placeholder="Enter tasks..."><?= old('task_' . $dayIndex . '_' . $deptIndex) ?></textarea>
                                                    </td>
                                                <?php endfor; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Schedule
                            </button>
                            <a href="<?= base_url('weekly-schedule') ?>" class="btn btn-secondary float-right">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>

            <?= form_close() ?>

        </div>
    </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#client_id').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: '-- Select Client --',
            allowClear: true
        });

        // Update table headers when department names change
        for (let i = 0; i < 7; i++) {
            $('input[name="dept_' + i + '"]').on('input', function() {
                let newName = $(this).val() || 'Department ' + (i + 1);
                $('#header_' + i + ' .dept-name').text(newName);
            });
        }
    });
</script>
<?= $this->endSection() ?>

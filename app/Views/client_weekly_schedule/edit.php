<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\client_weekly_schedule\edit.php 

$session = \Config\Services::session();
$validation = $validation ?? \Config\Services::validation();
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

function get_value($field, $schedule_data, $default = '') {
    return old($field) !== null ? old($field) : ($schedule_data[$field] ?? $default);
}
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
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php if ($session->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $session->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if ($session->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $session->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if ($validation->getErrors()): ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <strong>Validation Error!</strong> Please correct the errors below.
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <?= form_open(base_url('weekly-schedule/update/' . $schedule['id'])) ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">Edit Schedule Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Client Selection -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_id">Client <span class="text-danger">*</span></label>
                                        <select id="client_id" 
                                                name="client_id" 
                                                class="form-control select2 <?= $validation->hasError('client_id') ? 'is-invalid' : '' ?>" 
                                                required>
                                            <option value="">-- Select Client --</option>
                                            <?php 
                                            $currentClientId = get_value('client_id', $schedule);
                                            foreach ($clients as $client): ?>
                                                <option value="<?= esc($client['id']) ?>" <?= $currentClientId == $client['id'] ? 'selected' : '' ?>>
                                                    <?= esc($client['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if ($validation->hasError('client_id')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('client_id') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Week Start Date -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="week_start_date">Week Start Date <span class="text-danger">*</span></label>
                                        <input type="date" 
                                               class="form-control <?= $validation->hasError('week_start_date') ? 'is-invalid' : '' ?>" 
                                               id="week_start_date" 
                                               name="week_start_date" 
                                               value="<?= esc(get_value('week_start_date', $schedule)) ?>" 
                                               required>
                                        <?php if ($validation->hasError('week_start_date')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('week_start_date') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" required>
                                            <?php $currentStatus = get_value('status', $schedule); ?>
                                            <option value="draft" <?= $currentStatus == 'draft' ? 'selected' : '' ?>>Draft</option>
                                            <option value="published" <?= $currentStatus == 'published' ? 'selected' : '' ?>>Published</option>
                                            <option value="archived" <?= $currentStatus == 'archived' ? 'selected' : '' ?>>Archived</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Department Columns -->
                            <div class="form-group">
                                <label>Department Columns</label>
                                <div class="row" id="departmentColumns">
                                    <?php foreach ($departments as $index => $dept): ?>
                                        <div class="col-md-3 mb-2">
                                            <input type="text" 
                                                   name="dept_<?= $index ?>" 
                                                   class="form-control dept-input" 
                                                   placeholder="Department <?= $index + 1 ?>" 
                                                   value="<?= old('dept_' . $index, esc($dept)) ?>"
                                                   data-index="<?= $index ?>">
                                        </div>
                                    <?php endforeach; ?>
                                    <?php 
                                    // Add empty inputs if less than 7 departments
                                    for ($i = count($departments); $i < 7; $i++): 
                                    ?>
                                        <div class="col-md-3 mb-2">
                                            <input type="text" 
                                                   name="dept_<?= $i ?>" 
                                                   class="form-control dept-input" 
                                                   placeholder="Department <?= $i + 1 ?>" 
                                                   value="<?= old('dept_' . $i, '') ?>"
                                                   data-index="<?= $i ?>">
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control" 
                                          id="remarks" 
                                          name="remarks" 
                                          rows="2"><?= esc(get_value('remarks', $schedule)) ?></textarea>
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
                                            <?php for ($i = 0; $i < 7; $i++): ?>
                                                <th class="dept-header" id="header_<?= $i ?>">
                                                    <span class="dept-name"><?= esc($departments[$i] ?? 'Department ' . ($i + 1)) ?></span>
                                                </th>
                                            <?php endfor; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($days as $dayIndex => $day): ?>
                                            <tr>
                                                <td class="font-weight-bold bg-light"><?= $day ?></td>
                                                <?php for ($deptIndex = 0; $deptIndex < 7; $deptIndex++): ?>
                                                    <td>
                                                        <?php 
                                                        $deptName = $departments[$deptIndex] ?? '';
                                                        $taskValue = '';
                                                        if (!empty($deptName) && isset($scheduleData[$day][$deptName])) {
                                                            $taskValue = $scheduleData[$day][$deptName];
                                                        }
                                                        ?>
                                                        <textarea 
                                                            name="task_<?= $dayIndex ?>_<?= $deptIndex ?>" 
                                                            class="form-control form-control-sm"
                                                            rows="2"
                                                            placeholder="Enter tasks..."><?= old('task_' . $dayIndex . '_' . $deptIndex, esc($taskValue)) ?></textarea>
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
                                <i class="fas fa-save"></i> Update Schedule
                            </button>
                            <a href="<?= base_url('weekly-schedule') ?>" class="btn btn-default float-right">Cancel</a>
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
        $('.dept-input').on('input', function() {
            let index = $(this).data('index');
            let newName = $(this).val() || 'Department ' + (index + 1);
            $('#header_' + index + ' .dept-name').text(newName);
        });
    });
</script>
<?= $this->endSection() ?>

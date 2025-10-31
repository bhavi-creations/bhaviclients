<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\employee\view.php 
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Employee Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('employee') ?>">Employees</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Employee Information Card -->
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <div class="profile-user-img img-circle bg-primary d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px;">
                                    <h1 class="text-white mb-0">
                                        <?= strtoupper(substr($employee['first_name'], 0, 1) . substr($employee['last_name'], 0, 1)) ?>
                                    </h1>
                                </div>
                            </div>

                            <h3 class="profile-username text-center mt-3">
                                <?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?>
                            </h3>

                            <p class="text-muted text-center"><?= esc($employee['role_name'] ?? 'Employee') ?></p>

                            <?php if (!empty($employee['employee_code'])): ?>
                                <p class="text-center">
                                    <span class="badge badge-info badge-lg">
                                        <i class="fas fa-id-badge"></i> <?= esc($employee['employee_code']) ?>
                                    </span>
                                </p>
                            <?php endif; ?>

                            <p class="text-center">
                                <?php if ($employee['status'] == 'active'): ?>
                                    <span class="badge badge-success badge-lg">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary badge-lg">Inactive</span>
                                <?php endif; ?>
                            </p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b><i class="fas fa-envelope mr-2"></i> Email</b>
                                    <a class="float-right"><?= esc($employee['email']) ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-phone mr-2"></i> Phone</b>
                                    <a class="float-right"><?= esc($employee['phone']) ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-building mr-2"></i> Department</b>
                                    <a class="float-right"><?= esc($employee['department_name'] ?? 'N/A') ?></a>
                                </li>
                                <?php if (!empty($employee['date_of_joining'])): ?>
                                    <li class="list-group-item">
                                        <b><i class="fas fa-calendar mr-2"></i> Joining Date</b>
                                        <a class="float-right"><?= date('M d, Y', strtotime($employee['date_of_joining'])) ?></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (!empty($employee['current_salary'])): ?>
                                    <li class="list-group-item">
                                        <b><i class="fas fa-rupee-sign mr-2"></i> Current Salary</b>
                                        <a class="float-right"><strong>₹<?= number_format($employee['current_salary'], 2) ?></strong></a>
                                    </li>
                                <?php endif; ?>
                            </ul>

                            <a href="<?= base_url('employee/edit/' . $employee['id']) ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-edit"></i> Edit Employee
                            </a>
                        </div>
                    </div>

                    <!-- Parent Information -->
                    <?php if (!empty($employee['parent_name']) || !empty($employee['parent_phone'])): ?>
                        <div class="card">
                            <div class="card-header bg-info">
                                <h3 class="card-title"><i class="fas fa-users"></i> Parent/Guardian Info</h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($employee['parent_name'])): ?>
                                    <p><strong>Name:</strong> <?= esc($employee['parent_name']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($employee['parent_phone'])): ?>
                                    <p><strong>Phone:</strong> <?= esc($employee['parent_phone']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Details and History -->
                <div class="col-md-8">
                    
                    <!-- Salary Information Card -->
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title"><i class="fas fa-rupee-sign"></i> Salary Information</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#addSalaryModal">
                                    <i class="fas fa-plus"></i> Add Salary/Increment
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($salaryStats)): ?>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="info-box bg-info">
                                            <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Current</span>
                                                <span class="info-box-number">₹<?= number_format($salaryStats['current_salary'], 2) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-secondary">
                                            <span class="info-box-icon"><i class="fas fa-hand-holding-usd"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Initial</span>
                                                <span class="info-box-number">₹<?= number_format($salaryStats['initial_salary'], 2) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Increase</span>
                                                <span class="info-box-number">₹<?= number_format($salaryStats['total_increase'], 2) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-warning">
                                            <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Growth</span>
                                                <span class="info-box-number"><?= number_format($salaryStats['total_percentage'], 2) ?>%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-center text-muted">No salary information available</p>
                            <?php endif; ?>

                            <!-- Salary History Table -->
                            <?php if (!empty($salaryHistory)): ?>
                                <h5 class="mt-4 mb-3">Salary History</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Type</th>
                                                <th>Increment</th>
                                                <th>Reason</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($salaryHistory as $salary): ?>
                                                <tr>
                                                    <td><?= date('M d, Y', strtotime($salary['effective_date'])) ?></td>
                                                    <td><strong>₹<?= number_format($salary['salary_amount'], 2) ?></strong></td>
                                                    <td>
                                                        <span class="badge badge-<?= $salary['increment_type'] == 'initial' ? 'secondary' : 'primary' ?>">
                                                            <?= ucfirst(str_replace('_', ' ', $salary['increment_type'])) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($salary['increment_percentage'])): ?>
                                                            <span class="badge badge-success">+<?= number_format($salary['increment_percentage'], 2) ?>%</span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= esc($salary['reason'] ?? '-') ?></td>
                                                    <td>
                                                        <?php if ($salary['increment_type'] != 'initial'): ?>
                                                            <button type="button" 
                                                                    class="btn btn-warning btn-sm" 
                                                                    data-toggle="modal" 
                                                                    data-target="#editSalaryModal<?= $salary['id'] ?>"
                                                                    title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <?= form_open('employee/deleteSalary/' . $salary['id'], ['class' => 'd-inline', 'onsubmit' => "return confirm('Delete this salary record?')"]) ?>
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            <?= form_close() ?>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Uploaded Files Card -->
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fas fa-folder-open"></i> Employee Documents</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#uploadFilesModal">
                                    <i class="fas fa-cloud-upload-alt"></i> Upload Files
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($files)): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>File Name</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $sno = 1; ?>
                                            <?php foreach ($files as $file): ?>
                                                <tr>
                                                    <td><?= $sno++ ?></td>
                                                    <td>
                                                        <i class="fas fa-file mr-2"></i>
                                                        <?= esc($file) ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('employee/downloadFile/' . $employee['id'] . '/' . $file) ?>" 
                                                           class="btn btn-info btn-sm" 
                                                           title="Download">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <?= form_open('employee/deleteFile/' . $employee['id'] . '/' . $file, ['class' => 'd-inline', 'onsubmit' => "return confirm('Delete this file?')"]) ?>
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        <?= form_close() ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center text-muted">No files uploaded yet</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Remarks Card -->
                    <?php if (!empty($employee['remarks'])): ?>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-sticky-note"></i> Remarks</h3>
                            </div>
                            <div class="card-body">
                                <?= nl2br(esc($employee['remarks'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Back Button -->
            <a href="<?= base_url('employee') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Employee List
            </a>

        </div>
    </section>
</div>

<!-- Add Salary Modal -->
<div class="modal fade" id="addSalaryModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-white">Add Salary/Increment</h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <?= form_open('employee/addSalary/' . $employee['id']) ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Salary Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="salary_amount" class="form-control" step="0.01" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Effective Date <span class="text-danger">*</span></label>
                            <input type="date" name="effective_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Increment Type <span class="text-danger">*</span></label>
                    <select name="increment_type" class="form-control" required>
                        <option value="increment">Regular Increment</option>
                        <option value="promotion">Promotion</option>
                        <option value="annual_review">Annual Review</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Reason</label>
                    <textarea name="reason" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Add Salary Record
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Upload Files Modal -->
<div class="modal fade" id="uploadFilesModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-white">Upload Documents</h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <?= form_open_multipart('employee/uploadFiles/' . $employee['id']) ?>
            <div class="modal-body">
                <div class="form-group">
                    <label>Select Files (Multiple allowed)</label>
                    <div class="custom-file">
                        <input type="file" name="employee_files[]" class="custom-file-input" id="uploadFiles" multiple required>
                        <label class="custom-file-label" for="uploadFiles">Choose files...</label>
                    </div>
                    <small class="form-text text-muted">Allowed: PDF, DOC, DOCX, Images, ZIP</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Edit Salary Modals (One for each salary record) -->
<?php if (!empty($salaryHistory)): ?>
    <?php foreach ($salaryHistory as $salary): ?>
        <?php if ($salary['increment_type'] != 'initial'): ?>
        <div class="modal fade" id="editSalaryModal<?= $salary['id'] ?>">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h4 class="modal-title">Edit Salary Record</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <?= form_open('employee/editSalary/' . $salary['id']) ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Salary Amount (₹) <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           name="salary_amount" 
                                           class="form-control" 
                                           step="0.01" 
                                           value="<?= $salary['salary_amount'] ?>" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Effective Date <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           name="effective_date" 
                                           class="form-control" 
                                           value="<?= $salary['effective_date'] ?>" 
                                           required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Increment Type <span class="text-danger">*</span></label>
                            <select name="increment_type" class="form-control" required>
                                <option value="increment" <?= $salary['increment_type'] == 'increment' ? 'selected' : '' ?>>Regular Increment</option>
                                <option value="promotion" <?= $salary['increment_type'] == 'promotion' ? 'selected' : '' ?>>Promotion</option>
                                <option value="annual_review" <?= $salary['increment_type'] == 'annual_review' ? 'selected' : '' ?>>Annual Review</option>
                                <option value="adjustment" <?= $salary['increment_type'] == 'adjustment' ? 'selected' : '' ?>>Adjustment</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Reason</label>
                            <textarea name="reason" class="form-control" rows="2"><?= esc($salary['reason'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Update Salary Record
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<script>
    // Update file input label
    $('#uploadFiles').on('change', function(e) {
        var files = e.target.files;
        var label = $(this).next('.custom-file-label');
        if (files.length > 0) {
            label.text(files.length === 1 ? files[0].name : files.length + ' files selected');
        } else {
            label.text('Choose files...');
        }
    });
</script>

<?= $this->endSection() ?>

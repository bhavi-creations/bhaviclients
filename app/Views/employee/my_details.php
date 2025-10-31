<?php
// C:\xampp\htdocs\bhaviclients\app\Views\employee\my_details.php
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
                        <li class="breadcrumb-item"><a href="<?= base_url('employee-dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

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
                <!-- Personal Information -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user"></i> Personal Information</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-5">Employee Code:</dt>
                                <dd class="col-sm-7"><?= esc($employee['employee_code'] ?? 'N/A') ?></dd>

                                <dt class="col-sm-5">Name:</dt>
                                <dd class="col-sm-7"><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></dd>

                                <dt class="col-sm-5">Email:</dt>
                                <dd class="col-sm-7"><?= esc($employee['email']) ?></dd>

                                <dt class="col-sm-5">Phone:</dt>
                                <dd class="col-sm-7"><?= esc($employee['phone'] ?? 'N/A') ?></dd>

                                <dt class="col-sm-5">Department:</dt>
                                <dd class="col-sm-7"><?= esc($employee['department_name'] ?? 'N/A') ?></dd>

                                <dt class="col-sm-5">Join Date:</dt>
                                <dd class="col-sm-7"><?= !empty($employee['date_of_joining']) ? date('M d, Y', strtotime($employee['date_of_joining'])) : 'N/A' ?></dd>

                                <dt class="col-sm-5">Status:</dt>
                                <dd class="col-sm-7">
                                    <?php if ($employee['status'] == 'active'): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </dd>

                                <?php if (!empty($employee['parent_name'])): ?>
                                    <dt class="col-sm-5">Parent Name:</dt>
                                    <dd class="col-sm-7"><?= esc($employee['parent_name']) ?></dd>
                                <?php endif; ?>

                                <?php if (!empty($employee['parent_phone'])): ?>
                                    <dt class="col-sm-5">Parent Phone:</dt>
                                    <dd class="col-sm-7"><?= esc($employee['parent_phone']) ?></dd>
                                <?php endif; ?>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Salary Information -->
                <div class="col-md-6">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-money-bill-wave"></i> Current Salary</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($latestSalary)): ?>
                                <dl class="row">
                                    <dt class="col-sm-6">Salary Amount:</dt>
                                    <dd class="col-sm-6">
                                        <strong class="text-success">₹<?= number_format($latestSalary['salary_amount'], 2) ?></strong>
                                    </dd>

                                    <dt class="col-sm-6">Effective Date:</dt>
                                    <dd class="col-sm-6"><?= date('M d, Y', strtotime($latestSalary['effective_date'])) ?></dd>

                                    <dt class="col-sm-6">Increment Type:</dt>
                                    <dd class="col-sm-6">
                                        <span class="badge badge-info"><?= ucwords(str_replace('_', ' ', $latestSalary['increment_type'])) ?></span>
                                    </dd>

                                    <?php if (!empty($latestSalary['increment_percentage'])): ?>
                                        <dt class="col-sm-6">Increment %:</dt>
                                        <dd class="col-sm-6">
                                            <span class="badge badge-success"><?= $latestSalary['increment_percentage'] ?>%</span>
                                        </dd>
                                    <?php endif; ?>

                                    <?php if (!empty($latestSalary['previous_salary'])): ?>
                                        <dt class="col-sm-6">Previous Salary:</dt>
                                        <dd class="col-sm-6">₹<?= number_format($latestSalary['previous_salary'], 2) ?></dd>
                                    <?php endif; ?>

                                    <?php if (!empty($latestSalary['reason'])): ?>
                                        <dt class="col-sm-12 mt-2">Reason:</dt>
                                        <dd class="col-sm-12">
                                            <div class="alert alert-info mb-0">
                                                <?= esc($latestSalary['reason']) ?>
                                            </div>
                                        </dd>
                                    <?php endif; ?>
                                </dl>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Salary information not available
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Salary History Card -->
            <?php if (!empty($salaryHistory) && count($salaryHistory) > 1): ?>
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-history"></i> Salary History</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Effective Date</th>
                                        <th>Salary Amount</th>
                                        <th>Type</th>
                                        <th>Increment %</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($salaryHistory as $history): ?>
                                        <tr>
                                            <td><?= date('M d, Y', strtotime($history['effective_date'])) ?></td>
                                            <td><strong>₹<?= number_format($history['salary_amount'], 2) ?></strong></td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?= ucwords(str_replace('_', ' ', $history['increment_type'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($history['increment_percentage'])): ?>
                                                    <span class="badge badge-success"><?= $history['increment_percentage'] ?>%</span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($history['reason'] ?? '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Remarks Card -->
            <?php if (!empty($employee['remarks'])): ?>
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-comment-alt"></i> Admin Remarks</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-0">
                            <?= nl2br(esc($employee['remarks'])) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Uploaded Files Card -->
            <?php if (!empty($uploadedFiles) && is_array($uploadedFiles)): ?>
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-paperclip"></i> My Documents (<?= count($uploadedFiles) ?>)</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($uploadedFiles as $index => $file): ?>
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center p-3">
                                            <?php
                                            $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                            $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                            ?>
                                            <?php if (in_array($fileExt, $imageExts)): ?>
                                                <i class="fas fa-image fa-3x text-info mb-2"></i>
                                            <?php elseif ($fileExt == 'pdf'): ?>
                                                <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                            <?php elseif (in_array($fileExt, ['doc', 'docx'])): ?>
                                                <i class="fas fa-file-word fa-3x text-primary mb-2"></i>
                                            <?php elseif (in_array($fileExt, ['xls', 'xlsx'])): ?>
                                                <i class="fas fa-file-excel fa-3x text-success mb-2"></i>
                                            <?php else: ?>
                                                <i class="fas fa-file fa-3x text-secondary mb-2"></i>
                                            <?php endif; ?>
                                            <p class="mb-2 text-truncate" style="font-size: 12px;" title="<?= esc($file) ?>">
                                                <strong><?= esc(strlen($file) > 20 ? substr(basename($file), 0, 20) . '...' : basename($file)) ?></strong>
                                            </p>
                                            <a href="<?= base_url('my-details/download-file/' . $index) ?>" 
                                               class="btn btn-sm btn-primary btn-block">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<?= $this->endSection() ?>

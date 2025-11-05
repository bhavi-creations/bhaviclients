<?php
// C:\xampp\htdocs\bhaviclients\app\Views\employee\leave\apply.php
use App\Models\LeaveRequestModel;
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-file-medical"></i> <?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('my-leaves') ?>">My Leaves</a></li>
                        <li class="breadcrumb-item active">Apply</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Flash Messages -->
            <?= view('flash_messages') ?>

            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-edit"></i> Leave Application Form</h3>
                        </div>
                        <?= form_open('my-leaves/store') ?>
                        <div class="card-body">
                            
                            <div class="form-group">
                                <label>Leave Type <span class="text-danger">*</span></label>
                                <select name="leave_type" class="form-control" required>
                                    <option value="">-- Select Leave Type --</option>
                                    <?php 
                                    $leaveTypes = LeaveRequestModel::getLeaveTypes();
                                    foreach ($leaveTypes as $key => $value): 
                                    ?>
                                        <option value="<?= $key ?>" <?= old('leave_type') == $key ? 'selected' : '' ?>>
                                            <?= esc($value) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($validation && $validation->hasError('leave_type')): ?>
                                    <small class="text-danger"><?= $validation->getError('leave_type') ?></small>
                                <?php endif; ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Start Date <span class="text-danger">*</span></label>
                                        <input type="date" 
                                               name="start_date" 
                                               class="form-control" 
                                               value="<?= old('start_date') ?>" 
                                               min="<?= date('Y-m-d') ?>"
                                               required>
                                        <?php if ($validation && $validation->hasError('start_date')): ?>
                                            <small class="text-danger"><?= $validation->getError('start_date') ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>End Date <span class="text-danger">*</span></label>
                                        <input type="date" 
                                               name="end_date" 
                                               class="form-control" 
                                               value="<?= old('end_date') ?>" 
                                               min="<?= date('Y-m-d') ?>"
                                               required>
                                        <?php if ($validation && $validation->hasError('end_date')): ?>
                                            <small class="text-danger"><?= $validation->getError('end_date') ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Reason for Leave <span class="text-danger">*</span></label>
                                <textarea name="reason" 
                                          class="form-control" 
                                          rows="5" 
                                          placeholder="Please provide a detailed reason for your leave request..." 
                                          required><?= old('reason') ?></textarea>
                                <small class="text-muted">Minimum 10 characters required</small>
                                <?php if ($validation && $validation->hasError('reason')): ?>
                                    <small class="text-danger d-block"><?= $validation->getError('reason') ?></small>
                                <?php endif; ?>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Note:</strong> Your leave request will be sent to admin for approval. 
                                You will be notified once it's reviewed.
                            </div>

                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Leave Request
                            </button>
                            <a href="<?= base_url('my-leaves') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>

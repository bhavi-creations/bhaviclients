<?php
// C:\xampp\htdocs\bhaviclients\app\Views\employee\leave\view.php
use App\Models\LeaveRequestModel;
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
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('my-leaves') ?>">My Leaves</a></li>
                        <li class="breadcrumb-item active">View</li>
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
                    <div class="card shadow-lg">
                        <div class="card-header <?= $leave['status'] == 'pending' ? 'bg-warning' : ($leave['status'] == 'approved' ? 'bg-success' : 'bg-danger') ?>">
                            <h3 class="card-title text-white">
                                <i class="fas fa-file-alt"></i>
                                <strong>Leave Request Details</strong>
                            </h3>
                            <div class="card-tools">
                                <span class="badge badge-dark badge-lg"><?= strtoupper($leave['status']) ?></span>
                            </div>
                        </div>
                        <div class="card-body">
                            
                            <!-- Leave Type & Days -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-calendar-check"></i> Leave Details</h5>
                                    <?php $leaveTypes = LeaveRequestModel::getLeaveTypes(); ?>
                                    <p><strong>Leave Type:</strong> 
                                        <span class="badge badge-info badge-lg"><?= esc($leaveTypes[$leave['leave_type']]) ?></span>
                                    </p>
                                    <p><strong>Total Days:</strong> 
                                        <span class="badge badge-primary badge-lg"><?= $leave['total_days'] ?> day(s)</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-info-circle"></i> Status</h5>
                                    <p><strong>Current Status:</strong> 
                                        <span class="badge badge-<?= $leave['status'] == 'pending' ? 'warning' : ($leave['status'] == 'approved' ? 'success' : 'danger') ?> badge-lg">
                                            <?= ucfirst($leave['status']) ?>
                                        </span>
                                    </p>
                                    <p><strong>Applied On:</strong> <?= date('d M Y, h:i A', strtotime($leave['created_at'])) ?></p>
                                </div>
                            </div>

                            <hr>

                            <!-- Duration -->
                            <div class="mb-4">
                                <h5 class="mb-3"><i class="fas fa-calendar-alt"></i> Leave Duration</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-primary"><i class="fas fa-calendar-day"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Start Date</span>
                                                <span class="info-box-number"><?= date('d M Y', strtotime($leave['start_date'])) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-primary"><i class="fas fa-calendar-day"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">End Date</span>
                                                <span class="info-box-number"><?= date('d M Y', strtotime($leave['end_date'])) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reason -->
                            <div class="mb-4">
                                <h5 class="mb-3"><i class="fas fa-comment-dots"></i> Reason for Leave</h5>
                                <div class="alert alert-light border">
                                    <?= nl2br(esc($leave['reason'])) ?>
                                </div>
                            </div>

                            <!-- Admin Remarks -->
                            <?php if (!empty($leave['admin_remarks'])): ?>
                                <div class="mb-3">
                                    <h5 class="mb-3"><i class="fas fa-sticky-note"></i> Admin Remarks</h5>
                                    <div class="alert alert-<?= $leave['status'] == 'approved' ? 'success' : 'danger' ?>">
                                        <?= nl2br(esc($leave['admin_remarks'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                        <div class="card-footer">
                            <a href="<?= base_url('my-leaves') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to My Leaves
                            </a>
                            
                            <?php if ($leave['status'] == 'pending'): ?>
                                <a href="<?= base_url('my-leaves/edit/' . $leave['id']) ?>" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit Request
                                </a>
                                <a href="<?= base_url('my-leaves/delete/' . $leave['id']) ?>" 
                                   class="btn btn-danger float-right" 
                                   onclick="return confirm('Delete this leave request?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
 
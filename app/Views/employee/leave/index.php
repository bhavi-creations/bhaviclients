<?php
// C:\xampp\htdocs\bhaviclients\app\Views\employee\leave\index.php
use App\Models\LeaveRequestModel;
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-calendar-check"></i> <?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Flash Messages -->
            <?= view('flash_messages') ?>

            <div class="card shadow-lg">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fas fa-list"></i> My Leave Requests</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('my-leaves/apply') ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Apply for Leave
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($leaves)): ?>
                        <div class="row p-3">
                            <?php 
                            $leaveTypes = LeaveRequestModel::getLeaveTypes();
                            foreach ($leaves as $leave): 
                            ?>
                                <div class="col-md-6">
                                    <div class="card shadow-sm">
                                        <div class="card-header <?= $leave['status'] == 'pending' ? 'bg-warning' : ($leave['status'] == 'approved' ? 'bg-success' : 'bg-danger') ?>">
                                            <h3 class="card-title text-white">
                                                <i class="fas fa-calendar"></i>
                                                <strong><?= esc($leaveTypes[$leave['leave_type']]) ?></strong>
                                            </h3>
                                            <div class="card-tools">
                                                <span class="badge badge-dark"><?= ucfirst($leave['status']) ?></span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                <strong>Duration:</strong><br>
                                                <i class="fas fa-calendar-day"></i> 
                                                <?= date('d M Y', strtotime($leave['start_date'])) ?> 
                                                <strong>to</strong> 
                                                <?= date('d M Y', strtotime($leave['end_date'])) ?>
                                                <br>
                                                <span class="badge badge-info mt-1"><?= $leave['total_days'] ?> day(s)</span>
                                            </div>
                                            
                                            <div class="mb-2">
                                                <strong>Reason:</strong><br>
                                                <small><?= esc($leave['reason']) ?></small>
                                            </div>
                                            
                                            <?php if (!empty($leave['admin_remarks'])): ?>
                                                <div class="alert alert-light mb-2">
                                                    <strong>Admin Remarks:</strong><br>
                                                    <small><?= esc($leave['admin_remarks']) ?></small>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <p class="text-muted mb-0">
                                                <small>
                                                    <i class="fas fa-clock"></i> 
                                                    Applied: <?= date('d M Y, h:i A', strtotime($leave['created_at'])) ?>
                                                </small>
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            <div class="btn-group d-flex" role="group">
                                                <a href="<?= base_url('my-leaves/view/' . $leave['id']) ?>" 
                                                   class="btn btn-info btn-sm flex-fill">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <?php if ($leave['status'] == 'pending'): ?>
                                                    <a href="<?= base_url('my-leaves/edit/' . $leave['id']) ?>" 
                                                       class="btn btn-warning btn-sm flex-fill">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="<?= base_url('my-leaves/delete/' . $leave['id']) ?>" 
                                                       class="btn btn-danger btn-sm flex-fill" 
                                                       onclick="return confirm('Delete this leave request?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-calendar-times fa-4x mb-3"></i>
                            <h5>No Leave Requests Yet</h5>
                            <p>You haven't applied for any leaves</p>
                            <a href="<?= base_url('my-leaves/apply') ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Apply for Leave
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>

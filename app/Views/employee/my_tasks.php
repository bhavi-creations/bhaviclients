<?php
// C:\xampp\htdocs\bhaviclients\app\Views\employee\my_tasks.php
$request = \Config\Services::request();
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
                        <li class="breadcrumb-item active">My Tasks</li>
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
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Filter Card -->
            <div class="card card-primary collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">Filter Tasks</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?= form_open(base_url('my-tasks'), ['method' => 'get']) ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="Pending" <?= ($request->getGet('status') == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="In Progress" <?= ($request->getGet('status') == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                                    <option value="Completed" <?= ($request->getGet('status') == 'Completed') ? 'selected' : '' ?>>Completed</option>
                                    <option value="Review" <?= ($request->getGet('status') == 'Review') ? 'selected' : '' ?>>Review</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Priority</label>
                                <select name="priority" class="form-control">
                                    <option value="">All Priority</option>
                                    <option value="Low" <?= ($request->getGet('priority') == 'Low') ? 'selected' : '' ?>>Low</option>
                                    <option value="Medium" <?= ($request->getGet('priority') == 'Medium') ? 'selected' : '' ?>>Medium</option>
                                    <option value="High" <?= ($request->getGet('priority') == 'High') ? 'selected' : '' ?>>High</option>
                                    <option value="Urgent" <?= ($request->getGet('priority') == 'Urgent') ? 'selected' : '' ?>>Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Apply Filter
                                </button>
                                <a href="<?= base_url('my-tasks') ?>" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>

            <!-- Tasks Card -->
            <div class="card shadow-lg">
                <div class="card-header border-0">
                    <h3 class="card-title">My Assigned Tasks</h3>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($tasks)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Title</th>
                                        <th>Client</th>
                                        <th>Priority</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; ?>
                                    <?php foreach ($tasks as $task): ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td><strong><?= esc($task['title']) ?></strong></td>
                                            <td><?= esc($task['client_name'] ?? 'N/A') ?></td>
                                            <td>
                                                <?php
                                                $priorityBadge = [
                                                    'Low' => 'secondary',
                                                    'Medium' => 'info',
                                                    'High' => 'warning',
                                                    'Urgent' => 'danger'
                                                ];
                                                $badge = $priorityBadge[$task['priority']] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $badge ?>"><?= esc($task['priority']) ?></span>
                                            </td>
                                            <td>
                                                <?php if (!empty($task['due_date'])): ?>
                                                    <?= date('M d, Y', strtotime($task['due_date'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">No deadline</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusBadge = [
                                                    'Pending' => 'warning',
                                                    'In Progress' => 'info',
                                                    'Completed' => 'success',
                                                    'Review' => 'primary'
                                                ];
                                                $badge = $statusBadge[$task['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $badge ?>"><?= esc($task['status']) ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('my-tasks/view/' . $task['id']) ?>"
                                                    class="btn btn-sm btn-primary"
                                                    title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <!-- Edit button always visible -->
                                                <a href="<?= base_url('my-tasks/edit/' . $task['id']) ?>"
                                                    class="btn btn-sm btn-warning"
                                                    title="Edit Work">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <!-- Submit button only for non-completed tasks -->
                                                <?php if ($task['status'] != 'Completed'): ?>
                                                    <a href="<?= base_url('my-tasks/submit/' . $task['id']) ?>"
                                                        class="btn btn-sm btn-success"
                                                        title="Submit Work">
                                                        <i class="fas fa-upload"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>


                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No tasks assigned yet</h5>
                            <p class="text-muted">Tasks assigned by admin will appear here</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>
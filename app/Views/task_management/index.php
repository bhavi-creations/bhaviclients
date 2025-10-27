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
                        <li class="breadcrumb-item active">Employee Tasks</li>
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
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i> Filter Tasks
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= base_url('task-management') ?>" id="filterForm">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Employee</label>
                                    <select class="form-control live-filter" name="employee_id" id="filterEmployee">
                                        <option value="">-- All Employees --</option>
                                        <?php foreach ($employees as $emp): ?>
                                            <option value="<?= $emp['id'] ?>" 
                                                <?= (isset($_GET['employee_id']) && $_GET['employee_id'] == $emp['id']) ? 'selected' : '' ?>>
                                                <?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Client</label>
                                    <select class="form-control live-filter" name="client_id" id="filterClient">
                                        <option value="">-- All Clients --</option>
                                        <?php foreach ($clients as $client): ?>
                                            <option value="<?= $client['id'] ?>" 
                                                <?= (isset($_GET['client_id']) && $_GET['client_id'] == $client['id']) ? 'selected' : '' ?>>
                                                <?= esc($client['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control live-filter" name="status" id="filterStatus">
                                        <option value="">-- All Status --</option>
                                        <option value="Pending" <?= (isset($_GET['status']) && $_GET['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                        <option value="In Progress" <?= (isset($_GET['status']) && $_GET['status'] == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                                        <option value="Completed" <?= (isset($_GET['status']) && $_GET['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
                                        <option value="Review" <?= (isset($_GET['status']) && $_GET['status'] == 'Review') ? 'selected' : '' ?>>Review</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" 
                                           class="form-control live-filter" 
                                           name="from_date" 
                                           id="fromDate"
                                           value="<?= isset($_GET['from_date']) ? $_GET['from_date'] : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" 
                                           class="form-control live-filter" 
                                           name="to_date" 
                                           id="toDate"
                                           value="<?= isset($_GET['to_date']) ? $_GET['to_date'] : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <a href="<?= base_url('task-management') ?>" class="btn btn-secondary btn-block">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tasks Table Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Employee Tasks</h3>
                    <div class="card-tools">
                        <span class="badge badge-info"><?= count($tasks) ?> Tasks</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($tasks)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">S.No</th>
                                        <th width="15%">Employee</th>
                                        <th width="20%">Title</th>
                                        <th width="15%">Client</th>
                                        <th width="10%">Department</th>
                                        <th width="10%">Status</th>
                                        <th width="12%">Submitted</th>
                                        <th width="13%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; ?>
                                    <?php foreach ($tasks as $task): ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td>
                                                <strong><?= esc($task['emp_first_name'] . ' ' . $task['emp_last_name']) ?></strong>
                                                <?php if (!empty($task['department_name'])): ?>
                                                    <br><small class="text-muted"><?= esc($task['department_name']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= esc($task['title']) ?>
                                                <?php if (!empty($task['files_upload'])): ?>
                                                    <?php $files = json_decode($task['files_upload'], true); ?>
                                                    <?php if (is_array($files) && count($files) > 0): ?>
                                                        <br><small class="text-info">
                                                            <i class="fas fa-paperclip"></i> <?= count($files) ?> file(s)
                                                        </small>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($task['client_name'] ?? 'N/A') ?></td>
                                            <td><?= esc($task['department_name'] ?? 'N/A') ?></td>
                                            <td>
                                                <?php
                                                $statusClass = [
                                                    'Pending' => 'warning',
                                                    'In Progress' => 'info',
                                                    'Completed' => 'success',
                                                    'Review' => 'primary'
                                                ];
                                                $class = $statusClass[$task['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $class ?>">
                                                    <?= esc($task['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small>
                                                    <?= date('M d, Y', strtotime($task['submitted_at'])) ?><br>
                                                    <?= date('h:i A', strtotime($task['submitted_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('task-management/view/' . $task['id']) ?>" 
                                                       class="btn btn-info" 
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <?php if (session()->get('role_id') == 1): ?>
                                                        <!-- Status Update Button (Admin Only) -->
                                                        <button type="button" 
                                                                class="btn btn-warning" 
                                                                data-toggle="modal" 
                                                                data-target="#statusModal<?= $task['id'] ?>"
                                                                title="Update Status">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        
                                                        <!-- Delete Button (Admin Only) -->
                                                        <button type="button" 
                                                                class="btn btn-danger" 
                                                                onclick="confirmDelete(<?= $task['id'] ?>)"
                                                                title="Delete Task">
                                                            <i class="fas fa-trash"></i>
                                                        </button>

                                                        <!-- Hidden Delete Form -->
                                                        <form id="deleteForm<?= $task['id'] ?>" 
                                                              action="<?= base_url('task-management/delete/' . $task['id']) ?>" 
                                                              method="post" 
                                                              style="display:none;">
                                                            <?= csrf_field() ?>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Status Update Modal (Admin Only) -->
                                        <?php if (session()->get('role_id') == 1): ?>
                                            <div class="modal fade" id="statusModal<?= $task['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Update Task Status</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <form action="<?= base_url('task-management/update-status/' . $task['id']) ?>" method="post">
                                                            <?= csrf_field() ?>
                                                            <div class="modal-body">
                                                                <p><strong>Task:</strong> <?= esc($task['title']) ?></p>
                                                                <p><strong>Employee:</strong> <?= esc($task['emp_first_name'] . ' ' . $task['emp_last_name']) ?></p>
                                                                <hr>
                                                                <div class="form-group">
                                                                    <label>New Status</label>
                                                                    <select class="form-control" name="status" required>
                                                                        <option value="Pending" <?= $task['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                                        <option value="In Progress" <?= $task['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                                                        <option value="Completed" <?= $task['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                                                        <option value="Review" <?= $task['status'] == 'Review' ? 'selected' : '' ?>>Review</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Update Status</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No tasks found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
// Live filtering - auto-submit on change
document.querySelectorAll('.live-filter').forEach(function(element) {
    element.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
});

function confirmDelete(taskId) {
    if (confirm('Are you sure you want to delete this task? All associated files will also be deleted. This action cannot be undone.')) {
        document.getElementById('deleteForm' + taskId).submit();
    }
}
</script>

<?= $this->endSection() ?>

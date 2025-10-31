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
                        <li class="breadcrumb-item"><a href="<?= base_url('client-dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Work Updates</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Filter Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i> Filter Work Updates
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= base_url('work-updates') ?>" id="filterForm">
                        <div class="row">
                            <!-- Department Filter - NEW -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Department</label>
                                    <select class="form-control live-filter" name="department">
                                        <option value="">-- All Departments --</option>
                                        <?php if (!empty($departments)): ?>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?= esc($dept['id']) ?>" 
                                                    <?= (isset($_GET['department']) && $_GET['department'] == $dept['id']) ? 'selected' : '' ?>>
                                                    <?= esc($dept['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control live-filter" name="status">
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
                                           value="<?= isset($_GET['from_date']) ? $_GET['from_date'] : '' ?>">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" 
                                           class="form-control live-filter" 
                                           name="to_date"
                                           value="<?= isset($_GET['to_date']) ? $_GET['to_date'] : '' ?>">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <a href="<?= base_url('work-updates') ?>" class="btn btn-secondary btn-block">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Work Updates Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Work Done for You</h3>
                    <div class="card-tools">
                        <span class="badge badge-info"><?= count($tasks) ?> Updates</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($tasks)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">S.No</th>
                                        <th width="25%">Work Title</th>
                                        <th width="20%">Employee</th>
                                        <th width="15%">Department</th>
                                        <th width="12%">Status</th>
                                        <th width="13%">Date</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; ?>
                                    <?php foreach ($tasks as $task): ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td>
                                                <strong><?= esc($task['title']) ?></strong>
                                                <?php if (!empty($task['files_upload'])): ?>
                                                    <?php $files = json_decode($task['files_upload'], true); ?>
                                                    <?php if (is_array($files) && count($files) > 0): ?>
                                                        <br><small class="text-info">
                                                            <i class="fas fa-paperclip"></i> <?= count($files) ?> file(s) attached
                                                        </small>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-user-tie text-primary"></i>
                                                <?= esc($task['emp_first_name'] . ' ' . $task['emp_last_name']) ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($task['department_name'])): ?>
                                                    <span class="badge badge-secondary">
                                                        <i class="fas fa-building"></i> <?= esc($task['department_name']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
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
                                                    <i class="far fa-calendar"></i> <?= date('M d, Y', strtotime($task['submitted_at'])) ?><br>
                                                    <i class="far fa-clock"></i> <?= date('h:i A', strtotime($task['submitted_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('view-work/' . $task['id']) ?>" 
                                                   class="btn btn-sm btn-info"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center p-5">
                            <i class="fas fa-info-circle fa-3x mb-3"></i>
                            <h5>No work updates found</h5>
                            <p class="text-muted">
                                <?php if (!empty($_GET['status']) || !empty($_GET['department']) || !empty($_GET['from_date'])): ?>
                                    Try adjusting your filters to see more results.
                                <?php else: ?>
                                    Work updates will appear here once employees submit work for you.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
// Live filtering
document.querySelectorAll('.live-filter').forEach(function(element) {
    element.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
});
</script>

<?= $this->endSection() ?>

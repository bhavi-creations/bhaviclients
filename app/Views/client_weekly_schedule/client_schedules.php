<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\client_weekly_schedule\client_schedules.php 
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
                        <li class="breadcrumb-item active"><?= esc($client['name']) ?></li>
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

            <!-- Client Info Card -->
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Client Name:</strong>
                            <p class="text-muted"><?= esc($client['name']) ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Email:</strong>
                            <p class="text-muted"><?= esc($client['email']) ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Phone:</strong>
                            <p class="text-muted"><?= esc($client['phone']) ?></p>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Schedules:</strong>
                            <p><span class="badge badge-info badge-lg"><?= count($schedules) ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedules Table -->
            <div class="card shadow-lg">
                <div class="card-header border-0">
                    <h3 class="card-title">All Schedules for <?= esc($client['name']) ?></h3>
                    <div class="card-tools">
                        <a href="<?= base_url('weekly-schedule/create') ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-plus-circle"></i> Create New Schedule
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-valign-middle">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Week Period</th>
                                    <th>Status</th>
                                    <th>Created On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($schedules)): ?>
                                    <?php $sn = 1; ?>
                                    <?php foreach ($schedules as $schedule): ?>
                                        <tr>
                                            <td><?= $sn++ ?></td>
                                            <td>
                                                <strong>
                                                    <?= date('M d', strtotime($schedule['week_start_date'])) ?> - 
                                                    <?= date('M d, Y', strtotime($schedule['week_end_date'])) ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <?php if ($schedule['status'] == 'published'): ?>
                                                    <span class="badge badge-success">Published</span>
                                                <?php elseif ($schedule['status'] == 'draft'): ?>
                                                    <span class="badge badge-warning">Draft</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Archived</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($schedule['created_at'])) ?></td>
                                            <td>
                                                <a href="<?= base_url('weekly-schedule/view/' . $schedule['id']) ?>" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="View Schedule">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= base_url('weekly-schedule/edit/' . $schedule['id']) ?>" 
                                                   class="btn btn-sm btn-info" 
                                                   title="Edit Schedule">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" 
                                                   onclick="confirmDelete(<?= $schedule['id'] ?>)" 
                                                   class="btn btn-sm btn-danger" 
                                                   title="Delete Schedule">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-4">No schedules found for this client.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <a href="<?= base_url('weekly-schedule') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Clients
            </a>

        </div>
    </section>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="deleteForm" method="post" action="">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this weekly schedule?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function confirmDelete(id) {
        document.getElementById('deleteForm').action = '<?= base_url("weekly-schedule/delete/") ?>' + id;
        $('#deleteModal').modal('show');
    }
</script>

<?= $this->endSection() ?>

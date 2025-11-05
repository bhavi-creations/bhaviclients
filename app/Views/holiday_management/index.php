<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-calendar-day"></i>
                        <?= esc($title) ?>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Holidays</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?= view('flash_messages') ?>

            <div class="card shadow-lg">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Holiday List</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('holidays/create') ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Add Holiday
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($holidays)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Holiday Name</th>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Recurring?</th>
                                        <th>Added By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($holidays as $i => $holiday): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= esc($holiday['holiday_name']) ?></td>
                                            <td>
                                                <strong><?= date('d M Y', strtotime($holiday['holiday_date'])) ?></strong>
                                            </td>
                                            <td>
                                                <small><?= esc($holiday['description']) ?></small>
                                            </td>
                                            <td>
                                                <?= $holiday['is_recurring'] ? '<span class="badge badge-info">Yes</span>' : '<span class="badge badge-dark">No</span>' ?>
                                            </td>
                                            <td>
                                                <small>
                                                    <?= esc($holiday['creator_first_name'] ?? '') ?>
                                                    <?= esc($holiday['creator_last_name'] ?? '') ?>
                                                </small>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('holidays/edit/' . $holiday['id']) ?>" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                <a href="<?= base_url('holidays/delete/' . $holiday['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this holiday?')" title="Delete"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-calendar-day fa-4x mb-3"></i>
                            <h5>No Holidays Added Yet</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?= $this->endSection() ?>

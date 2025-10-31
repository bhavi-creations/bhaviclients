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
                        <li class="breadcrumb-item"><a href="<?= base_url('maintenance') ?>">Project Details</a></li>
                        <li class="breadcrumb-item active"><?= esc($client['name']) ?></li>
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

            <!-- Table Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building"></i> Client: <strong><?= esc($client['name']) ?></strong>
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info"><?= count($records) ?> Records</span>
                        <a href="<?= base_url('maintenance/create') ?>" class="btn btn-primary btn-sm ml-2">
                            <i class="fas fa-plus"></i> Add New
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($records)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="4%">S.No</th>
                                        <th width="18%">Title</th>
                                        <th width="22%">Description</th>
                                        <th width="18%">Remarks</th>
                                        <th width="8%">Files</th>
                                        <th width="12%">Created</th>
                                        <th width="18%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; ?>
                                    <?php foreach ($records as $row): ?>
                                        <tr>
                                            <td><?= $sno++ ?></td>
                                            <td>
                                                <strong><?= esc($row['title']) ?></strong>
                                            </td>
                                            <td>
                                                <?php 
                                                $desc = esc($row['description'] ?: 'No description');
                                                echo strlen($desc) > 80 ? substr($desc, 0, 80) . '...' : $desc;
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if (!empty($row['remarks'])) {
                                                    $remarks = esc($row['remarks']);
                                                    echo strlen($remarks) > 80 ? substr($remarks, 0, 80) . '...' : $remarks;
                                                } else {
                                                    echo '<span class="text-muted">No remarks</span>';
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (!empty($row['file_uploads'])): ?>
                                                    <?php $files = json_decode($row['file_uploads'], true); ?>
                                                    <?php if (is_array($files) && count($files) > 0): ?>
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-paperclip"></i> <?= count($files) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">0</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">0</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small><?= date('M d, Y', strtotime($row['created_at'])) ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('maintenance/view/' . $row['id']) ?>" 
                                                       class="btn btn-info" 
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('maintenance/edit/' . $row['id']) ?>" 
                                                       class="btn btn-warning" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-danger" 
                                                            onclick="confirmDelete(<?= $row['id'] ?>)"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Hidden Delete Form -->
                                                <form id="deleteForm<?= $row['id'] ?>" 
                                                      action="<?= base_url('maintenance/delete/' . $row['id']) ?>" 
                                                      method="post" 
                                                      style="display:none;">
                                                    <?= csrf_field() ?>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> No project details found for this client.
                            <br>
                            <a href="<?= base_url('maintenance/create') ?>" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-plus"></i> Add First Record
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="<?= base_url('maintenance') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to All Clients
                    </a>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this project details record? All associated files will also be deleted. This action cannot be undone.')) {
        document.getElementById('deleteForm' + id).submit();
    }
}
</script>

<?= $this->endSection() ?>

<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\work\create.php
?>
<!-- Load Layout Template -->
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php 
// Assuming the validation service is passed to the view, otherwise use the global service
$validation = \Config\Services::validation(); 
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('work/mytasks') ?>">My Tasks</a></li>
                        <li class="breadcrumb-item active"><?= esc($title) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <!-- Card for Form -->
                    <div class="card card-primary shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">Enter New Work Details</h3>
                        </div>
                        
                        <!-- Form submits to Work::store() method -->
                        <?= form_open(base_url('work/store')) ?>
                        <?= csrf_field() ?>

                        <div class="card-body">

                            <!-- Display general error messages (if any) -->
                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                            <?php endif; ?>

                            <!-- CLIENT SELECTION FIELD -->
                            <div class="form-group">
                                <label for="client_id">Select Client <span class="text-danger">*</span></label>
                                <select class="form-control <?= $validation->hasError('client_id') ? 'is-invalid' : '' ?>"
                                    id="client_id" name="client_id" required>
                                    <option value="">-- Select a Client --</option>
                                    <?php 
                                        $oldClientId = old('client_id');
                                        foreach ($clients as $client): 
                                    ?>
                                        <option value="<?= esc($client['id']) ?>" 
                                            <?= ($oldClientId == $client['id']) ? 'selected' : '' ?>>
                                            <?= esc($client['name']) ?> (<?= esc($client['owner_first_name'] . ' ' . $client['owner_last_name']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($validation->hasError('client_id')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('client_id') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Task Title -->
                            <div class="form-group">
                                <label for="title">Work/Task Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= $validation->hasError('title') ? 'is-invalid' : '' ?>"
                                    id="title" name="title" placeholder="e.g., Website Content Review"
                                    value="<?= old('title') ?>" required>
                                <?php if ($validation->hasError('title')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('title') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Description -->
                            <div class="form-group">
                                <label for="description">Detailed Description <span class="text-danger">*</span></label>
                                <textarea class="form-control <?= $validation->hasError('description') ? 'is-invalid' : '' ?>"
                                    id="description" name="description" rows="5"
                                    placeholder="Describe the task, scope, and objectives..."><?= old('description') ?></textarea>
                                <?php if ($validation->hasError('description')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('description') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="row">
                                <!-- Due Date -->
                                <div class="form-group col-md-6">
                                    <label for="due_date">Target Due Date (Optional)</label>
                                    <input type="date" class="form-control <?= $validation->hasError('due_date') ? 'is-invalid' : '' ?>"
                                        id="due_date" name="due_date"
                                        value="<?= old('due_date') ?>">
                                    <?php if ($validation->hasError('due_date')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('due_date') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Status (Initial status is almost always Pending) -->
                                <div class="form-group col-md-6">
                                    <label for="status">Initial Status <span class="text-danger">*</span></label>
                                    <select class="form-control <?= $validation->hasError('status') ? 'is-invalid' : '' ?>"
                                        id="status" name="status" required>
                                        <?php 
                                            $statuses = ['Pending', 'In Progress', 'Completed', 'Review'];
                                            $oldStatus = old('status', 'Pending'); 
                                            foreach ($statuses as $s): ?>
                                            <option value="<?= $s ?>" <?= ($oldStatus == $s) ? 'selected' : '' ?>>
                                                <?= $s ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if ($validation->hasError('status')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('status') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="<?= base_url('work/mytasks') ?>" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i> Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-1"></i> Submit Work</button>
                        </div>
                        <?= form_close() ?>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<?= $this->endSection() ?>

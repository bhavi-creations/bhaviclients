<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\work\edit.php
?>
<!-- Load Layout Template -->
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php 
// Assuming the validation service is passed to the view, otherwise use the global service
$validation = \Config\Services::validation(); 
// Determine the current task data, falling back to an empty array for safety
$currentTask = $task ?? [];
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
                    <div class="card card-warning shadow-lg">
                        <div class="card-header">
                            <h3 class="card-title">Update Work Details: #<?= esc($currentTask['id'] ?? 'N/A') ?></h3>
                        </div>
                        
                        <!-- Form submits to Work::update() method with the task ID -->
                        <?= form_open(base_url('work/update/' . ($currentTask['id'] ?? '') )) ?>
                        <?= csrf_field() ?>
                        <!-- Required for CodeIgniter to handle PUT/PATCH requests via POST -->
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="id" value="<?= esc($currentTask['id'] ?? '') ?>">

                        <div class="card-body">

                            <!-- Display general error messages (if any) -->
                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                            <?php endif; ?>
                            
                            <!-- CLIENT SELECTION FIELD (UPDATED) -->
                            <div class="form-group">
                                <label for="client_id">Select Client <span class="text-danger">*</span></label>
                                <select class="form-control <?= $validation->hasError('client_id') ? 'is-invalid' : '' ?>"
                                    id="client_id" name="client_id" required>
                                    <option value="">-- Select a Client --</option>
                                    <?php 
                                        // Priority: 1. Old input, 2. Current task value
                                        $selectedClientId = old('client_id', $currentTask['client_id'] ?? '');
                                        foreach ($clients as $client): 
                                    ?>
                                        <option value="<?= esc($client['id']) ?>" 
                                            <?= ($selectedClientId == $client['id']) ? 'selected' : '' ?>>
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

                            <!-- Task Title (Editable, but required) -->
                            <div class="form-group">
                                <label for="title">Work/Task Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= $validation->hasError('title') ? 'is-invalid' : '' ?>"
                                    id="title" name="title" placeholder="e.g., Q3 Report Compilation"
                                    value="<?= old('title', $currentTask['title'] ?? '') ?>" required>
                                <?php if ($validation->hasError('title')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('title') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Description (Progress Report) -->
                            <div class="form-group">
                                <label for="description">Detailed Description / Progress Report <span class="text-danger">*</span></label>
                                <textarea class="form-control <?= $validation->hasError('description') ? 'is-invalid' : '' ?>"
                                    id="description" name="description" rows="5"
                                    placeholder="Describe the task, your goals, or the current progress..."><?= old('description', $currentTask['description'] ?? '') ?></textarea>
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
                                        value="<?= old('due_date', $currentTask['due_date'] ?? '') ?>">
                                    <?php if ($validation->hasError('due_date')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('due_date') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Status (CRITICAL: Allows employee to update status) -->
                                <div class="form-group col-md-6">
                                    <label for="status">Current Status <span class="text-danger">*</span></label>
                                    <select class="form-control <?= $validation->hasError('status') ? 'is-invalid' : '' ?>"
                                        id="status" name="status" required>
                                        <?php 
                                            // Options based on the ENUM in the database: Pending, In Progress, Completed, Review
                                            $statuses = ['Pending', 'In Progress', 'Completed', 'Review'];
                                            $oldStatus = old('status', $currentTask['status'] ?? 'Pending'); 
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
                            <a href="<?= base_url('work/mytasks') ?>" class="btn btn-default"><i class="fas fa-arrow-left mr-1"></i> Back to List</a>
                            <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i> Update Work</button>
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

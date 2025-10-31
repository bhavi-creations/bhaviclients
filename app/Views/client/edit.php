<?php 
// C:\xampp\htdocs\bhaviclients\app\Views\client\edit.php 
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= esc($title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('client') ?>">Clients</a></li>
                        <li class="breadcrumb-item active">Edit Client</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card shadow-lg">
                        <div class="card-header border-0">
                            <h3 class="card-title">Update Client Details</h3>
                        </div>
                        
                        <?php if (isset($validation) && $validation->getErrors()): ?>
                            <div class="alert alert-danger m-3">
                                <ul class="mb-0">
                                    <?php foreach ($validation->getErrors() as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?= form_open('client/update/' . $client['id']) ?>
                        <div class="card-body">

                            <div class="row">
                                <!-- Company Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Company Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?= isset($validation) && $validation->hasError('name') ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= esc(old('name', $client['name'])) ?>" placeholder="Enter company name" required>
                                        <?php if (isset($validation) && $validation->hasError('name')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('name') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Reference -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reference">Reference</label>
                                        <input type="text" class="form-control <?= isset($validation) && $validation->hasError('reference') ? 'is-invalid' : '' ?>" id="reference" name="reference" value="<?= esc(old('reference', $client['reference'] ?? '')) ?>" placeholder="Enter reference (optional)">
                                        <?php if (isset($validation) && $validation->hasError('reference')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('reference') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Owner First Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="owner_first_name">Owner First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?= isset($validation) && $validation->hasError('owner_first_name') ? 'is-invalid' : '' ?>" id="owner_first_name" name="owner_first_name" value="<?= esc(old('owner_first_name', $client['owner_first_name'])) ?>" placeholder="Enter owner's first name" required>
                                        <?php if (isset($validation) && $validation->hasError('owner_first_name')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('owner_first_name') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Owner Last Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="owner_last_name">Owner Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?= isset($validation) && $validation->hasError('owner_last_name') ? 'is-invalid' : '' ?>" id="owner_last_name" name="owner_last_name" value="<?= esc(old('owner_last_name', $client['owner_last_name'])) ?>" placeholder="Enter owner's last name" required>
                                        <?php if (isset($validation) && $validation->hasError('owner_last_name')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('owner_last_name') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Email Address -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control <?= isset($validation) && $validation->hasError('email') ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= esc(old('email', $client['email'])) ?>" placeholder="Enter email address" required>
                                        <?php if (isset($validation) && $validation->hasError('email')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Phone Number -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control <?= isset($validation) && $validation->hasError('phone') ? 'is-invalid' : '' ?>" id="phone" name="phone" value="<?= esc(old('phone', $client['phone'])) ?>" placeholder="Enter phone number" required>
                                        <?php if (isset($validation) && $validation->hasError('phone')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('phone') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Manager Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="manager_name">Manager Name</label>
                                        <input type="text" class="form-control <?= isset($validation) && $validation->hasError('manager_name') ? 'is-invalid' : '' ?>" id="manager_name" name="manager_name" value="<?= esc(old('manager_name', $client['manager_name'] ?? '')) ?>" placeholder="Enter manager name (optional)">
                                        <?php if (isset($validation) && $validation->hasError('manager_name')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('manager_name') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Manager Phone -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="manager_phone">Manager Phone</label>
                                        <input type="text" class="form-control <?= isset($validation) && $validation->hasError('manager_phone') ? 'is-invalid' : '' ?>" id="manager_phone" name="manager_phone" value="<?= esc(old('manager_phone', $client['manager_phone'] ?? '')) ?>" placeholder="Enter manager phone (optional)">
                                        <?php if (isset($validation) && $validation->hasError('manager_phone')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('manager_phone') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Started Date -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="started_date">Started Date</label>
                                        <input type="date" class="form-control <?= isset($validation) && $validation->hasError('started_date') ? 'is-invalid' : '' ?>" id="started_date" name="started_date" value="<?= esc(old('started_date', $client['started_date'] ?? '')) ?>">
                                        <?php if (isset($validation) && $validation->hasError('started_date')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('started_date') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control <?= isset($validation) && $validation->hasError('remarks') ? 'is-invalid' : '' ?>" id="remarks" name="remarks" rows="3" placeholder="Enter any additional remarks (optional)"><?= esc(old('remarks', $client['remarks'] ?? '')) ?></textarea>
                                <?php if (isset($validation) && $validation->hasError('remarks')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('remarks') ?></div>
                                <?php endif; ?>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Update Client
                            </button>
                            <a href="<?= base_url('client') ?>" class="btn btn-secondary">Cancel</a>
                        </div>

                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>

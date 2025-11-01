<?php
// C:\xampp\htdocs\bhaviclients\app\Views\client\create.php 
?>
<!-- Load Layout Template -->
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= esc($title) ?></h1>
                    <small class="text-muted">A corresponding **Client User Account** will be created for authentication.</small>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('client') ?>">Clients</a></li>
                        <li class="breadcrumb-item active">Add Client</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 offset-md-1">

                    <!-- SUCCESS/INFO FLASH MESSAGE -->
                    <?php if (session()->getFlashdata('message')): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Success!</h5>
                            <?= session()->getFlashdata('message') ?>
                        </div>
                    <?php endif; ?>

                    <!-- SYSTEM ERROR FLASH MESSAGE -->
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-ban"></i> System Error!</h5>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <!-- VALIDATION ERRORS FLASH MESSAGE -->
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-warning alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Validation Errors:</h5>
                            <?= session()->getFlashdata('errors') ?>
                        </div>
                    <?php endif; ?>

                    <!-- Card for Add Client Form -->
                    <div class="card shadow-lg">
                        <div class="card-header border-0">
                            <h3 class="card-title">Client Details & User Credentials</h3>
                        </div>

                        <?= form_open('client/store') ?>
                        <div class="card-body">

                            <div class="row">
                                <!-- Company Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Company Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?= $validation->hasError('name') ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= old('name') ?>" placeholder="Enter company name">
                                        <?php if ($validation->hasError('name')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('name') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Reference -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reference">Reference</label>
                                        <input type="text" class="form-control <?= $validation->hasError('reference') ? 'is-invalid' : '' ?>" id="reference" name="reference" value="<?= old('reference') ?>" placeholder="Enter reference (optional)">
                                        <?php if ($validation->hasError('reference')): ?>
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
                                        <input type="text" class="form-control <?= $validation->hasError('owner_first_name') ? 'is-invalid' : '' ?>" id="owner_first_name" name="owner_first_name" value="<?= old('owner_first_name') ?>" placeholder="Enter owner's first name">
                                        <?php if ($validation->hasError('owner_first_name')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('owner_first_name') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Owner Last Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="owner_last_name">Owner Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?= $validation->hasError('owner_last_name') ? 'is-invalid' : '' ?>" id="owner_last_name" name="owner_last_name" value="<?= old('owner_last_name') ?>" placeholder="Enter owner's last name">
                                        <?php if ($validation->hasError('owner_last_name')): ?>
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
                                        <input type="email" class="form-control <?= $validation->hasError('email') ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= old('email') ?>" placeholder="Enter valid email (used for username)">
                                        <?php if ($validation->hasError('email')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Phone Number -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?= $validation->hasError('phone') ? 'is-invalid' : '' ?>" id="phone" name="phone" value="<?= old('phone') ?>" placeholder="Enter phone number">
                                        <small class="form-text text-info">This number is used as the **initial password** for the client's login account.</small>
                                        <?php if ($validation->hasError('phone')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('phone') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>


                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="manager_name">Manager Name <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control <?= $validation->hasError('manager_name') ? 'is-invalid' : '' ?>"
                                                id="manager_name"
                                                name="manager_name"
                                                value="<?= old('manager_name') ?>"
                                                placeholder="Enter manager name "
                                                required> <?php if ($validation->hasError('manager_name')): ?>
                                                <div class="invalid-feedback"><?= $validation->getError('manager_name') ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="manager_phone">Manager Phone <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control <?= $validation->hasError('manager_phone') ? 'is-invalid' : '' ?>"
                                                id="manager_phone"
                                                name="manager_phone"
                                                value="<?= old('manager_phone') ?>"
                                                placeholder="Enter manager phone "
                                                required> <?php if ($validation->hasError('manager_phone')): ?>
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
                                        <input type="date" class="form-control <?= $validation->hasError('started_date') ? 'is-invalid' : '' ?>" id="started_date" name="started_date" value="<?= old('started_date') ?>">
                                        <?php if ($validation->hasError('started_date')): ?>
                                            <div class="invalid-feedback"><?= $validation->getError('started_date') ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control <?= $validation->hasError('remarks') ? 'is-invalid' : '' ?>" id="remarks" name="remarks" rows="3" placeholder="Enter any additional remarks (optional)"><?= old('remarks') ?></textarea>
                                <?php if ($validation->hasError('remarks')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('remarks') ?></div>
                                <?php endif; ?>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus mr-1"></i> Save Client
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
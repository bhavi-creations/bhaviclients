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
                <div class="col-md-8">
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

                            <!-- Company Name -->
                            <div class="form-group">
                                <label for="name">Company Name <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    class="form-control <?= isset($validation) && $validation->hasError('name') ? 'is-invalid' : '' ?>" 
                                    id="name" 
                                    name="name" 
                                    value="<?= esc(old('name', $client['name'])) ?>" 
                                    placeholder="Enter company name" 
                                    required>
                                <?php if (isset($validation) && $validation->hasError('name')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('name') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Owner First Name -->
                            <div class="form-group">
                                <label for="owner_first_name">Owner First Name <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    class="form-control <?= isset($validation) && $validation->hasError('owner_first_name') ? 'is-invalid' : '' ?>" 
                                    id="owner_first_name" 
                                    name="owner_first_name" 
                                    value="<?= esc(old('owner_first_name', $client['owner_first_name'])) ?>" 
                                    placeholder="Enter owner's first name" 
                                    required>
                                <?php if (isset($validation) && $validation->hasError('owner_first_name')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('owner_first_name') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Owner Last Name -->
                            <div class="form-group">
                                <label for="owner_last_name">Owner Last Name <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    class="form-control <?= isset($validation) && $validation->hasError('owner_last_name') ? 'is-invalid' : '' ?>" 
                                    id="owner_last_name" 
                                    name="owner_last_name" 
                                    value="<?= esc(old('owner_last_name', $client['owner_last_name'])) ?>" 
                                    placeholder="Enter owner's last name" 
                                    required>
                                <?php if (isset($validation) && $validation->hasError('owner_last_name')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('owner_last_name') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Email Address -->
                            <div class="form-group">
                                <label for="email">Email Address <span class="text-danger">*</span></label>
                                <input 
                                    type="email" 
                                    class="form-control <?= isset($validation) && $validation->hasError('email') ? 'is-invalid' : '' ?>" 
                                    id="email" 
                                    name="email" 
                                    value="<?= esc(old('email', $client['email'])) ?>" 
                                    placeholder="Enter email address" 
                                    required>
                                <?php if (isset($validation) && $validation->hasError('email')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Phone Number -->
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input 
                                    type="tel" 
                                    class="form-control <?= isset($validation) && $validation->hasError('phone') ? 'is-invalid' : '' ?>" 
                                    id="phone" 
                                    name="phone" 
                                    value="<?= esc(old('phone', $client['phone'])) ?>" 
                                    placeholder="Enter phone number">
                                <?php if (isset($validation) && $validation->hasError('phone')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('phone') ?></div>
                                <?php endif; ?>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Update Client</button>
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

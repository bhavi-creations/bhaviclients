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
                        <li class="breadcrumb-item active">Profile</li>
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
                    <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-md-5">
                    <!-- Profile Card -->
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-body box-profile">
                            <!-- Profile Picture -->
                            <div class="text-center mb-3">
                                <img class="profile-user-img img-fluid img-circle elevation-2" 
                                     src="https://ui-avatars.com/api/?name=<?= urlencode($user['first_name'] . ' ' . $user['last_name']) ?>&size=128&background=007bff&color=fff&bold=true" 
                                     alt="User profile picture"
                                     style="width: 128px; height: 128px;">
                            </div>

                            <!-- User Name -->
                            <h3 class="profile-username text-center mb-1">
                                <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                            </h3>

                            <!-- Role Badge -->
                            <p class="text-center mb-4">
                                <span class="badge badge-primary badge-lg" style="font-size: 14px; padding: 8px 16px;">
                                    <i class="fas fa-user-tag mr-1"></i> <?= esc($user['role_name'] ?? 'N/A') ?>
                                </span>
                            </p>

                            <!-- Profile Details -->
                            <div class="card mb-3" style="background-color: #f8f9fa; border: none;">
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border: none; padding: 12px 20px;">
                                            <span><i class="fas fa-phone text-primary mr-2"></i><strong>Phone</strong></span>
                                            <span class="text-muted"><?= esc($user['phone']) ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border-top: 1px solid #dee2e6; padding: 12px 20px;">
                                            <span><i class="fas fa-id-badge text-info mr-2"></i><strong>Username</strong></span>
                                            <span class="text-muted"><?= esc($user['username']) ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border-top: 1px solid #dee2e6; padding: 12px 20px;">
                                            <span><i class="fas fa-calendar text-success mr-2"></i><strong>Member Since</strong></span>
                                            <span class="text-muted"><?= date('M d, Y', strtotime($user['created_at'])) ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-4">
                                <a href="<?= base_url('profile/edit-username') ?>" class="btn btn-info btn-block btn-lg shadow-sm">
                                    <i class="fas fa-user-edit mr-2"></i> Change Username
                                </a>
                                <a href="<?= base_url('profile/change-password') ?>" class="btn btn-warning btn-block btn-lg shadow-sm">
                                    <i class="fas fa-key mr-2"></i> Change Password
                                </a>
                            </div>

                            <!-- Security Notice -->
                            <div class="alert alert-light mt-4 mb-0" style="border: 1px solid #e3e6f0;">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt mr-1"></i> 
                                    Keep your login credentials secure. Never share your password with anyone.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>

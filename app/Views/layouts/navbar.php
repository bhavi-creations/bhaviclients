<!-- Navbar -->
<nav class="app-header navbar navbar-expand bg-body">
    <!--begin::Container-->
    <div class="container-fluid">
        <!-- Start navbar links (Left Side) -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
        </ul>
        <!-- End navbar links -->

        <!-- Right Side: User Menu and Icons -->
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>
                    <span class="d-none d-md-inline ms-1">
                        <?= esc(session()->get('first_name') . ' ' . session()->get('last_name')) ?>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <!-- User Image -->
                    <li class="user-header text-bg-primary text-center py-3">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('first_name') . ' ' . session()->get('last_name')) ?>&size=90&background=fff&color=007bff&bold=true" 
                             class="img-circle elevation-2 mb-2" 
                             alt="User Image"
                             style="width: 90px; height: 90px;">
                        <p class="mb-1" style="font-size: 16px; font-weight: 600;">
                            <?= esc(session()->get('first_name') . ' ' . session()->get('last_name')) ?>
                        </p>
                        <small style="opacity: 0.9;">
                            <?= esc(session()->get('role_name') ?? 'User') ?>
                        </small>
                    </li>

                    <!-- Menu Body -->
                    <li class="user-body">
                        <div class="list-group list-group-flush">
                            <a href="<?= base_url('profile/edit-username') ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-user-circle mr-2 text-primary"></i> Change Username
                            </a>
                            <a href="<?= base_url('profile/change-password') ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-key mr-2 text-warning"></i> Change Password
                            </a>
                        </div>
                    </li>

                    <!-- Menu Footer -->
                    <li class="user-footer text-bg-primary">
                        <a href="<?= base_url('logout') ?>" class="btn btn-flat btn-block" 
                           style="background-color: white; color: #0c51b8ff; font-weight: 600;">
                            <i class="fas fa-sign-out-alt mr-1"></i> Sign Out
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <!--end::Container-->
</nav>
<!-- /.navbar -->

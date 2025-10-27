<aside class="app-sidebar side_bg shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="<?= base_url('/') ?>" class="brand-link">
            <span class="brand-text">Bhavi Clients</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                <?php
                $userRoleId   = session()->get('role_id');
                $currentUri   = uri_string();
                $isDashboardActive     = $currentUri == 'dashboard' || $currentUri == '';
                $isDepartmentActive    = url_is('department*');
                $isClientActive        = url_is('client*');
                $isEmployeeActive      = url_is('employee*') && !url_is('employee/tasks*');
                $isRolesActive         = url_is('roles*');
                $isTaskManagementActive = url_is('task-management*');
                $isProfileActive       = url_is('profile*');
                $isMyTasksActive       = url_is('my-tasks*') || url_is('submit-work*') || url_is('edit-task*');
                ?>

                <!-- Dashboard + Profile for All -->
                <li class="nav-item">
                    <?php if ($userRoleId == 1 || $userRoleId == 5): ?>
                        <a href="<?= base_url('dashboard') ?>" class="nav-link <?= $isDashboardActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Dashboard</p>
                        </a>
                    <?php elseif ($userRoleId == 2): ?>
                        <a href="<?= base_url('employee-dashboard') ?>" class="nav-link <?= url_is('employee-dashboard') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Dashboard</p>
                        </a>
                    <?php elseif ($userRoleId == 3 || $userRoleId == 4): ?>
                        <a href="<?= base_url('client-dashboard') ?>" class="nav-link <?= url_is('client-dashboard') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Dashboard</p>
                        </a>
                    <?php endif; ?>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('profile') ?>" class="nav-link <?= $isProfileActive ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-circle"></i>
                        <p>My Profile</p>
                    </a>
                </li>

                <!-- ADMIN + ADMIN MANAGER PANEL -->
                <?php if (in_array($userRoleId, [1, 5])): ?>
                    <li class="nav-header">ADMIN PANEL</li>
                    <li class="nav-item">
                        <a href="<?= base_url('department') ?>" class="nav-link <?= $isDepartmentActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-sitemap"></i>
                            <p>Departments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('roles') ?>" class="nav-link <?= $isRolesActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>Roles Management</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('employee') ?>" class="nav-link <?= $isEmployeeActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Employees</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('task-management') ?>" class="nav-link <?= $isTaskManagementActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Employee Tasks</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('client') ?>" class="nav-link <?= $isClientActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-handshake"></i>
                            <p>Clients</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('client-uploads') ?>" class="nav-link <?= url_is('client-uploads*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-cloud-download-alt"></i>
                            <p>Client Uploads</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('user-management') ?>" class="nav-link <?= url_is('user-management*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>User Management</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- EMPLOYEE -->
                <?php if ($userRoleId == 2): ?>
                    <li class="nav-header">EMPLOYEE PANEL</li>
                    <li class="nav-item">
                        <a href="<?= base_url('client-uploads') ?>" class="nav-link <?= url_is('client-uploads*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-cloud-download-alt"></i>
                            <p>Client Uploads</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('my-tasks') ?>" class="nav-link <?= $isMyTasksActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>My Tasks</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('submit-work') ?>" class="nav-link">
                            <i class="nav-icon fas fa-upload"></i>
                            <p>Submit Work</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- CLIENT + CLIENT MANAGER -->
                <?php if (in_array($userRoleId, [3, 4])): ?>
                    <li class="nav-header">CLIENT PANEL</li>
                    <li class="nav-item">
                        <a href="<?= base_url('work-updates') ?>" class="nav-link <?= url_is('work-updates*') || url_is('view-work*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>Work Updates</p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="<?= base_url('download-files') ?>" class="nav-link">
                            <i class="nav-icon fas fa-download"></i>
                            <p> Excel Files</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('self-uploads') ?>" class="nav-link">
                            <i class="nav-icon fas fa-upload"></i>
                            <p>My Uploads</p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="<?= base_url('upload-files') ?>" class="nav-link <?= url_is('upload-files*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-cloud-upload-alt"></i>
                            <p>Upload Files</p>
                        </a>
                    </li>

                <?php endif; ?>

                <?php if (in_array(session()->get('role_id'), [1])): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('maintenance') ?>" class="nav-link">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>Maintenance</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (in_array(session()->get('role_id'), [3])): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('client-maintenance') ?>" class="nav-link">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>Maintenance</p>
                        </a>
                    </li>
                <?php endif; ?>



                <!-- Logout -->
                <li class="nav-item mt-3">
                    <a href="<?= base_url('logout') ?>" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
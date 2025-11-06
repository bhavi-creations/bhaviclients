<?php
// C:\xampp\htdocs\bhaviclients\app\Views\layouts\sidebar.php
?>
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
                $isClientActive        = url_is('client*') && !url_is('client-payment*') && !url_is('client-uploads*') && !url_is('client-report*');
                $isClientPaymentsActive = url_is('client-payment/list') || url_is('client-payment/*');
                $isClientReportsActive = url_is('client-report*');
                $isWeeklyScheduleActive = url_is('weekly-schedule*') || url_is('my-weekly-schedule');
                $isEmployeeActive      = url_is('employee*') && !url_is('employee/tasks*');
                $isRolesActive         = url_is('roles*');
                $isTaskManagementActive = url_is('task-management*');
                $isPayslipsActive = url_is('employee-payslip*');
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

                <!-- <li class="nav-item">
                    <a href="<?= base_url('profile') ?>" class="nav-link <?= $isProfileActive ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-circle"></i>
                        <p>My Profile</p>
                    </a>
                </li> -->

                <!-- SUPER ADMIN PANEL (role_id = 1) -->
                <?php if ($userRoleId == 1): ?>
                    <li class="nav-header">ADMIN PANEL</li>

                    <!-- 1. Clients -->
                    <li class="nav-item">
                        <a href="<?= base_url('client') ?>" class="nav-link <?= $isClientActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-handshake"></i>
                            <p>Clients</p>
                        </a>
                    </li>

                    <!-- 2. Employees -->
                    <li class="nav-item">
                        <a href="<?= base_url('employee') ?>" class="nav-link <?= $isEmployeeActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Employees</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('department') ?>" class="nav-link <?= $isDepartmentActive ? 'active' : '' ?>">
                            <i class="fas fa-sitemap nav-icon"></i>
                            <p>Departments</p>
                        </a>
                    </li>
                    <!-- 3. Client Payments -->
                    <li class="nav-item">
                        <a href="<?= base_url('client-payment/list') ?>" class="nav-link <?= $isClientPaymentsActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-money-bill-wave"></i>
                            <p> Payments</p>
                        </a>
                    </li>

                    <!-- 4. Monthly Reports -->
                    <li class="nav-item">
                        <a href="<?= base_url('client-report') ?>" class="nav-link <?= $isClientReportsActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Monthly Reports</p>
                        </a>
                    </li>

                    <!-- 5. Daily Works -->
                    <li class="nav-item">
                        <a href="<?= base_url('task-management') ?>" class="nav-link <?= $isTaskManagementActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-day"></i>
                            <p>Daily Works</p>
                        </a>
                    </li>

                    <!-- 6. Weekly Schedules -->
                    <li class="nav-item">
                        <a href="<?= base_url('weekly-schedule') ?>" class="nav-link <?= $isWeeklyScheduleActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-week"></i>
                            <p>Weekly Schedules</p>
                        </a>
                    </li>



                    <!-- 8. Project Details -->
                    <li class="nav-item">
                        <a href="<?= base_url('maintenance') ?>" class="nav-link <?= url_is('maintenance*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-project-diagram"></i>
                            <p>Project Details</p>
                        </a>
                    </li>

                    <!-- 9. Client Uploads -->
                    <li class="nav-item">
                        <a href="<?= base_url('client-uploads') ?>" class="nav-link <?= url_is('client-uploads*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-cloud-download-alt"></i>
                            <p>Client Uploads</p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a href="<?= base_url('social-media-calendar') ?>" class="nav-link <?= url_is('social-media-calendar*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Social Media Calendars</p>
                        </a>
                    </li>


                    <!-- NEW: Leave Management -->
                    <li class="nav-item">
                        <a href="<?= base_url('leave-management') ?>" class="nav-link <?= url_is('leave-management*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>
                                Leave Management
                                <?php
                                // Get pending leave count
                                $leaveModel = new \App\Models\LeaveRequestModel();
                                $pendingCount = $leaveModel->getPendingCount();
                                if ($pendingCount > 0):
                                ?>
                                    <span class="badge badge-warning right"><?= $pendingCount ?></span>
                                <?php endif; ?>
                            </p>
                        </a>
                    </li>


                    <!-- Holiday Management -->
                    <li class="nav-item">
                        <a href="<?= base_url('holidays') ?>" class="nav-link <?= url_is('holidays*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-day"></i>
                            <p>Holiday Management</p>
                        </a>
                    </li>

                    <!-- 9. Company Assets (Dropdown Menu) -->
                    <!-- <?php
                            $isCompanyAssetsActive = url_is('department*') || url_is('roles*') || url_is('user-management*');
                            ?>
                    <li class="nav-item <?= $isCompanyAssetsActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isCompanyAssetsActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-building"></i>
                            <p>
                                Dept. & Roles
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('department') ?>" class="nav-link <?= $isDepartmentActive ? 'active' : '' ?>">
                                    <i class="fas fa-sitemap nav-icon"></i>
                                    <p>Departments</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('roles') ?>" class="nav-link <?= $isRolesActive ? 'active' : '' ?>">
                                    <i class="fas fa-users-cog nav-icon"></i>
                                    <p>Role Management</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('user-management') ?>" class="nav-link <?= url_is('user-management*') ? 'active' : '' ?>">
                                    <i class="fas fa-user-shield nav-icon"></i>
                                    <p>User Management</p>
                                </a>
                            </li>
                        </ul>
                    </li> -->







                <?php endif; ?>




                <!-- ADMIN MANAGER PANEL (role_id = 5) - LIMITED ACCESS -->
                <?php if ($userRoleId == 5): ?>
                    <li class="nav-header">ADMIN MANAGER PANEL</li>

                    <!-- 1. Employees -->
                    <li class="nav-item">
                        <a href="<?= base_url('employee') ?>" class="nav-link <?= $isEmployeeActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Employees</p>
                        </a>
                    </li>

                    <!-- 2. Payslips -->
                    <li class="nav-item">
                        <a href="<?= base_url('employee-payslip') ?>" class="nav-link <?= $isPayslipsActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>
                            <p>Payslips</p>
                        </a>
                    </li>

                    <!-- 3. Daily Works -->
                    <li class="nav-item">
                        <a href="<?= base_url('task-management') ?>" class="nav-link <?= $isTaskManagementActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-day"></i>
                            <p>Daily Works</p>
                        </a>
                    </li>

                    <!-- 4. Clients -->
                    <li class="nav-item">
                        <a href="<?= base_url('client') ?>" class="nav-link <?= $isClientActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-handshake"></i>
                            <p>Clients</p>
                        </a>
                    </li>

                    <!-- 5. Monthly Reports -->
                    <li class="nav-item">
                        <a href="<?= base_url('client-report') ?>" class="nav-link <?= $isClientReportsActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Monthly Reports</p>
                        </a>
                    </li>

                    <!-- 6. Weekly Schedules -->
                    <li class="nav-item">
                        <a href="<?= base_url('weekly-schedule') ?>" class="nav-link <?= $isWeeklyScheduleActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-week"></i>
                            <p>Weekly Schedules</p>
                        </a>
                    </li>
                    <!-- Client Uploads & Assets (Other Assets) -->
                    <li class="nav-item">
                        <a href="<?= base_url('client-uploads') ?>" class="nav-link <?= url_is('client-uploads*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-cloud-download-alt"></i>
                            <p>Client Uploads</p>
                        </a>
                    </li>
                    <!-- 7. Client Assets -->
                    <li class="nav-item">
                        <a href="<?= base_url('client-assets') ?>" class="nav-link <?= url_is('client-assets*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-folder-open"></i>
                            <p>Client Assets</p>
                        </a>
                    </li>

                    <!-- Holiday Management -->
                    <li class="nav-item">
                        <a href="<?= base_url('holidays') ?>" class="nav-link <?= url_is('holidays*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-day"></i>
                            <p>Holiday Management</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- EMPLOYEE PANEL (role_id = 2) -->
                <?php if ($userRoleId == 2): ?>
                    <li class="nav-header">EMPLOYEE PANEL</li>

                    <li class="nav-item">
                        <a href="<?= base_url('employee-client-assets') ?>" class="nav-link <?= url_is('employee-client-assets*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-folder-open"></i>
                            <p>Client Assets</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('my-tasks') ?>" class="nav-link <?= url_is('my-tasks*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>My Tasks</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('my-payslips') ?>" class="nav-link <?= url_is('my-payslips*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-money-check-alt"></i>
                            <p>My Payslips</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('my-details') ?>" class="nav-link <?= url_is('my-details*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-id-card"></i>
                            <p>My Details</p>
                        </a>
                    </li>


                    <!-- NEW: My Leaves -->
                    <li class="nav-item">
                        <a href="<?= base_url('my-leaves') ?>" class="nav-link <?= url_is('my-leaves*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>My Leave Requests</p>
                        </a>
                    </li>

                    <!-- Quick Apply Button (Optional) -->
                    <li class="nav-item">
                        <a href="<?= base_url('my-leaves/apply') ?>" class="nav-link">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p>Apply for Leave</p>
                        </a>
                    </li>


                    <!-- View Holidays -->
                    <li class="nav-item">
                        <a href="<?= base_url('holidays-list') ?>" class="nav-link <?= url_is('holidays-list*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-day"></i>
                            <p>Holidays</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- CLIENT + CLIENT MANAGER -->
                <?php if (in_array($userRoleId, [3, 4])): ?>
                    <li class="nav-header">CLIENT PANEL</li>

                    <li class="nav-item">
                        <a href="<?= base_url('work-updates') ?>" class="nav-link <?= url_is('work-updates*') || url_is('view-work*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>Daily Works</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('my-weekly-schedule') ?>" class="nav-link <?= $isWeeklyScheduleActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-week"></i>
                            <p>Weekly Schedule</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('download-files') ?>" class="nav-link <?= url_is('download-files*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Working Calendar</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= base_url('self-uploads') ?>" class="nav-link <?= url_is('self-uploads*') ? 'active' : '' ?>">
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

                    <li class="nav-item">
                        <a href="<?= base_url('holidays-list') ?>" class="nav-link <?= url_is('holidays-list*') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-calendar-day"></i>
                            <p>Company Holidays</p>
                        </a>
                    </li>

                    <?php if ($userRoleId == 3): ?>
                        <!-- ONLY SHOW PROJECT DETAILS FOR CLIENTS (role 3), NOT CLIENT MANAGERS (role 4) -->
                        <li class="nav-item">
                            <a href="<?= base_url('client-maintenance') ?>" class="nav-link <?= url_is('client-maintenance*') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-project-diagram"></i>
                                <p>Project Details</p>
                            </a>
                        </li>

                        <!-- NEW: PAYMENT INFORMATION -->
                        <li class="nav-item">
                            <a href="<?= base_url('my-payments') ?>" class="nav-link <?= url_is('my-payments*') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-rupee-sign"></i>
                                <p>Payment Information</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= base_url('my-social-media-calendar') ?>" class="nav-link <?= url_is('my-social-media-calendar*') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-calendar-alt"></i>
                                <p>Social Media Calendar</p>
                            </a>
                        </li>
                    <?php endif; ?>

                <?php endif; ?>


                <!-- <li class="nav-item mt-3">
                    <a href="<?= base_url('logout') ?>" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li> -->
            </ul>
        </nav>
    </div>
</aside>
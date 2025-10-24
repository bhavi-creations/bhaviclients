<aside class="app-sidebar side_bg shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="<?= base_url('/') ?>" class="brand-link">
            <span class="brand-text">Bhavi clients</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                
                <?php 
                // Define active state variables based on the current URL
                $currentUri = uri_string();
                $isDepartmentActive = url_is('department*'); 
                $isClientActive = url_is('client*'); 

                // New active state for the tasks link
                $isTasksActive = url_is('employee/tasks*'); 

                // New active state for the roles link
                $isRolesActive = url_is('roles*'); // <--- NEW LINE

                // Updated Employee link: active on any 'employee*' route, but NOT if it's the tasks route
                $isEmployeeActive = url_is('employee*') && !$isTasksActive; 

                // Check if the current URI is 'dashboard' or the root path
                $isDashboardActive = $currentUri == 'dashboard' || $currentUri == ''; 
                ?>

                <li class="nav-item">
                    <a href="<?= base_url('dashboard') ?>" class="nav-link <?= $isDashboardActive ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-chart-bar"></i> 
                        <p>Dashboard</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('department') ?>" class="nav-link <?= $isDepartmentActive ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-sitemap"></i>
                        <p>Employee Departments</p>
                    </a>
                </li>
                
                <!-- NEW: Roles Management Link -->
                <li class="nav-item">
                    <a href="<?= base_url('roles') ?>" class="nav-link <?= $isRolesActive ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-users-cog"></i> <!-- Using 'users-cog' for roles/management -->
                        <p>Roles Management</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('employee') ?>" class="nav-link <?= $isEmployeeActive ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Employees</p>
                    </a>
                </li>
                
                <!-- Employee Task Sheet -->
                <li class="nav-item">
                    <a href="<?= base_url('employee/tasks') ?>" class="nav-link <?= $isTasksActive ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tasks"></i> 
                        <p>Employee Task Sheet</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?= base_url('client') ?>" class="nav-link <?= $isClientActive ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-handshake"></i>
                        <p>Clients</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('logout') ?>" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>

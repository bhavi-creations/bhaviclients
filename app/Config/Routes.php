<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultController('Auth');
$routes->setDefaultMethod('login');

// 1. PUBLIC AUTH ROUTES (NO FILTERS)
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('authenticate', 'Auth::authenticate');
$routes->get('logout', 'Auth::logout');

// 2. DASHBOARD (All authenticated users)
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// 3. ADMIN + ADMIN MANAGER (role_id = 1, 5) - FULL ACCESS
$routes->group('/', ['filter' => 'auth:1,5'], function ($routes) {
    // DEPARTMENTS
    $routes->group('department', function ($routes) {
        $routes->get('', 'Department::index');
        $routes->get('create', 'Department::create');
        $routes->post('store', 'Department::store');
        $routes->get('edit/(:num)', 'Department::edit/$1');
        $routes->post('update/(:num)', 'Department::update/$1');
        $routes->post('delete/(:num)', 'Department::delete/$1');
    });

    $routes->group('user-management', function ($routes) {
        $routes->get('', 'UserManagement::index');
        $routes->get('create', 'UserManagement::create');
        $routes->post('store', 'UserManagement::store');
        $routes->post('delete/(:num)', 'UserManagement::delete/$1');
    });


    // EMPLOYEE
    $routes->group('employee', function ($routes) {
        $routes->get('', 'Employee::index');
        $routes->get('create', 'Employee::create');
        $routes->post('store', 'Employee::store');
        $routes->get('edit/(:num)', 'Employee::edit/$1');
        $routes->post('update/(:num)', 'Employee::update/$1');
        $routes->post('delete/(:num)', 'Employee::delete/$1');
    });

    // CLIENT
    $routes->group('client', function ($routes) {
        $routes->get('', 'Client::index');
        $routes->get('create', 'Client::create');
        $routes->post('store', 'Client::store');
        $routes->get('edit/(:num)', 'Client::edit/$1');
        $routes->post('update/(:num)', 'Client::update/$1');
        $routes->post('delete/(:num)', 'Client::delete/$1');
        $routes->get('files/(:num)', 'Client::files/$1');
        $routes->post('upload/(:num)', 'Client::upload/$1');
        $routes->get('download/(:num)', 'Client::downloadFile/$1');
        $routes->post('deleteFile/(:num)', 'Client::deleteFile/$1');
    });

    // ROLES
    $routes->group('roles', function ($routes) {
        $routes->get('', 'RoleController::index');
        $routes->get('create', 'RoleController::create');
        $routes->post('store', 'RoleController::store');
        $routes->get('edit/(:num)', 'RoleController::edit/$1');
        $routes->post('update/(:num)', 'RoleController::update/$1');
        $routes->get('delete/(:num)', 'RoleController::delete/$1');
    });

    // EMPLOYEE TASKS
    $routes->group('task-management', function ($routes) {
        $routes->get('', 'TaskManagement::index');
        $routes->get('view/(:num)', 'TaskManagement::view/$1');
        $routes->post('update-status/(:num)', 'TaskManagement::updateStatus/$1');
        $routes->post('delete/(:num)', 'TaskManagement::delete/$1');
        $routes->get('filter', 'TaskManagement::filter');
    });
});

// 4. EMPLOYEE ROUTES (role_id = 2)
$routes->group('/', ['filter' => 'auth:2'], function ($routes) {
    $routes->get('employee-dashboard', 'EmployeeDashboard::index');
    $routes->get('my-tasks', 'EmployeeDashboard::myTasks');
    $routes->get('submit-work', 'EmployeeDashboard::submitWork');
    $routes->post('store-work', 'EmployeeDashboard::storeWork');
    $routes->get('edit-task/(:num)', 'EmployeeDashboard::editTask/$1');
    $routes->post('update-task/(:num)', 'EmployeeDashboard::updateTask/$1');
    $routes->post('delete-task/(:num)', 'EmployeeDashboard::deleteTask/$1');
    $routes->get('delete-file/(:num)/(:num)', 'EmployeeDashboard::deleteFile/$1/$2');
  
});

// 5. CLIENT + CLIENT MANAGER (role_id = 3, 4) routes
$routes->group('/', ['filter' => 'auth:3,4'], function ($routes) {
    // CLIENT DASHBOARD
    $routes->get('client-dashboard', 'ClientDashboard::index');
    $routes->get('work-updates', 'ClientDashboard::workUpdates');
    $routes->get('view-work/(:num)', 'ClientDashboard::viewTask/$1');
    $routes->get('download-files', 'ClientDashboard::downloadFiles');
    $routes->get('self-delete/(:num)', 'ClientDashboard::deleteSelfUpload/$1');
    $routes->get('self-uploads', 'ClientDashboard::selfUploads');
    $routes->get('download-file/(:num)', 'ClientDashboard::downloadFile/$1');
    $routes->get('upload-files', 'ClientDashboard::uploadFiles');
    $routes->post('store-files', 'ClientDashboard::storeFiles');
    // CLIENT MANAGER
    $routes->get('manager-dashboard', 'ClientManager::index');
    $routes->get('manager/clients', 'ClientManager::clients');
    $routes->get('manager/work-updates', 'ClientManager::workUpdates');
    $routes->get('manager/upload-files', 'ClientManager::uploadFiles');
    $routes->post('manager/store-files', 'ClientManager::storeFiles');
    $routes->get('manager/client-files/(:num)', 'ClientManager::clientFiles/$1');
    $routes->get('manager/download-file/(:num)', 'ClientManager::downloadFile/$1');
    $routes->post('manager/delete-file/(:num)', 'ClientManager::deleteFile/$1');
    $routes->group('client', function ($routes) {
        $routes->get('', 'Client::index');
        $routes->get('edit/(:num)', 'Client::edit/$1');
        $routes->post('update/(:num)', 'Client::update/$1');
    });
});

// 6. CLIENT UPLOADS (ALL LOGGED-IN ROLES, UI/SECURITY IN CONTROLLER)
$routes->get('client-uploads', 'ClientUploads::index', ['filter' => 'auth']);
$routes->get('client-uploads/by-client/(:num)', 'ClientUploads::byClient/$1', ['filter' => 'auth']);
$routes->get('client-uploads/download/(:num)', 'ClientUploads::download/$1', ['filter' => 'auth']);
$routes->post('client-uploads/delete/(:num)', 'ClientUploads::delete/$1', ['filter' => 'auth']);

// 7. PROFILE - (ALL AUTHED)
$routes->group('profile', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'Profile::index');
    $routes->get('edit', 'Profile::edit');
    $routes->post('update', 'Profile::update');
    $routes->get('change-password', 'Profile::changePassword');
    $routes->post('update-password', 'Profile::updatePassword');
});




$routes->get('maintenance', 'Maintenance::index', ['filter' => 'auth:1']);
$routes->get('maintenance/client/(:num)', 'Maintenance::client/$1', ['filter' => 'auth:1']);
$routes->get('maintenance/create', 'Maintenance::create', ['filter' => 'auth:1']);
$routes->post('maintenance/store', 'Maintenance::store', ['filter' => 'auth:1']);
$routes->get('maintenance/edit/(:num)', 'Maintenance::edit/$1', ['filter' => 'auth:1']);
$routes->post('maintenance/update/(:num)', 'Maintenance::update/$1', ['filter' => 'auth:1']);
$routes->post('maintenance/delete/(:num)', 'Maintenance::delete/$1', ['filter' => 'auth:1']);
$routes->get('client-maintenance', 'Maintenance::clientView', ['filter' => 'auth:3,4']);

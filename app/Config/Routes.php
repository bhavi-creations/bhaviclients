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



    // HOLIDAY MANAGEMENT (Admins and Managers)
    $routes->group('holidays', ['filter' => 'auth:1,5'], function ($routes) {
        $routes->get('', 'HolidayManagement::index');
        $routes->get('create', 'HolidayManagement::create');
        $routes->post('store', 'HolidayManagement::store');
        $routes->get('edit/(:num)', 'HolidayManagement::edit/$1');
        $routes->post('update/(:num)', 'HolidayManagement::update/$1');
        $routes->get('delete/(:num)', 'HolidayManagement::delete/$1');
    });





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
        $routes->post('delete/(:num)', 'UserManagement::delete/$1');
    });

    // LEAVE MANAGEMENT (Admin)
    $routes->group('leave-management', function ($routes) {
        $routes->get('', 'LeaveManagement::index');
        $routes->get('employee/(:num)', 'LeaveManagement::employeeLeaves/$1');
        $routes->get('view/(:num)', 'LeaveManagement::viewLeave/$1');
        $routes->post('update-status/(:num)', 'LeaveManagement::updateStatus/$1');
        $routes->get('delete/(:num)', 'LeaveManagement::delete/$1');
    });


    // SOCIAL MEDIA CALENDAR (Admin)
    $routes->group('social-media-calendar', function ($routes) {
        $routes->get('', 'SocialMediaCalendar::index');
        $routes->get('upload', 'SocialMediaCalendar::upload');
        $routes->get('upload/(:num)', 'SocialMediaCalendar::upload/$1');
        $routes->post('store', 'SocialMediaCalendar::store');
        $routes->post('update', 'SocialMediaCalendar::update');
        $routes->get('client/(:num)', 'SocialMediaCalendar::clientCalendars/$1');
        $routes->get('download/(:num)', 'SocialMediaCalendar::download/$1');
        $routes->get('delete/(:num)', 'SocialMediaCalendar::delete/$1');
        $routes->get('view/(:num)', 'SocialMediaCalendar::view/$1', ['filter' => 'auth']);
    });

    // EMPLOYEE
    $routes->group('employee', function ($routes) {
        $routes->get('', 'Employee::index');
        $routes->get('create', 'Employee::create');
        $routes->post('store', 'Employee::store');
        $routes->get('view/(:num)', 'Employee::view/$1');
        $routes->get('edit/(:num)', 'Employee::edit/$1');
        $routes->post('update/(:num)', 'Employee::update/$1');
        $routes->post('delete/(:num)', 'Employee::delete/$1');
        $routes->post('uploadFiles/(:num)', 'Employee::uploadFiles/$1');
        $routes->get('downloadFile/(:num)/(:any)', 'Employee::downloadFile/$1/$2');
        $routes->post('deleteFile/(:num)/(:any)', 'Employee::deleteFile/$1/$2');
        $routes->post('addSalary/(:num)', 'Employee::addSalary/$1');
        $routes->post('editSalary/(:num)', 'Employee::editSalary/$1');  // <-- ADD THIS LINE
        $routes->post('deleteSalary/(:num)', 'Employee::deleteSalary/$1');
    });






    // CLIENT
    $routes->group('client', function ($routes) {
        $routes->get('', 'Client::index');
        $routes->get('create', 'Client::create');
        $routes->post('store', 'Client::store');
        $routes->get('view/(:num)', 'Client::view/$1');
        $routes->get('edit/(:num)', 'Client::edit/$1');
        $routes->post('update/(:num)', 'Client::update/$1');
        $routes->post('delete/(:num)', 'Client::delete/$1');
        $routes->get('files/(:num)', 'Client::files/$1');
        $routes->post('upload/(:num)', 'Client::upload/$1');
        $routes->get('download/(:num)', 'Client::downloadFile/$1');
        $routes->post('deleteFile/(:num)', 'Client::deleteFile/$1');
    });


    // CLIENT PAYMENT MANAGEMENT
    $routes->group('client-payment', function ($routes) {
        $routes->get('list', 'ClientPayment::list');
        $routes->get('(:num)', 'ClientPayment::index/$1');

        // Project Management
        $routes->post('add-project/(:num)', 'ClientPayment::addProject/$1');
        $routes->post('update-project-value/(:num)', 'ClientPayment::updateProjectValue/$1');
        $routes->post('update-timeline/(:num)', 'ClientPayment::updateTimeline/$1');

        // Payment Management
        $routes->post('add-payment/(:num)', 'ClientPayment::addPayment/$1');
        $routes->post('edit-payment/(:num)', 'ClientPayment::editPayment/$1');
        $routes->get('delete-payment/(:num)', 'ClientPayment::deletePayment/$1');
        $routes->get('download-payment-file/(:num)', 'ClientPayment::downloadPaymentFile/$1');

        // Schedule Management
        $routes->post('add-schedule/(:num)', 'ClientPayment::addSchedule/$1');
        $routes->post('edit-schedule/(:num)', 'ClientPayment::editSchedule/$1');
        $routes->get('delete-schedule/(:num)', 'ClientPayment::deleteSchedule/$1');
        $routes->get('download-schedule-file/(:num)', 'ClientPayment::downloadScheduleFile/$1');
        $routes->post('update-schedule-status/(:num)', 'ClientPayment::updateScheduleStatus/$1');
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

    // CLIENT REPORTS
    $routes->group('client-report', function ($routes) {
        $routes->get('', 'ClientReport::index');
        $routes->get('create', 'ClientReport::create');
        $routes->post('store', 'ClientReport::store');
        $routes->get('view/(:num)', 'ClientReport::view/$1');
        $routes->get('edit/(:num)', 'ClientReport::edit/$1');
        $routes->post('update/(:num)', 'ClientReport::update/$1');
        $routes->get('downloadFile/(:num)/(:any)', 'ClientReport::downloadFile/$1/$2');
        $routes->post('deleteFile/(:num)/(:any)', 'ClientReport::deleteFile/$1/$2');
        $routes->post('delete/(:num)', 'ClientReport::delete/$1');
    });
    // WEEKLY SCHEDULES (Admin & Admin Manager)
    $routes->group('weekly-schedule', function ($routes) {
        $routes->get('', 'ClientWeeklySchedule::index');
        $routes->get('client/(:num)', 'ClientWeeklySchedule::clientSchedules/$1'); // <-- ADD THIS
        $routes->get('create', 'ClientWeeklySchedule::create');
        $routes->post('store', 'ClientWeeklySchedule::store');
        $routes->get('view/(:num)', 'ClientWeeklySchedule::view/$1');
        $routes->get('edit/(:num)', 'ClientWeeklySchedule::edit/$1');
        $routes->post('update/(:num)', 'ClientWeeklySchedule::update/$1');
        $routes->post('delete/(:num)', 'ClientWeeklySchedule::delete/$1');
    });


    // EMPLOYEE PAYSLIPS (Admin & Admin Manager)
    $routes->group('employee-payslip', function ($routes) {
        $routes->get('', 'EmployeePayslip::index');
        $routes->get('employee/(:num)', 'EmployeePayslip::employeePayslips/$1');
        $routes->get('create', 'EmployeePayslip::create');
        $routes->post('store', 'EmployeePayslip::store');
        $routes->get('edit/(:num)', 'EmployeePayslip::edit/$1');
        $routes->post('update/(:num)', 'EmployeePayslip::update/$1');
        $routes->post('delete/(:num)', 'EmployeePayslip::delete/$1');
        $routes->get('download/(:num)', 'EmployeePayslip::download/$1');
    });
    // TASK MANAGEMENT (Admin & Admin Manager)
    $routes->group('task-management', function ($routes) {
        $routes->get('', 'TaskManagement::index');
        $routes->get('create', 'TaskManagement::create');
        $routes->post('store', 'TaskManagement::store');
        $routes->get('view/(:num)', 'TaskManagement::view/$1');
        $routes->get('edit/(:num)', 'TaskManagement::edit/$1');
        $routes->post('update/(:num)', 'TaskManagement::update/$1');
        $routes->post('updateStatus/(:num)', 'TaskManagement::updateStatus/$1');
        $routes->post('delete/(:num)', 'TaskManagement::delete/$1');
    });




    // EMPLOYEE TASKS
    // $routes->group('task-management', function ($routes) {
    //     $routes->get('', 'TaskManagement::index');
    //     $routes->get('view/(:num)', 'TaskManagement::view/$1');
    //     $routes->post('update-status/(:num)', 'TaskManagement::updateStatus/$1');
    //     $routes->post('delete/(:num)', 'TaskManagement::delete/$1');
    //     $routes->get('filter', 'TaskManagement::filter');
    // });
});

// 5. ADMIN MANAGER ROUTES (role_id = 5)
$routes->group('/', ['filter' => 'auth:5'], function ($routes) {
    // Existing routes...

    // Client Assets Routes - NEW
    $routes->get('client-assets', 'ClientAsset::index');
    $routes->get('client-assets/create', 'ClientAsset::create');
    $routes->post('client-assets/store', 'ClientAsset::store');
    $routes->get('client-assets/view/(:num)', 'ClientAsset::view/$1');
    $routes->get('client-assets/edit/(:num)', 'ClientAsset::edit/$1');
    $routes->post('client-assets/update/(:num)', 'ClientAsset::update/$1');
    $routes->post('client-assets/delete/(:num)', 'ClientAsset::delete/$1');
    $routes->get('client-assets/download/(:segment)/(:any)', 'ClientAsset::downloadFile/$1/$2');
});


// 4. EMPLOYEE ROUTES (role_id = 2)
// $routes->group('/', ['filter' => 'auth:2'], function ($routes) {
//     $routes->get('employee-dashboard', 'EmployeeDashboard::index');
//     $routes->get('my-tasks', 'EmployeeDashboard::myTasks');
//     $routes->get('submit-work', 'EmployeeDashboard::submitWork');
//     $routes->post('store-work', 'EmployeeDashboard::storeWork');
//     $routes->get('edit-task/(:num)', 'EmployeeDashboard::editTask/$1');
//     $routes->post('update-task/(:num)', 'EmployeeDashboard::updateTask/$1');
//     $routes->post('delete-task/(:num)', 'EmployeeDashboard::deleteTask/$1');
//     $routes->get('delete-file/(:num)/(:num)', 'EmployeeDashboard::deleteFile/$1/$2');

// });




// 4. EMPLOYEE ROUTES (role_id = 2)
$routes->group('/', ['filter' => 'auth:2'], function ($routes) {
    $routes->get('employee-dashboard', 'EmployeeDashboard::index');

    // My Tasks
    $routes->get('my-tasks', 'EmployeeDashboard::myTasks');
    $routes->get('my-tasks/view/(:num)', 'EmployeeDashboard::viewTask/$1');
    $routes->get('my-tasks/submit/(:num)', 'EmployeeDashboard::submitWorkForm/$1');
    $routes->post('my-tasks/store-work/(:num)', 'EmployeeDashboard::storeTaskWork/$1');
    $routes->post('my-tasks/update-status/(:num)', 'EmployeeDashboard::updateTaskStatus/$1');
    $routes->get('my-tasks/edit/(:num)', 'EmployeeDashboard::editTaskWork/$1');
    $routes->post('my-tasks/update-work/(:num)', 'EmployeeDashboard::updateTaskWork/$1');

    // My Payslips
    $routes->get('my-payslips', 'EmployeePayslipView::index');
    $routes->get('my-payslips/download/(:num)', 'EmployeePayslipView::download/$1');

    // My Details - NEW
    $routes->get('my-details', 'EmployeeDetails::index');
    $routes->get('my-details/download-file/(:num)', 'EmployeeDetails::downloadFile/$1');


    // Client Assets (View Only) - NEW
    $routes->get('employee-client-assets', 'EmployeeClientAssets::index');
    $routes->get('employee-client-assets/view/(:num)', 'EmployeeClientAssets::view/$1');
    $routes->get('employee-client-assets/download/(:segment)/(:any)', 'EmployeeClientAssets::downloadFile/$1/$2');


    // Old task routes (backward compatibility)
    $routes->get('submit-work', 'EmployeeDashboard::submitWork');
    $routes->post('store-work', 'EmployeeDashboard::storeWork');
    $routes->get('edit-task/(:num)', 'EmployeeDashboard::editTask/$1');
    $routes->post('update-task/(:num)', 'EmployeeDashboard::updateTask/$1');
    $routes->post('delete-task/(:num)', 'EmployeeDashboard::deleteTask/$1');
    $routes->get('delete-file/(:num)/(:num)', 'EmployeeDashboard::deleteFile/$1/$2');



    // MY LEAVES (Employee)
    $routes->group('my-leaves', function ($routes) {
        $routes->get('', 'EmployeeLeave::index');
        $routes->get('apply', 'EmployeeLeave::apply');
        $routes->post('store', 'EmployeeLeave::store');
        $routes->get('edit/(:num)', 'EmployeeLeave::edit/$1');
        $routes->post('update/(:num)', 'EmployeeLeave::update/$1');
        $routes->get('delete/(:num)', 'EmployeeLeave::delete/$1');
        $routes->get('view/(:num)', 'EmployeeLeave::view/$1');
    });
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



    // SOCIAL MEDIA CALENDAR (Client Read-Only)
    $routes->get('my-social-media-calendar', 'ClientSocialMediaCalendar::index');
    $routes->get('my-social-media-calendar/download/(:num)', 'ClientSocialMediaCalendar::download/$1');
    $routes->get('my-social-media-calendar/view/(:num)', 'ClientSocialMediaCalendar::view/$1');


 



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

    // Project Details - NEW
    $routes->get('client-maintenance', 'ClientDashboard::clientMaintenance');
    $routes->get('view-project-detail/(:num)', 'ClientDashboard::viewProjectDetail/$1');
    $routes->get('download-maintenance-file/(:num)/(:any)', 'ClientDashboard::downloadMaintenanceFile/$1/$2');


    // CLIENT WEEKLY SCHEDULE VIEW
    $routes->get('my-weekly-schedule', 'ClientDashboard::myWeeklySchedule');
    $routes->get('view-weekly-schedule/(:num)', 'ClientDashboard::viewWeeklySchedule/$1');

    // CLIENT PAYMENT VIEW (Read-Only for Clients - Role ID 3)
    $routes->group('my-payments', function ($routes) {
        $routes->get('/', 'ClientPaymentView::index');
        $routes->get('download-payment-file/(:num)', 'ClientPaymentView::downloadPaymentFile/$1');
        $routes->get('download-schedule-file/(:num)', 'ClientPaymentView::downloadScheduleFile/$1');
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
    $routes->get('edit-username', 'Profile::editUsername');
    $routes->post('update-username', 'Profile::updateUsername');   // ✅ ADD THIS
    $routes->get('change-password', 'Profile::changePassword');
    $routes->post('update-password', 'Profile::updatePassword');
});







$routes->get('maintenance', 'Maintenance::index', ['filter' => 'auth:1']);
$routes->get('maintenance/client/(:num)', 'Maintenance::client/$1', ['filter' => 'auth:1']);
$routes->get('maintenance/view/(:num)', 'Maintenance::view/$1', ['filter' => 'auth:1,3,4']);
$routes->get('maintenance/downloadFile/(:num)/(:any)', 'Maintenance::downloadFile/$1/$2', ['filter' => 'auth:1,3,4']);
$routes->get('maintenance/deleteFile/(:num)/(:any)', 'Maintenance::deleteFile/$1/$2', ['filter' => 'auth:1']);
$routes->get('maintenance/create', 'Maintenance::create', ['filter' => 'auth:1']);
$routes->post('maintenance/store', 'Maintenance::store', ['filter' => 'auth:1']);
$routes->get('maintenance/edit/(:num)', 'Maintenance::edit/$1', ['filter' => 'auth:1']);
$routes->post('maintenance/update/(:num)', 'Maintenance::update/$1', ['filter' => 'auth:1']);
$routes->post('maintenance/delete/(:num)', 'Maintenance::delete/$1', ['filter' => 'auth:1']);
$routes->get('client-maintenance', 'Maintenance::clientView', ['filter' => 'auth:3,4']);







// NOTIFICATIONS (All authenticated users)
$routes->get('notifications', 'Notifications::index', ['filter' => 'auth']);
$routes->get('notifications/mark-read/(:num)', 'Notifications::markAsRead/$1', ['filter' => 'auth']);
$routes->get('notifications/mark-all-read', 'Notifications::markAllAsRead', ['filter' => 'auth']);






// Holidays (public view for all users)
$routes->get('holidays-list', 'HolidayManagement::index', ['filter' => 'auth']);



// FORGOT PASSWORD
$routes->get('forgot-password', 'ForgotPassword::index');
$routes->post('forgot-password/send', 'ForgotPassword::sendResetLink');
$routes->get('reset-password/(:any)', 'ForgotPassword::resetForm/$1');
$routes->post('reset-password/update', 'ForgotPassword::resetPassword');

<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Set a default controller if the root route fails to match anything else
$routes->setDefaultController('Auth');
$routes->setDefaultMethod('login');

// 1. --- PUBLIC AUTHENTICATION ROUTES (NO FILTERS APPLIED) ---
// These must be accessible to everyone, including unauthenticated users.
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('authenticate', 'Auth::authenticate');
$routes->get('logout', 'Auth::logout');


// 2. --- ADMIN / OWNER MANAGEMENT ROUTES (Filter: 'auth:admin') ---
// These routes require the user to be logged in AND have the 'admin' role.
$routes->group('/', ['filter' => 'auth:admin'], function ($routes) {

    // --- DEPARTMENTS MODULE ROUTES ---
    $routes->group('department', function ($routes) {
        $routes->get('', 'Department::index');
        $routes->get('create', 'Department::create');
        $routes->post('store', 'Department::store');
        $routes->get('edit/(:num)', 'Department::edit/$1');
        $routes->post('update/(:num)', 'Department::update/$1');
        // CRITICAL FIX: Delete should be a POST request
        $routes->post('delete/(:num)', 'Department::delete/$1');
    });

    // --- EMPLOYEE MANAGEMENT ROUTES ---
    $routes->group('employee', function ($routes) {
        $routes->get('', 'Employee::index');
        $routes->get('create', 'Employee::create');
        $routes->post('store', 'Employee::store'); // Correctly defined as POST
        $routes->get('edit/(:num)', 'Employee::edit/$1');
        $routes->post('update/(:num)', 'Employee::update/$1');
        // CRITICAL FIX: Delete should be a POST request
        $routes->post('delete/(:num)', 'Employee::delete/$1');
    });

    $routes->group('client', function ($routes) {
        $routes->get('', 'Client::index');
        $routes->get('create', 'Client::create');
        $routes->post('store', 'Client::store');
        $routes->get('edit/(:num)', 'Client::edit/$1');
        $routes->post('update/(:num)', 'Client::update/$1');
        $routes->post('delete/(:num)', 'Client::delete/$1');

        // File management routes
        $routes->get('files/(:num)', 'Client::files/$1');
        $routes->post('upload/(:num)', 'Client::upload/$1');
        $routes->get('download/(:num)', 'Client::downloadFile/$1');
        $routes->post('deleteFile/(:num)', 'Client::deleteFile/$1');
    });



    $routes->group('roles', function ($routes) {
        // --------------------------------------------------------------------
        // ROLE MANAGEMENT ROUTES
        // --------------------------------------------------------------------
        $routes->get('', 'RoleController::index');
        $routes->get('create', 'RoleController::create');
        $routes->post('store', 'RoleController::store');
        $routes->get('edit/(:num)', 'RoleController::edit/$1');
        $routes->post('update/(:num)', 'RoleController::update/$1');
        $routes->get('delete/(:num)', 'RoleController::delete/$1');
    });
});


// 3. --- GENERAL EMPLOYEE ROUTES (Filter: 'auth') ---
// These routes require any user (Admin or Employee) to be logged in.
$routes->group('/', ['filter' => 'auth'], function ($routes) {

    // Dashboard (accessible by both roles)
    $routes->get('dashboard', 'Home::index');

    // Work Submission Routes (Employee Specific)
    $routes->group('work', function ($routes) {
        $routes->get('mytasks', 'Work::myTasks');
        $routes->get('create', 'Work::create');
        $routes->post('store', 'Work::store');
        $routes->get('edit/(:num)', 'Work::edit/$1');
        $routes->post('update/(:num)', 'Work::update/$1');
        $routes->post('delete/(:num)', 'Work::delete/$1');
    });
});

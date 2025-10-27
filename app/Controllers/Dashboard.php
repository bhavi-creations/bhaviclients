<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\Department.php

namespace App\Controllers;

use App\Models\DepartmentModel;
use App\Models\EmployeeTaskModel; // or your actual Task model
use CodeIgniter\Controller;
use App\Models\ClientModel;
use App\Models\EmployeeModel;
use App\Models\UserModel;
use App\Models\ClientFileModel;

class Dashboard extends BaseController
{
    // Property to hold the Model instance
    protected $departmentModel;
    protected $employeeTaskModel;
    protected $clientModel;
    protected $employeeModel;
    protected $userModel;
    protected $clientFileModel;


    public function index()
    {
        $roleId = session()->get('role_id');
        if ($roleId == 1 || $roleId == 5) {
            $clientModel = new ClientModel();
            $employeeModel = new EmployeeModel();
            $userModel = new UserModel();
            $taskModel = new EmployeeTaskModel(); // Use your task model here!
            $fileModel = new ClientFileModel();

            $totalClients    = $clientModel->countAllResults();
            $totalEmployees  = $employeeModel->countAllResults();
            $totalUsers      = $userModel->countAllResults();
            $recentClients   = $clientModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
            $totalTasks      = $taskModel->countAllResults();      // <-- TASKS, not maintenance
            $totalFiles      = $fileModel->countAllResults();

            $data = [
                'title'          => 'Dashboard',
                'totalClients'   => $totalClients,
                'totalEmployees' => $totalEmployees,
                'totalUsers'     => $totalUsers,
                'recentClients'  => $recentClients,
                'totalTasks'     => $totalTasks,           // <-- changed
                'totalFiles'     => $totalFiles,
            ];

            return view('dashboard/index', $data);
        } else {
            // Redirect other role to their dashboard if required
            return redirect()->to('client-dashboard');
        }
    }
}

<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\Department.php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DepartmentModel;
use App\Models\EmployeeTaskModel; // or your actual Task model


class Department extends Controller
{
    // Property to hold the Model instance
    protected $departmentModel;    protected $employeeTaskModel;


    // Constructor to load the Model and Helpers
    public function __construct()
    {
        log_message('debug', 'ROLE CHECK: ' . print_r(session()->get(), true));

        $this->departmentModel = new DepartmentModel();
        helper(['form', 'url']);

        // Access control: Only allow admins and admin managers
        if (!in_array(session()->get('role_id'), [1, 5])) {
            // Use redirect if possible, fallback to exit for AJAX/not loaded helpers
            if (!function_exists('redirect')) {
                header('Location: ' . base_url('dashboard'));
                exit;
            } else {
                redirect()->to(base_url('dashboard'))->send();
                exit;
            }
        }
    }


    // R - Read (Displays the list)
    public function index()
    {
        $roleId = session()->get('role_id');
        $userId = session()->get('user_id');

        // For admin/admin manager: show all stats; for others, optionally redirect or adapt
        if ($roleId == 1 || $roleId == 5) {
            $clientModel = new \App\Models\ClientModel();
            $employeeModel = new \App\Models\EmployeeModel();
            $userModel = new \App\Models\UserModel();
            $maintenanceModel = new \App\Models\MaintenanceModel();
            $fileModel = new \App\Models\ClientFileModel();

            $totalClients = $clientModel->countAllResults();
            $totalEmployees = $employeeModel->countAllResults();
            $totalUsers = $userModel->countAllResults();
            $recentClients = $clientModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
            $maintenanceCount = $maintenanceModel->countAllResults();
            $totalFiles = $fileModel->countAllResults();

            $data = [
                'title' => 'Dashboard',
                'totalClients' => $totalClients,
                'totalEmployees' => $totalEmployees,
                'totalUsers' => $totalUsers,
                'recentClients' => $recentClients,
                'maintenanceCount' => $maintenanceCount,
                'totalFiles' => $totalFiles,
            ];

            return view('dashboard/index', $data);
        } else {
            // Optionally: redirect other roles or show minimal info
            return redirect()->to('client-dashboard');
        }
    }

    // C - Create (Displays the form)
    public function create()
    {
        $data['title'] = 'Add New Department';
        $data['validation'] = \Config\Services::validation();
        return view('department/create', $data);
    }

    // C - Create (Handles form submission and saving data)
    public function store()
    {
        // 1. Define Validation Rules
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
        ];

        // 2. Validate the request data
        if ($this->validate($rules)) {
            $data = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
            ];

            // 3. Save to database
            $this->departmentModel->save($data);

            // 4. Set success flash message and REDIRECT CORRECTLY
            session()->setFlashdata('message', 'Department added successfully!');
            return redirect()->to(base_url('department'));
        } else {
            // Validation failed, return to form with errors
            return view('department/create', [
                'title' => 'Add New Department',
                'validation' => $this->validator,
            ]);
        }
    }

    // E - Edit (Displays the edit form)
    public function edit($id = null)
    {
        // Find the record
        $department = $this->departmentModel->find($id);

        if (!$department) {
            session()->setFlashdata('error', 'Department not found for editing.');
            return redirect()->to(base_url('department'));
        }

        $data['title'] = 'Edit Department';
        $data['page_title'] = 'Edit Department'; // For the breadcrumb in the view
        $data['department'] = $department;
        $data['validation'] = \Config\Services::validation();

        return view('department/edit', $data);
    }

    // U - Update (Handles form submission and saving changes)
    public function update($id = null)
    {
        $department = $this->departmentModel->find($id);

        if (!$department) {
            session()->setFlashdata('error', 'Department not found for update.');
            return redirect()->to(base_url('department'));
        }

        $validation = \Config\Services::validation();

        $validation->setRules([
            'name' => "required|min_length[3]|max_length[255]|is_unique[departments.name,id,{$id}]",
            'description' => 'permit_empty'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return view('department/edit', [
                'title' => 'Edit Department',
                'page_title' => 'Edit Department',
                'department' => $department,
                'validation' => $validation
            ]);
        }

        $data = [
            'id' => $id,
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ];

        $this->departmentModel->save($data);

        session()->setFlashdata('message', 'Department updated successfully!');
        return redirect()->to(base_url('department'));
    }


    // D - Delete (Handles the deletion of a record)
    public function delete($id = null)
    {
        $department = $this->departmentModel->find($id);

        if (!$department) {
            session()->setFlashdata('error', 'Department not found for deletion.');
            return redirect()->to(base_url('department'));
        }

        // Delete the record
        $this->departmentModel->delete($id);

        session()->setFlashdata('message', 'Department deleted successfully!');
        return redirect()->to(base_url('department'));
    }
}

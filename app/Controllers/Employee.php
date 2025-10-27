<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeeModel;
use App\Models\DepartmentModel;
use App\Models\RoleModel;
use App\Models\UserModel;

class Employee extends BaseController
{
    protected $employeeModel;
    protected $departmentModel;
    protected $roleModel;
    protected $userModel;
    protected $validation;
    protected $session;
    protected $db;

   

    public function __construct()
    {
        $this->employeeModel   = new EmployeeModel();
        $this->departmentModel = new DepartmentModel();
        $this->roleModel       = new RoleModel();
        $this->userModel       = new UserModel();
        $this->session         = \Config\Services::session();
        $this->validation      = \Config\Services::validation();
        $this->db              = \Config\Database::connect();
        helper(['form', 'url']);

        // --- ACCESS CHECK FOR ADMIN + ADMIN MANAGER ---
        if (!in_array(session()->get('role_id'), [1, 5])) {
            if (!function_exists('redirect')) {
                header('Location: ' . base_url('dashboard'));
                exit;
            } else {
                redirect()->to(base_url('dashboard'))->send();
                exit;
            }
        }
    }


    /**
     * Display employee list
     */
    public function index()
    {
        $employees = $this->employeeModel
            ->select('employees.*, departments.name AS department_name, roles.name AS role_name')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->join('roles', 'roles.id = employees.role_id', 'left')
            ->findAll();

        $data['employees'] = $employees;
        $data['title'] = 'Employee List';
        return view('employee/index', $data);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $data['departments'] = $this->departmentModel->findAll();
        $data['roles'] = $this->roleModel->findAll();
        $data['title'] = 'Add New Employee';
        $data['validation'] = $this->validation;

        return view('employee/create', $data);
    }

    public function store()
    {
        // Validation Rules
        $rules = [
            'first_name'    => 'required|min_length[2]|max_length[100]',
            'last_name'     => 'required|min_length[2]|max_length[100]',
            'email'         => 'required|valid_email|is_unique[users.email]',
            'phone'         => 'required|numeric|min_length[10]|max_length[20]|is_unique[users.phone]',
            'department_id' => 'required|integer',
            'role_id'       => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validation->getErrors();
            log_message('error', 'Employee Validation Failed: ' . json_encode($errors));

            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validation)
                ->with('error', 'Validation failed. Please check the form.');
        }

        $input = $this->request->getPost();

        // Start transaction
        $this->db->transStart();

        try {
            // 1. Create User first (UserModel will hash password automatically)
            $userData = [
                'role_id'       => $input['role_id'],
                'first_name'    => trim($input['first_name']),
                'last_name'     => trim($input['last_name']),
                'email'         => trim($input['email']),
                'phone'         => trim($input['phone']),
                'department_id' => $input['department_id'],
                'username'      => trim($input['email']),
                'password'      => $input['phone'], // ← REMOVED password_hash() - Let UserModel handle it
                'created_at'    => date('Y-m-d H:i:s'),
            ];

            $this->userModel->insert($userData);
            $userId = $this->userModel->getInsertID();

            if (!$userId) {
                throw new \Exception('Failed to create user account.');
            }

            // 2. Create Employee with user_id
            $employeeData = [
                'user_id'       => $userId,
                'first_name'    => trim($input['first_name']),
                'last_name'     => trim($input['last_name']),
                'email'         => trim($input['email']),
                'phone'         => trim($input['phone']),
                'department_id' => $input['department_id'],
                'role_id'       => $input['role_id'],
                'created_at'    => date('Y-m-d H:i:s'),
            ];

            $this->employeeModel->insert($employeeData);
            $employeeId = $this->employeeModel->getInsertID();

            if (!$employeeId) {
                throw new \Exception('Failed to create employee record.');
            }

            // 3. Update user with employee_id
            $this->userModel->update($userId, ['employee_id' => $employeeId]);

            // Commit transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed.');
            }

            $this->session->setFlashdata(
                'success',
                'Employee created successfully! Username: ' . $input['email'] . ', Password: ' . $input['phone']
            );

            return redirect()->to(base_url('employee'));
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Employee Creation Error: ' . $e->getMessage());

            $this->session->setFlashdata('error', 'Failed to create employee: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }


    /**
     * Show edit form
     */
    public function edit($id = null)
    {
        $employee = $this->employeeModel->find($id);

        if (!$employee) {
            session()->setFlashdata('error', 'Employee not found.');
            return redirect()->to(base_url('employee'));
        }

        $data = [
            'title'       => 'Edit Employee',
            'employee'    => $employee,
            'departments' => $this->departmentModel->findAll(),
            'validation'  => \Config\Services::validation()
        ];

        return view('employee/edit', $data);
    }

    /**
     * Update employee
     */
    public function update($id = null)
    {
        $employee = $this->employeeModel->find($id);

        if (!$employee) {
            session()->setFlashdata('error', 'Employee not found.');
            return redirect()->to(base_url('employee'));
        }

        $userId = $employee['user_id'];

        // Validation with uniqueness excluding current record
        $rules = [
            'first_name'    => 'required|min_length[2]|max_length[100]',
            'last_name'     => 'required|min_length[2]|max_length[100]',
            'email'         => "required|valid_email|is_unique[users.email,id,{$userId}]",
            'phone'         => "required|numeric|min_length[10]|is_unique[users.phone,id,{$userId}]",
            'department_id' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validation->getErrors();
            log_message('error', 'Update Validation Failed: ' . json_encode($errors));

            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validation)
                ->with('error', 'Validation failed: ' . implode(', ', $errors));
        }

        $input = $this->request->getPost();

        // Start transaction
        $this->db->transStart();

        try {
            // Update employee
            $employeeData = [
                'first_name'    => trim($input['first_name']),
                'last_name'     => trim($input['last_name']),
                'email'         => trim($input['email']),
                'phone'         => trim($input['phone']),
                'department_id' => $input['department_id'],
                'updated_at'    => date('Y-m-d H:i:s'),
            ];

            // Use skipValidation to avoid model validation conflicts
            $this->employeeModel->skipValidation(true)->update($id, $employeeData);

            log_message('debug', 'Employee Update Query: ' . $this->db->getLastQuery());

            // Update user (WITHOUT password field - don't change password on update)
            if ($userId) {
                $userData = [
                    'first_name'    => trim($input['first_name']),
                    'last_name'     => trim($input['last_name']),
                    'email'         => trim($input['email']),
                    'username'      => trim($input['email']),
                    'phone'         => trim($input['phone']),
                    'department_id' => $input['department_id'],
                    'updated_at'    => date('Y-m-d H:i:s'),
                ];

                // Skip validation and callbacks to avoid password hashing
                $this->userModel->skipValidation(true);
                $this->db->table('users')->where('id', $userId)->update($userData);

                log_message('debug', 'User Update Query: ' . $this->db->getLastQuery());
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed.');
            }

            session()->setFlashdata('success', 'Employee updated successfully.');
            return redirect()->to(base_url('employee'));
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Employee Update Error: ' . $e->getMessage());

            session()->setFlashdata('error', 'Update failed: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }


    /**
     * Delete employee
     */
    public function delete($id = null)
    {
        $employee = $this->employeeModel->find($id);

        if (!$employee) {
            session()->setFlashdata('error', 'Employee not found.');
            return redirect()->to(base_url('employee'));
        }

        $this->db->transStart();

        try {
            // Delete user first
            if ($employee['user_id']) {
                $this->userModel->delete($employee['user_id']);
            }

            // Delete employee
            $this->employeeModel->delete($id);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed.');
            }

            session()->setFlashdata('success', 'Employee deleted successfully.');
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Employee Deletion Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Deletion failed: ' . $e->getMessage());
        }

        return redirect()->to(base_url('employee'));
    }
}

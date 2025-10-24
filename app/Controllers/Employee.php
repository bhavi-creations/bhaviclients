<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\Employee.php

namespace App\Controllers;

use App\Controllers\BaseController; // Reverted to CI standard BaseController
use App\Models\EmployeeModel;
use App\Models\DepartmentModel;
use App\Models\RoleModel;
use App\Models\UserModel;

class Employee extends BaseController // Reverted to CI standard BaseController
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
        // Initialize the models
        $this->employeeModel = new EmployeeModel();
        $this->departmentModel = new DepartmentModel();
        $this->roleModel = new RoleModel();
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        // Initialize Database Connection Service
        $this->db = \Config\Database::connect();    

        helper(['form', 'url']);
    }

    /**
     * R - Read (Displays the employee list with departments and roles)
     */
    public function index()
    {
        $employees = $this->employeeModel
            ->select('employees.*, departments.name AS department_name, roles.name AS role_name')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->join('roles', 'roles.id = employees.role_id', 'left') // <-- FIXED: Changed employees->role_id to employees.role_id
            ->findAll();

        $data['employees'] = $employees;
        $data['title'] = 'Employee List';
        return view('employee/index', $data);
    }

    // --------------------------------------------------------------------------------
    // --- C - Create ---
    // --------------------------------------------------------------------------------


    public function create()
    {
        $data['departments'] = $this->departmentModel->findAll(); // Using model for simplicity
        $data['roles'] = $this->roleModel->findAll(); // Fetch roles for the dropdown
        $data['title'] = 'Add New Employee';
        $data['validation'] = $this->validation;

        return view('employee/create', $data);
    }

    public function store()
    {
        // 1. Define Validation Rules
        $rules = [
            'first_name'    => 'required|min_length[3]|max_length[100]',
            'last_name'     => 'required|min_length[3]|max_length[100]',
            'email'         => 'required|valid_email|is_unique[users.email]|max_length[255]',
            'phone'         => 'required|min_length[10]|max_length[20]|is_unique[users.phone]',
            'department_id' => 'required|numeric',
            'role_id'       => 'required|numeric', // Ensure role_id is passed from form
        ];

        if (!$this->validate($rules)) {
            $this->session->setFlashdata('error', 'Validation failed. Please correct the errors shown below.');
            return redirect()->back()->withInput()->with('validation', $this->validation);
        }

        $input = $this->request->getPost();

        // --- START TRANSACTION ---
        $this->db->transStart();

        try {
            // 1. Prepare Employee Data (NOW including address)
            $employeeData = [
                'first_name'    => $input['first_name'],
                'last_name'     => $input['last_name'],
                'email'         => $input['email'],
                'phone'         => $input['phone'],
                'department_id' => $input['department_id'],
                'role_id'       => $input['role_id'], // Use submitted role_id
                'address'       => $input['address'] ?? null, // Include optional address
                'user_id'       => 0, // Placeholder, will be updated later
            ];

            // 2. Save Employee and get the new ID
            $this->employeeModel->insert($employeeData);
            $employeeId = $this->employeeModel->getInsertID();

            if (!$employeeId) {
                throw new \Exception('Failed to save employee data.');
            }

            // 3. Prepare User Data for the 'users' table (NOW including address)
            $userData = [
                'employee_id'   => $employeeId,
                'role_id'       => $input['role_id'], // Use submitted role_id
                'first_name'    => $input['first_name'],
                'last_name'     => $input['last_name'],
                'email'         => $input['email'],
                'phone'         => $input['phone'],
                'address'       => $input['address'] ?? null, // Include optional address
                // Set department_id for quick lookups
                'department_id' => $input['department_id'],
                // Default username and password based on input
                'username'      => $input['email'],
                'password'      => $input['phone'], // Hashed by UserModel's beforeInsert
            ];

            // 4. Save User and get the new User ID
            $this->userModel->insert($userData);
            $userId = $this->userModel->getInsertID();

            if (!$userId) {
                throw new \Exception('Failed to create user account.');
            }

            // 5. CRITICAL FIX: Update the employee record with the new user's ID
            $this->employeeModel->update($employeeId, ['user_id' => $userId]);

            // Commit the transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed to commit.');
            }

            $this->session->setFlashdata('success', 'Employee and User created successfully! Email is the username, and the phone number is the temporary password.');
            return redirect()->to(base_url('employee'));
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Employee Creation Error: ' . $e->getMessage());
            $this->session->setFlashdata('error', 'System Error! Employee creation failed. ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    // =============================================================
    // EDIT & UPDATE METHODS (Clean, Rebuilt, Stable)
    // =============================================================
    public function edit($id = null)
    {
        if ($id === null || !$employee = $this->employeeModel->find($id)) {
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


    public function update($id = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->to(base_url('employee/edit/' . $id));
        }

        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            session()->setFlashdata('error', 'Employee not found.');
            return redirect()->to(base_url('employee'));
        }

        $userId = $employee['user_id'];

        // --- CUSTOM VALIDATION (fixes uniqueness issue) ---
        $rules = [
            'first_name'    => 'required|max_length[100]',
            'last_name'     => 'required|max_length[100]',
            // Custom unique checks using callbacks instead of is_unique
            'email' => [
                'label' => 'Email',
                'rules' => [
                    'required',
                    'valid_email',
                    'max_length[255]',
                    function ($value) use ($userId) {
                        return $this->checkUniqueExcludingId($value, 'email', 'users', $userId);
                    }
                ],
                'errors' => [
                    'checkUniqueExcludingId' => 'The email is already in use by another employee.',
                ],
            ],
            'phone' => [
                'label' => 'Phone',
                'rules' => [
                    'required',
                    'numeric',
                    'min_length[10]',
                    'max_length[20]',
                    function ($value) use ($userId) {
                        return $this->checkUniqueExcludingId($value, 'phone', 'users', $userId);
                    }
                ],
                'errors' => [
                    'checkUniqueExcludingId' => 'The phone is already in use by another employee.',
                ],
            ],
            'department_id' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            log_message('debug', 'Validation errors: ' . json_encode($this->validator->getErrors()));
            session()->setFlashdata('error', 'Validation failed. Please check your inputs.');
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Capture input
        $input = $this->request->getPost();

        // Prepare employee data
        $employeeData = [
            'first_name'    => trim($input['first_name']),
            'last_name'     => trim($input['last_name']),
            'email'         => trim($input['email']),
            'phone'         => trim($input['phone']),
            'department_id' => $input['department_id'],
            'role_id'       => 2, // fixed role
        ];

        // Prepare user table data
        $userData = [
            'first_name'    => trim($input['first_name']),
            'last_name'     => trim($input['last_name']),
            'email'         => trim($input['email']),
            'username'      => trim($input['email']),
            'phone'         => trim($input['phone']),
            'department_id' => $input['department_id'],
            'role_id'       => 2,
        ];

        // --- TRANSACTION START ---
        $this->db->transStart();

        try {
            // Force employee update (skip validation prevents SELECT)
            $this->employeeModel->skipValidation(true)->update($id, $employeeData);
            log_message('debug', 'Employee update query: ' . $this->db->getLastQuery());

            // Update linked user record if exists
            if ($userId) {
                $this->userModel->skipValidation(true)->update($userId, $userData);
                log_message('debug', 'User update query: ' . $this->db->getLastQuery());
            }

            // Commit transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed to commit');
            }

            session()->setFlashdata('success', 'Employee and user details updated successfully.');
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Employee Update Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Update failed: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }

        // Redirect on success
        return redirect()->to(base_url('employee'));
    }


    // Helper function for custom uniqueness validation
    protected function checkUniqueExcludingId($value, $field, $table, $ignoreId)
    {
        $builder = $this->db->table($table);
        $builder->where($field, $value);
        $builder->where('id !=', $ignoreId);
        $result = $builder->countAllResults();

        if ($result > 0) {
            return false;
        }
        return true;
    }





    public function delete($id = null)
    {
        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            session()->setFlashdata('error', 'Employee not found for deletion.');
            return redirect()->to(base_url('employee'));
        }

        $this->db->transStart();

        try {
            if ($employee['user_id'] > 0) {
                $this->userModel->where('id', $employee['user_id'])->delete();
            }

            $this->employeeModel->delete($id);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                session()->setFlashdata('error', 'Database operation failed during employee and user deletion.');
            } else {
                session()->setFlashdata('success', 'Employee and associated user deleted successfully!');
            }
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Employee Deletion Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'An error occurred during deletion: ' . $e->getMessage());
        }

        return redirect()->to(base_url('employee'));
    }
}

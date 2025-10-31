<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\Employee.php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeeModel;
use App\Models\DepartmentModel;
use App\Models\RoleModel;
use App\Models\UserModel;
use App\Models\EmployeeSalaryHistoryModel;

class Employee extends BaseController
{
    protected $employeeModel;
    protected $departmentModel;
    protected $roleModel;
    protected $userModel;
    protected $salaryHistoryModel;
    protected $validation;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->employeeModel       = new EmployeeModel();
        $this->departmentModel     = new DepartmentModel();
        $this->roleModel           = new RoleModel();
        $this->userModel           = new UserModel();
        $this->salaryHistoryModel  = new EmployeeSalaryHistoryModel();
        $this->session             = \Config\Services::session();
        $this->validation          = \Config\Services::validation();
        $this->db                  = \Config\Database::connect();
        helper(['form', 'url']);

        // Access control
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
        $employees = $this->employeeModel->getAllEmployeesWithSalary();

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

    /**
     * Store new employee
     */
    public function store()
    {
        $rules = [
            'employee_code'   => 'permit_empty|max_length[50]|is_unique[employees.employee_code]',
            'first_name'      => 'required|min_length[2]|max_length[100]',
            'last_name'       => 'required|min_length[2]|max_length[100]',
            'email'           => 'required|valid_email|is_unique[users.email]',
            'phone'           => 'required|min_length[10]|max_length[20]|is_unique[users.phone]',
            'parent_name'     => 'permit_empty|max_length[100]',
            'parent_phone'    => 'permit_empty|max_length[20]',
            'date_of_joining' => 'permit_empty',
            'status'          => 'required|in_list[active,inactive]',
            'department_id'   => 'required|integer',
            'role_id'         => 'required|integer',
            'initial_salary'  => 'permit_empty|decimal',
            'remarks'         => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            // Get validation errors
            $errors = $this->validation->getErrors();

            // Log for debugging
            log_message('error', 'Employee Validation Failed: ' . json_encode($errors));

            // Also display errors in the flash message
            $errorList = '<ul>';
            foreach ($errors as $field => $error) {
                $errorList .= '<li><strong>' . $field . ':</strong> ' . $error . '</li>';
            }
            $errorList .= '</ul>';

            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validation)
                ->with('error', 'Validation failed: ' . $errorList);
        }

        $input = $this->request->getPost();
        $this->db->transStart();

        try {
            // 1. Create User
            $userData = [
                'role_id'       => $input['role_id'],
                'first_name'    => trim($input['first_name']),
                'last_name'     => trim($input['last_name']),
                'email'         => trim($input['email']),
                'phone'         => trim($input['phone']),
                'department_id' => $input['department_id'],
                'username'      => trim($input['email']),
                'password'      => $input['phone'],
                'created_at'    => date('Y-m-d H:i:s'),
            ];

            $this->userModel->insert($userData);
            $userId = $this->userModel->getInsertID();

            if (!$userId) {
                throw new \Exception('Failed to create user account.');
            }

            // 2. Create Employee
            $employeeData = [
                'employee_code'   => !empty($input['employee_code']) ? trim($input['employee_code']) : null,
                'user_id'         => $userId,
                'first_name'      => trim($input['first_name']),
                'last_name'       => trim($input['last_name']),
                'email'           => trim($input['email']),
                'phone'           => trim($input['phone']),
                'parent_name'     => !empty($input['parent_name']) ? trim($input['parent_name']) : null,
                'parent_phone'    => !empty($input['parent_phone']) ? trim($input['parent_phone']) : null,
                'date_of_joining' => !empty($input['date_of_joining']) ? $input['date_of_joining'] : null,
                'status'          => $input['status'] ?? 'active',
                'department_id'   => $input['department_id'],
                'role_id'         => $input['role_id'],
                'remarks'         => !empty($input['remarks']) ? trim($input['remarks']) : null,
                'file_uploads'    => json_encode([]),
                'created_at'      => date('Y-m-d H:i:s'),
            ];

            $this->employeeModel->insert($employeeData);
            $employeeId = $this->employeeModel->getInsertID();

            if (!$employeeId) {
                throw new \Exception('Failed to create employee record.');
            }

            // 3. Update user with employee_id
            $this->userModel->update($userId, ['employee_id' => $employeeId]);

            // 4. Add initial salary if provided
            if (!empty($input['initial_salary']) && $input['initial_salary'] > 0) {
                $salaryData = [
                    'employee_id'    => $employeeId,
                    'salary_amount'  => $input['initial_salary'],
                    'effective_date' => $input['date_of_joining'] ?? date('Y-m-d'),
                    'increment_type' => 'initial',
                    'reason'         => 'Initial joining salary',
                    'approved_by'    => session()->get('id')
                ];
                $this->salaryHistoryModel->insert($salaryData);
            }

            // 5. Handle file uploads (if any)
            $files = $this->request->getFiles('employee_files');
            if (!empty($files)) {
                $uploadPath = FCPATH . 'uploads/employees/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $uploadedFiles = [];
                foreach ($files as $file) {
                    if (is_array($file)) {
                        foreach ($file as $singleFile) {
                            if ($singleFile->isValid() && !$singleFile->hasMoved()) {
                                $newName = $singleFile->getRandomName();
                                $singleFile->move($uploadPath, $newName);
                                $uploadedFiles[] = $newName;
                            }
                        }
                    } else {
                        if ($file->isValid() && !$file->hasMoved()) {
                            $newName = $file->getRandomName();
                            $file->move($uploadPath, $newName);
                            $uploadedFiles[] = $newName;
                        }
                    }
                }

                if (!empty($uploadedFiles)) {
                    $this->employeeModel->update($employeeId, [
                        'file_uploads' => json_encode($uploadedFiles)
                    ]);
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed.');
            }

            session()->setFlashdata('success', 'Employee created successfully!');
            return redirect()->to(base_url('employee'));
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Employee Creation Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to create employee: ' . $e->getMessage());
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

        $departments = $this->departmentModel->findAll();
        $roles = $this->roleModel->findAll();

        // Get latest salary from employee_salary_history
        $latestSalary = $this->db->table('employee_salary_history')
            ->where('employee_id', $id)
            ->orderBy('effective_date', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $data = [
            'title' => 'Edit Employee',
            'employee' => $employee,
            'departments' => $departments,
            'roles' => $roles,
            'latestSalary' => $latestSalary, // Pass latest salary to view
            'validation' => $this->validation
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

        $rules = [
            'employee_code'   => "permit_empty|max_length[50]|is_unique[employees.employee_code,id,{$id}]",
            'first_name'      => 'required|min_length[2]|max_length[100]',
            'last_name'       => 'required|min_length[2]|max_length[100]',
            'email'           => "required|valid_email|is_unique[users.email,id,{$userId}]",
            'phone'           => "required|numeric|min_length[10]|is_unique[users.phone,id,{$userId}]",
            'parent_name'     => 'permit_empty|max_length[100]',
            'parent_phone'    => 'permit_empty|max_length[20]',
            'date_of_joining' => 'permit_empty',
            'status'          => 'required|in_list[active,inactive]',
            'department_id'   => 'required|integer',
            'role_id'         => 'required|integer',
            'remarks'         => 'permit_empty',
            'salary_amount'   => 'permit_empty|decimal',
            'increment_type'  => 'permit_empty|in_list[increment,promotion,annual_review,adjustment]',
            'effective_date'  => 'permit_empty',
            'salary_reason'   => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validation)
                ->with('error', 'Validation failed.');
        }

        $input = $this->request->getPost();
        $this->db->transStart();

        try {
            // Handle file uploads
            $existingFiles = !empty($employee['file_uploads']) ? json_decode($employee['file_uploads'], true) : [];
            $keepFiles = $this->request->getPost('keep_files') ?? [];

            // Filter existing files to keep only selected ones
            $keptFiles = [];
            foreach ($existingFiles as $index => $file) {
                if (in_array($index, $keepFiles)) {
                    $keptFiles[] = $file;
                } else {
                    // Delete file from server
                    $filePath = FCPATH . 'uploads/employee_files/' . $file;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            // Handle new file uploads
            $files = $this->request->getFiles('employee_files');

            if (!empty($files)) {
                $uploadPath = FCPATH . 'uploads/employee_files/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                foreach ($files as $file) {
                    if (is_array($file)) {
                        foreach ($file as $singleFile) {
                            if ($singleFile->isValid() && !$singleFile->hasMoved()) {
                                $newName = time() . '_' . $singleFile->getRandomName();
                                $singleFile->move($uploadPath, $newName);
                                $keptFiles[] = $newName;
                            }
                        }
                    } else {
                        if ($file->isValid() && !$file->hasMoved()) {
                            $newName = time() . '_' . $file->getRandomName();
                            $file->move($uploadPath, $newName);
                            $keptFiles[] = $newName;
                        }
                    }
                }
            }

            // Update employee
            $employeeData = [
                'employee_code'   => !empty($input['employee_code']) ? trim($input['employee_code']) : null,
                'first_name'      => trim($input['first_name']),
                'last_name'       => trim($input['last_name']),
                'email'           => trim($input['email']),
                'phone'           => trim($input['phone']),
                'parent_name'     => !empty($input['parent_name']) ? trim($input['parent_name']) : null,
                'parent_phone'    => !empty($input['parent_phone']) ? trim($input['parent_phone']) : null,
                'date_of_joining' => !empty($input['date_of_joining']) ? $input['date_of_joining'] : null,
                'status'          => $input['status'],
                'department_id'   => $input['department_id'],
                'role_id'         => $input['role_id'],
                'remarks'         => !empty($input['remarks']) ? trim($input['remarks']) : null,
                'file_uploads'    => !empty($keptFiles) ? json_encode($keptFiles) : null,
                'updated_at'      => date('Y-m-d H:i:s'),
            ];

            $this->employeeModel->skipValidation(true)->update($id, $employeeData);

            // Handle salary update
            if (!empty($input['salary_amount'])) {
                // Validate required fields for salary update
                if (empty($input['increment_type'])) {
                    throw new \Exception('Increment type is required when updating salary.');
                }

                // Get previous salary
                $previousSalary = $this->db->table('employee_salary_history')
                    ->where('employee_id', $id)
                    ->orderBy('effective_date', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray();

                $previousAmount = $previousSalary['salary_amount'] ?? 0;
                $newAmount = $input['salary_amount'];

                // Calculate increment percentage
                $incrementPercentage = 0;
                if ($previousAmount > 0) {
                    $incrementPercentage = (($newAmount - $previousAmount) / $previousAmount) * 100;
                }

                // Insert new salary record
                $salaryData = [
                    'employee_id'          => $id,
                    'salary_amount'        => $newAmount,
                    'effective_date'       => !empty($input['effective_date']) ? $input['effective_date'] : date('Y-m-d'),
                    'increment_type'       => $input['increment_type'],
                    'increment_percentage' => round($incrementPercentage, 2),
                    'previous_salary'      => $previousAmount > 0 ? $previousAmount : null,
                    'reason'               => !empty($input['salary_reason']) ? trim($input['salary_reason']) : null,
                    'approved_by'          => session()->get('id'), // Current admin user
                    'remarks'              => null,
                    'created_at'           => date('Y-m-d H:i:s'),
                    'updated_at'           => date('Y-m-d H:i:s')
                ];

                $this->db->table('employee_salary_history')->insert($salaryData);
            }

            // Update user
            if ($userId) {
                $userData = [
                    'role_id'       => $input['role_id'],
                    'first_name'    => trim($input['first_name']),
                    'last_name'     => trim($input['last_name']),
                    'email'         => trim($input['email']),
                    'username'      => trim($input['email']),
                    'phone'         => trim($input['phone']),
                    'department_id' => $input['department_id'],
                    'updated_at'    => date('Y-m-d H:i:s'),
                ];

                $this->userModel->skipValidation(true);
                $this->db->table('users')->where('id', $userId)->update($userData);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed.');
            }

            $successMessage = 'Employee updated successfully.';
            if (!empty($input['salary_amount'])) {
                $successMessage .= ' Salary updated to ₹' . number_format($input['salary_amount'], 2);
            }

            session()->setFlashdata('success', $successMessage);
            return redirect()->to(base_url('employee'));
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Employee Update Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Update failed: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }



    /**
     * View employee details with salary history
     */
    public function view($id = null)
    {
        $employee = $this->employeeModel->getEmployeeWithSalary($id);

        if (!$employee) {
            session()->setFlashdata('error', 'Employee not found.');
            return redirect()->to(base_url('employee'));
        }

        // Get salary history
        $salaryHistory = $this->salaryHistoryModel->getEmployeeSalaryHistory($id);

        // Get salary statistics
        $salaryStats = $this->salaryHistoryModel->getSalaryStats($id);

        // Get uploaded files
        $files = $this->employeeModel->getEmployeeFiles($id);

        $data = [
            'title' => 'Employee Details',
            'employee' => $employee,
            'salaryHistory' => $salaryHistory,
            'salaryStats' => $salaryStats,
            'files' => $files
        ];

        return view('employee/view', $data);
    }

    /**
     * Upload employee files
     */
    public function uploadFiles($id)
    {
        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        $files = $this->request->getFiles('employee_files');

        if (empty($files)) {
            return redirect()->back()->with('error', 'No files selected.');
        }

        $uploadPath = FCPATH . 'uploads/employees/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $uploadedCount = 0;

        foreach ($files as $file) {
            if (is_array($file)) {
                foreach ($file as $singleFile) {
                    if ($this->processFileUpload($singleFile, $uploadPath, $id)) {
                        $uploadedCount++;
                    }
                }
            } else {
                if ($this->processFileUpload($file, $uploadPath, $id)) {
                    $uploadedCount++;
                }
            }
        }

        session()->setFlashdata('success', $uploadedCount . ' file(s) uploaded successfully.');
        return redirect()->back();
    }

    /**
     * Process single file upload
     */
    private function processFileUpload($file, $uploadPath, $employeeId)
    {
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);

            // Add to employee's file_uploads JSON
            $this->employeeModel->addFile($employeeId, $newName);

            return true;
        }
        return false;
    }

    /**
     * Download employee file
     */
    public function downloadFile($employeeId, $fileName)
    {
        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        $files = $this->employeeModel->getEmployeeFiles($employeeId);
        if (!in_array($fileName, $files)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filePath = FCPATH . 'uploads/employees/' . $fileName;
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        return $this->response->download($filePath, null)->setFileName($fileName);
    }

    /**
     * Delete employee file
     */
    public function deleteFile($employeeId, $fileName)
    {
        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        $files = $this->employeeModel->getEmployeeFiles($employeeId);
        if (!in_array($fileName, $files)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filePath = FCPATH . 'uploads/employees/' . $fileName;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $this->employeeModel->removeFile($employeeId, $fileName);

        session()->setFlashdata('success', 'File deleted successfully.');
        return redirect()->back();
    }

    /**
     * Add salary increment
     */
    public function addSalary($employeeId)
    {
        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        $rules = [
            'salary_amount'  => 'required|decimal|greater_than[0]',
            'effective_date' => 'required',
            'increment_type' => 'required|in_list[increment,promotion,annual_review,adjustment]',
            'reason'         => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed.');
        }

        $input = $this->request->getPost();

        $salaryData = [
            'employee_id'    => $employeeId,
            'salary_amount'  => $input['salary_amount'],
            'effective_date' => $input['effective_date'],
            'increment_type' => $input['increment_type'],
            'reason'         => $input['reason'] ?? null,
            'approved_by'    => session()->get('id')
        ];

        if ($this->salaryHistoryModel->addSalaryRecord($salaryData)) {
            session()->setFlashdata('success', 'Salary record added successfully!');
        } else {
            session()->setFlashdata('error', 'Failed to add salary record.');
        }

        return redirect()->back();
    }

    /**
     * Delete salary record
     */
    public function deleteSalary($salaryId)
    {
        $salary = $this->salaryHistoryModel->find($salaryId);
        if (!$salary) {
            return redirect()->back()->with('error', 'Salary record not found.');
        }

        // Don't allow deleting initial salary
        if ($salary['increment_type'] == 'initial') {
            return redirect()->back()->with('error', 'Cannot delete initial salary record.');
        }

        if ($this->salaryHistoryModel->delete($salaryId)) {
            session()->setFlashdata('success', 'Salary record deleted successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to delete salary record.');
        }

        return redirect()->back();
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
            // Delete files
            $files = $this->employeeModel->getEmployeeFiles($id);
            foreach ($files as $file) {
                $filePath = FCPATH . 'uploads/employees/' . $file;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Delete salary history (cascade will handle this, but explicit is safer)
            $this->salaryHistoryModel->where('employee_id', $id)->delete();

            // Delete user
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

    /**
     * Edit salary record
     */
    public function editSalary($salaryId)
    {
        $salary = $this->salaryHistoryModel->find($salaryId);
        if (!$salary) {
            return redirect()->back()->with('error', 'Salary record not found.');
        }

        // Don't allow editing initial salary
        if ($salary['increment_type'] == 'initial') {
            return redirect()->back()->with('error', 'Cannot edit initial salary record.');
        }

        $rules = [
            'salary_amount'  => 'required|decimal|greater_than[0]',
            'effective_date' => 'required',
            'increment_type' => 'required|in_list[increment,promotion,annual_review,adjustment]',
            'reason'         => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed.');
        }

        $input = $this->request->getPost();

        // Get previous salary for recalculation
        $previousSalary = $this->salaryHistoryModel
            ->where('employee_id', $salary['employee_id'])
            ->where('effective_date <', $input['effective_date'])
            ->orderBy('effective_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();

        $salaryData = [
            'salary_amount'  => $input['salary_amount'],
            'effective_date' => $input['effective_date'],
            'increment_type' => $input['increment_type'],
            'reason'         => $input['reason'] ?? null
        ];

        // Recalculate increment percentage
        if ($previousSalary) {
            $salaryData['previous_salary'] = $previousSalary['salary_amount'];
            $increase = $input['salary_amount'] - $previousSalary['salary_amount'];
            $salaryData['increment_percentage'] = round(($increase / $previousSalary['salary_amount']) * 100, 2);
        }

        if ($this->salaryHistoryModel->update($salaryId, $salaryData)) {
            session()->setFlashdata('success', 'Salary record updated successfully!');
        } else {
            session()->setFlashdata('error', 'Failed to update salary record.');
        }

        return redirect()->back();
    }
}

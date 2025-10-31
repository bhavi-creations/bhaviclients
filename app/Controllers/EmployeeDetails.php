<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\EmployeeDetails.php

namespace App\Controllers;

use App\Controllers\BaseController;

class EmployeeDetails extends BaseController
{
    protected $db;
    protected $session;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();
        helper(['url', 'form']);

        // Access control - Only Employees (role_id = 2)
        if (session()->get('role_id') != 2) {
            header('Location: ' . base_url('dashboard'));
            exit;
        }
    }

    /**
     * Get employee ID from logged-in user
     */
    private function getEmployeeId()
    {
        // CORRECT: Use 'user_id' as stored in your login session
        $userId = session()->get('user_id');

        if (empty($userId)) {
            log_message('error', 'No user ID in session');
            return null;
        }

        $user = $this->db->table('users')
            ->select('employee_id')
            ->where('id', $userId)
            ->get()
            ->getRowArray();

        if (empty($user)) {
            log_message('error', 'User not found for ID: ' . $userId);
            return null;
        }

        return $user['employee_id'] ?? null;
    }


    /**
     * Display employee details with uploaded files
     */
    public function index()
    {
        $employeeId = $this->getEmployeeId();

        if (!$employeeId) {
            session()->setFlashdata('error', 'Employee profile not found.');
            return redirect()->to(base_url('employee-dashboard'));
        }

        // Get employee details with department
        $employee = $this->db->table('employees')
            ->select('employees.*, departments.name as department_name')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->where('employees.id', $employeeId)
            ->get()
            ->getRowArray();

        if (!$employee) {
            session()->setFlashdata('error', 'Employee profile not found.');
            return redirect()->to(base_url('employee-dashboard'));
        }

        // Get latest salary from employee_salary_history
        $latestSalary = $this->db->table('employee_salary_history')
            ->where('employee_id', $employeeId)
            ->orderBy('effective_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        // Get all salary history
        $salaryHistory = $this->db->table('employee_salary_history')
            ->where('employee_id', $employeeId)
            ->orderBy('effective_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        // Get uploaded files for this employee from employees table (file_uploads column)
        $uploadedFiles = [];
        if (!empty($employee['file_uploads'])) {
            $decodedFiles = json_decode($employee['file_uploads'], true);
            if (is_array($decodedFiles)) {
                $uploadedFiles = $decodedFiles;
            }
        }

        $data = [
            'title' => 'My Details',
            'employee' => $employee,
            'latestSalary' => $latestSalary,
            'salaryHistory' => $salaryHistory,
            'uploadedFiles' => $uploadedFiles
        ];

        return view('employee/my_details', $data);
    }

    /**
     * Download employee file
     */
    public function downloadFile($fileIndex)
    {
        $employeeId = $this->getEmployeeId();

        if (!$employeeId) {
            session()->setFlashdata('error', 'Employee profile not found.');
            return redirect()->to(base_url('employee-dashboard'));
        }

        // Get employee
        $employee = $this->db->table('employees')
            ->where('id', $employeeId)
            ->get()
            ->getRowArray();

        if (!$employee) {
            session()->setFlashdata('error', 'Employee not found.');
            return redirect()->to(base_url('my-details'));
        }

        // Get uploaded files (file_uploads column)
        $uploadedFiles = !empty($employee['file_uploads']) ? json_decode($employee['file_uploads'], true) : [];

        if (!isset($uploadedFiles[$fileIndex])) {
            session()->setFlashdata('error', 'File not found.');
            return redirect()->to(base_url('my-details'));
        }

        $fileName = $uploadedFiles[$fileIndex];
        $filePath = FCPATH . 'uploads/employee_files/' . $fileName;

        if (!file_exists($filePath)) {
            session()->setFlashdata('error', 'File not found on server.');
            return redirect()->to(base_url('my-details'));
        }

        // Force download
        return $this->response->download($filePath, null)->setFileName($fileName);
    }
}

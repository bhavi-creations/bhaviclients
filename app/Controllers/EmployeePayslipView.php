<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\EmployeePayslipView.php

namespace App\Controllers;

use App\Controllers\BaseController;

class EmployeePayslipView extends BaseController
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
        $userId = session()->get('user_id'); // Using user_id as per your existing code
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        return $user['employee_id'] ?? null;
    }

    /**
     * Display all payslips for logged-in employee
     */
    public function index()
    {
        $employeeId = $this->getEmployeeId();

        if (!$employeeId) {
            session()->setFlashdata('error', 'Employee profile not found.');
            return redirect()->to(base_url('employee-dashboard'));
        }

        // Get employee details
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

        // Get all payslips for this employee
        $payslips = $this->db->table('employee_payslips')
            ->select('employee_payslips.*, 
                     users.first_name as uploaded_by_name, 
                     users.last_name as uploaded_by_lastname')
            ->join('users', 'users.id = employee_payslips.uploaded_by', 'left')
            ->where('employee_payslips.employee_id', $employeeId)
            ->orderBy('employee_payslips.month', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'My Payslips',
            'employee' => $employee,
            'payslips' => $payslips
        ];

        return view('employee/my_payslips', $data);
    }

    /**
     * Download payslip file
     */
    public function download($id)
    {
        $employeeId = $this->getEmployeeId();

        if (!$employeeId) {
            session()->setFlashdata('error', 'Employee profile not found.');
            return redirect()->to(base_url('employee-dashboard'));
        }

        // Get payslip
        $payslip = $this->db->table('employee_payslips')
            ->where('id', $id)
            ->where('employee_id', $employeeId) // Security: Only own payslips
            ->get()
            ->getRowArray();

        if (!$payslip) {
            session()->setFlashdata('error', 'Payslip not found or access denied.');
            return redirect()->to(base_url('my-payslips'));
        }

        $filePath = FCPATH . 'uploads/payslips/' . $payslip['payslip_file'];

        if (!file_exists($filePath)) {
            session()->setFlashdata('error', 'Payslip file not found on server.');
            return redirect()->to(base_url('my-payslips'));
        }

        // Force download
        return $this->response->download($filePath, null)->setFileName($payslip['payslip_file']);
    }
}

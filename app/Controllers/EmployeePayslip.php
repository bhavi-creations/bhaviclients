<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\EmployeePayslip.php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeePayslipModel;
use App\Models\EmployeeModel;

class EmployeePayslip extends BaseController
{
    protected $payslipModel;
    protected $employeeModel;
    protected $validation;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->payslipModel = new EmployeePayslipModel();
        $this->employeeModel = new EmployeeModel();
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);

        // Access control - Only Admin and Admin Manager
        if (!in_array(session()->get('role_id'), [1, 5])) {
            header('Location: ' . base_url('dashboard'));
            exit;
        }
    }

    /**
     * Display list of employees who have payslips
     */
    public function index()
    {
        $employees = $this->payslipModel->getEmployeesWithPayslips();

        $data = [
            'title' => 'Employee Payslips',
            'employees' => $employees
        ];

        return view('employee_payslip/index', $data);
    }

    /**
     * Show all payslips for a specific employee
     */
    public function employeePayslips($employeeId)
    {
        $employee = $this->employeeModel->find($employeeId);
        
        if (!$employee) {
            session()->setFlashdata('error', 'Employee not found.');
            return redirect()->to(base_url('employee-payslip'));
        }

        $payslips = $this->payslipModel->getEmployeePayslips($employeeId);

        $data = [
            'title' => 'Payslips for ' . $employee['first_name'] . ' ' . $employee['last_name'],
            'employee' => $employee,
            'payslips' => $payslips
        ];

        return view('employee_payslip/employee_payslips', $data);
    }

    /**
     * Show upload form
     */
    public function create()
    {
        $employees = $this->employeeModel->orderBy('first_name', 'ASC')->findAll();

        $data = [
            'title' => 'Upload Payslip',
            'employees' => $employees,
            'validation' => $this->validation
        ];

        return view('employee_payslip/create', $data);
    }

    /**
     * Store payslip
     */
    public function store()
    {
        $rules = [
            'employee_id' => 'required|integer',
            'month' => 'required',
            'payslip_file' => 'uploaded[payslip_file]|max_size[payslip_file,5120]|ext_in[payslip_file,pdf,jpg,jpeg,png]',
            'remarks' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validation)
                ->with('error', 'Validation failed. Please check the form.');
        }

        $input = $this->request->getPost();

        // Check if payslip already exists for this employee and month
        if ($this->payslipModel->payslipExists($input['employee_id'], $input['month'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Payslip already exists for this employee and month.');
        }

        $this->db->transStart();

        try {
            // Handle file upload
            $file = $this->request->getFile('payslip_file');
            
            $uploadPath = FCPATH . 'uploads/payslips/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            if ($file->isValid() && !$file->hasMoved()) {
                $newName = 'payslip_' . $input['employee_id'] . '_' . str_replace('-', '', $input['month']) . '_' . time() . '.' . $file->getExtension();
                $file->move($uploadPath, $newName);

                // Insert payslip record
                $payslipData = [
                    'employee_id' => $input['employee_id'],
                    'month' => $input['month'],
                    'payslip_file' => $newName,
                    'remarks' => !empty($input['remarks']) ? trim($input['remarks']) : null,
                    'uploaded_by' => session()->get('id'),
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $this->payslipModel->insert($payslipData);

                $this->db->transComplete();

                if ($this->db->transStatus() === false) {
                    throw new \Exception('Transaction failed.');
                }

                session()->setFlashdata('success', 'Payslip uploaded successfully!');
                return redirect()->to(base_url('employee-payslip'));

            } else {
                throw new \Exception('File upload failed.');
            }

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Payslip Upload Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to upload payslip: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $payslip = $this->payslipModel->find($id);
        
        if (!$payslip) {
            session()->setFlashdata('error', 'Payslip not found.');
            return redirect()->to(base_url('employee-payslip'));
        }

        $employees = $this->employeeModel->orderBy('first_name', 'ASC')->findAll();

        $data = [
            'title' => 'Edit Payslip',
            'payslip' => $payslip,
            'employees' => $employees,
            'validation' => $this->validation
        ];

        return view('employee_payslip/edit', $data);
    }

    /**
     * Update payslip
     */
    public function update($id)
    {
        $payslip = $this->payslipModel->find($id);
        
        if (!$payslip) {
            session()->setFlashdata('error', 'Payslip not found.');
            return redirect()->to(base_url('employee-payslip'));
        }

        $rules = [
            'employee_id' => 'required|integer',
            'month' => 'required',
            'payslip_file' => 'permit_empty|max_size[payslip_file,5120]|ext_in[payslip_file,pdf,jpg,jpeg,png]',
            'remarks' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validation)
                ->with('error', 'Validation failed.');
        }

        $input = $this->request->getPost();

        // Check if payslip exists for another record
        if ($this->payslipModel->payslipExists($input['employee_id'], $input['month'], $id)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Payslip already exists for this employee and month.');
        }

        $this->db->transStart();

        try {
            $updateData = [
                'employee_id' => $input['employee_id'],
                'month' => $input['month'],
                'remarks' => !empty($input['remarks']) ? trim($input['remarks']) : null
            ];

            // Handle new file upload
            $file = $this->request->getFile('payslip_file');
            
            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Delete old file
                $oldFilePath = FCPATH . 'uploads/payslips/' . $payslip['payslip_file'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }

                // Upload new file
                $uploadPath = FCPATH . 'uploads/payslips/';
                $newName = 'payslip_' . $input['employee_id'] . '_' . str_replace('-', '', $input['month']) . '_' . time() . '.' . $file->getExtension();
                $file->move($uploadPath, $newName);

                $updateData['payslip_file'] = $newName;
            }

            $this->payslipModel->update($id, $updateData);

            $this->db->transComplete();

            session()->setFlashdata('success', 'Payslip updated successfully!');
            return redirect()->to(base_url('employee-payslip/employee/' . $input['employee_id']));

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Payslip Update Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to update payslip.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Delete payslip
     */
    public function delete($id)
    {
        $payslip = $this->payslipModel->find($id);

        if (!$payslip) {
            session()->setFlashdata('error', 'Payslip not found.');
            return redirect()->to(base_url('employee-payslip'));
        }

        try {
            // Delete file
            $filePath = FCPATH . 'uploads/payslips/' . $payslip['payslip_file'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete record
            $this->payslipModel->delete($id);

            session()->setFlashdata('success', 'Payslip deleted successfully.');
            return redirect()->back();

        } catch (\Exception $e) {
            log_message('error', 'Payslip Deletion Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to delete payslip.');
            return redirect()->back();
        }
    }

    /**
     * Download payslip
     */
    public function download($id)
    {
        $payslip = $this->payslipModel->find($id);

        if (!$payslip) {
            session()->setFlashdata('error', 'Payslip not found.');
            return redirect()->back();
        }

        $filePath = FCPATH . 'uploads/payslips/' . $payslip['payslip_file'];

        if (!file_exists($filePath)) {
            session()->setFlashdata('error', 'File not found on server.');
            return redirect()->back();
        }

        return $this->response->download($filePath, null)->setFileName($payslip['payslip_file']);
    }
}

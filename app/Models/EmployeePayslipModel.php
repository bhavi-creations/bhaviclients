<?php
// C:\xampp\htdocs\bhaviclients\app\Models\EmployeePayslipModel.php

namespace App\Models;

use CodeIgniter\Model;

class EmployeePayslipModel extends Model
{
    protected $table = 'employee_payslips';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'employee_id',
        'month',
        'payslip_file',
        'remarks',
        'uploaded_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'employee_id' => 'required|integer',
        'month' => 'required',
        'payslip_file' => 'required'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Get all employees who have payslips
     */
    public function getEmployeesWithPayslips()
    {
        return $this->db->query("
            SELECT 
                e.id,
                e.first_name,
                e.last_name,
                e.email,
                d.name as department_name,
                COUNT(ep.id) as payslip_count,
                MAX(ep.created_at) as latest_upload
            FROM employees e
            INNER JOIN employee_payslips ep ON e.id = ep.employee_id
            LEFT JOIN departments d ON e.department_id = d.id
            GROUP BY e.id
            ORDER BY latest_upload DESC
        ")->getResultArray();
    }

    /**
     * Get all payslips for a specific employee
     */
    public function getEmployeePayslips($employeeId)
    {
        return $this->select('employee_payslips.*, users.first_name as uploaded_by_name, users.last_name as uploaded_by_lastname')
                    ->join('users', 'users.id = employee_payslips.uploaded_by', 'left')
                    ->where('employee_payslips.employee_id', $employeeId)
                    ->orderBy('employee_payslips.month', 'DESC')
                    ->findAll();
    }

    /**
     * Check if payslip exists for employee and month
     */
    public function payslipExists($employeeId, $month, $excludeId = null)
    {
        $builder = $this->where('employee_id', $employeeId)
                        ->where('month', $month);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Get payslip details with employee info
     */
    public function getPayslipDetails($id)
    {
        return $this->select('employee_payslips.*, 
                             employees.first_name, 
                             employees.last_name,
                             employees.email,
                             employees.phone,
                             departments.name as department_name,
                             users.first_name as uploaded_by_name,
                             users.last_name as uploaded_by_lastname')
                    ->join('employees', 'employees.id = employee_payslips.employee_id', 'left')
                    ->join('departments', 'departments.id = employees.department_id', 'left')
                    ->join('users', 'users.id = employee_payslips.uploaded_by', 'left')
                    ->find($id);
    }
}

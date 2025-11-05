<?php
// C:\xampp\htdocs\bhaviclients\app\Models\LeaveRequestModel.php

namespace App\Models;

use CodeIgniter\Model;

class LeaveRequestModel extends Model
{
    protected $table = 'leave_requests';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'status',
        'admin_remarks',
        'approved_by',
        'approved_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'employee_id' => 'required|integer',
        'leave_type' => 'required|in_list[sick,casual,earned,unpaid,emergency]',
        'start_date' => 'required|valid_date',
        'end_date' => 'required|valid_date',
        'total_days' => 'required|integer|greater_than[0]',
        'reason' => 'required|min_length[10]|max_length[500]'
    ];

    /**
     * Get all leave requests with employee details
     */
    public function getAllLeavesWithEmployees()
    {
        return $this->select('leave_requests.*, 
                            users.first_name, 
                            users.last_name,
                            users.email,
                            employees.employee_code')
            ->join('users', 'users.id = leave_requests.employee_id', 'left')
            ->join('employees', 'employees.user_id = leave_requests.employee_id', 'left')
            ->orderBy('leave_requests.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get leaves for specific employee
     */
    public function getEmployeeLeaves($employeeId)
    {
        return $this->where('employee_id', $employeeId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get pending leaves count
     */
    public function getPendingCount()
    {
        return $this->where('status', 'pending')->countAllResults();
    }

     
    /**
     * Get employees with leave statistics
     */
    public function getEmployeesWithLeaveStats()
    {
        return $this->select('users.id as employee_id,
                        users.first_name,
                        users.last_name,
                        employees.employee_code,
                        COUNT(leave_requests.id) as total_leaves,
                        SUM(CASE WHEN leave_requests.status = "pending" THEN 1 ELSE 0 END) as pending_leaves,
                        SUM(CASE WHEN leave_requests.status = "approved" THEN 1 ELSE 0 END) as approved_leaves,
                        SUM(CASE WHEN leave_requests.status = "rejected" THEN 1 ELSE 0 END) as rejected_leaves,
                        SUM(CASE WHEN leave_requests.status = "approved" THEN leave_requests.total_days ELSE 0 END) as total_approved_days')
            ->join('users', 'users.id = leave_requests.employee_id', 'left')
            ->join('employees', 'employees.user_id = leave_requests.employee_id', 'left')
            ->groupBy('users.id, users.first_name, users.last_name, employees.employee_code')
            ->orderBy('pending_leaves', 'DESC')
            ->findAll();
    }


    /**
     * Get leave types
     */
    public static function getLeaveTypes()
    {
        return [
            'sick' => 'Sick Leave',
            'casual' => 'Casual Leave',
            'earned' => 'Earned Leave',
            'unpaid' => 'Unpaid Leave',
            'emergency' => 'Emergency Leave'
        ];
    }
}

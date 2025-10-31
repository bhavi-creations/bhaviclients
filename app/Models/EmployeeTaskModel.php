<?php
// C:\xampp\htdocs\bhaviclients\app\Models\EmployeeTaskModel.php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeTaskModel extends Model
{
    protected $table            = 'employee_tasks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'employee_id',
        'assigned_by',
        'client_id',
        'title',
        'description',
        'admin_remarks',
        'admin_files',          // NEW: Admin reference files
        'due_date',
        'status',
        'priority',
        'submitted_at',
        'employee_files',       // NEW: Renamed from files_upload
        'employee_remarks',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'employee_id' => 'required|integer',
        'title'       => 'required|min_length[3]|max_length[255]',
        'description' => 'required|min_length[10]',
        'status'      => 'in_list[Pending,In Progress,Completed,Review]',
        'priority'    => 'permit_empty|in_list[Low,Medium,High,Urgent]',
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Task title is required',
            'min_length' => 'Title must be at least 3 characters',
        ],
        'description' => [
            'required' => 'Task description is required',
            'min_length' => 'Description must be at least 10 characters',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $beforeUpdate   = [];

    /**
     * Get tasks assigned to a specific employee
     */
    public function getEmployeeTasks($employeeId)
    {
        return $this->select('employee_tasks.*, clients.name as client_name, users.first_name as assigned_by_name, users.last_name as assigned_by_lastname')
                    ->join('clients', 'clients.id = employee_tasks.client_id', 'left')
                    ->join('users', 'users.id = employee_tasks.assigned_by', 'left')
                    ->where('employee_tasks.employee_id', $employeeId)
                    ->orderBy('employee_tasks.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get all tasks with employee and client info
     */
    public function getAllTasksWithDetails()
    {
        return $this->select('employee_tasks.*, 
                             employees.first_name as emp_first_name, 
                             employees.last_name as emp_last_name,
                             employees.email as emp_email,
                             clients.name as client_name,
                             departments.name as department_name,
                             users.first_name as assigned_by_name,
                             users.last_name as assigned_by_lastname')
                    ->join('employees', 'employees.id = employee_tasks.employee_id', 'left')
                    ->join('clients', 'clients.id = employee_tasks.client_id', 'left')
                    ->join('departments', 'departments.id = employees.department_id', 'left')
                    ->join('users', 'users.id = employee_tasks.assigned_by', 'left')
                    ->orderBy('employee_tasks.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get task details with all relations
     */
    public function getTaskDetails($id)
    {
        return $this->select('employee_tasks.*, 
                             employees.first_name as emp_first_name, 
                             employees.last_name as emp_last_name,
                             employees.email as emp_email,
                             employees.phone as emp_phone,
                             clients.name as client_name,
                             clients.email as client_email,
                             departments.name as department_name,
                             users.first_name as assigned_by_name,
                             users.last_name as assigned_by_lastname')
                    ->join('employees', 'employees.id = employee_tasks.employee_id', 'left')
                    ->join('clients', 'clients.id = employee_tasks.client_id', 'left')
                    ->join('departments', 'departments.id = employees.department_id', 'left')
                    ->join('users', 'users.id = employee_tasks.assigned_by', 'left')
                    ->find($id);
    }
}

<?php

namespace App\Controllers;

use App\Models\EmployeeTaskModel;
use App\Models\ClientModel;
use App\Models\EmployeeModel;
use App\Models\UserModel;

class TaskManagement extends BaseController
{
    protected $taskModel;
    protected $clientModel;
    protected $employeeModel;
    protected $userModel;

    public function __construct()
    {
        $this->taskModel = new EmployeeTaskModel();
        $this->clientModel = new ClientModel();
        $this->employeeModel = new EmployeeModel();
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    /**
     * View all employee tasks (Admin & Admin Manager)
     */
    /**
     * View all employee tasks (Admin & Admin Manager)
     */
    public function index()
    {
        $userRoleId = session()->get('role_id');

        // Get filter parameters
        $employeeId = $this->request->getGet('employee_id');
        $clientId = $this->request->getGet('client_id');
        $status = $this->request->getGet('status');
        $fromDate = $this->request->getGet('from_date');
        $toDate = $this->request->getGet('to_date');

        // Build query with filters
        $builder = $this->taskModel
            ->select('employee_tasks.*, 
                 employees.first_name as emp_first_name, 
                 employees.last_name as emp_last_name,
                 clients.name as client_name,
                 departments.name as department_name')
            ->join('employees', 'employees.id = employee_tasks.employee_id', 'left')
            ->join('clients', 'clients.id = employee_tasks.client_id', 'left')
            ->join('departments', 'departments.id = employees.department_id', 'left');

        // Apply filters
        if (!empty($employeeId)) {
            $builder->where('employee_tasks.employee_id', $employeeId);
        }

        if (!empty($clientId)) {
            $builder->where('employee_tasks.client_id', $clientId);
        }

        if (!empty($status)) {
            $builder->where('employee_tasks.status', $status);
        }

        if (!empty($fromDate)) {
            $builder->where('DATE(employee_tasks.submitted_at) >=', $fromDate);
        }

        if (!empty($toDate)) {
            $builder->where('DATE(employee_tasks.submitted_at) <=', $toDate);
        }

        $tasks = $builder->orderBy('employee_tasks.submitted_at', 'DESC')->findAll();

        // Get filter data
        $employees = $this->employeeModel->findAll();
        $clients = $this->clientModel->findAll();

        $data = [
            'title' => 'Employee Tasks Management',
            'tasks' => $tasks,
            'employees' => $employees,
            'clients' => $clients
        ];

        return view('task_management/index', $data);
    }


    /**
     * View task details
     */
    public function view($id)
    {
        $task = $this->taskModel
            ->select('employee_tasks.*, 
                     employees.first_name as emp_first_name, 
                     employees.last_name as emp_last_name,
                     employees.email as emp_email,
                     employees.phone as emp_phone,
                     clients.name as client_name,
                     clients.email as client_email,
                     departments.name as department_name')
            ->join('employees', 'employees.id = employee_tasks.employee_id', 'left')
            ->join('clients', 'clients.id = employee_tasks.client_id', 'left')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->find($id);

        if (!$task) {
            session()->setFlashdata('error', 'Task not found.');
            return redirect()->to(base_url('task-management'));
        }

        $data = [
            'title' => 'Task Details',
            'task' => $task
        ];

        return view('task_management/view', $data);
    }

    /**
     * Update task status (Admin only)
     */
    public function updateStatus($id)
    {
        $task = $this->taskModel->find($id);

        if (!$task) {
            session()->setFlashdata('error', 'Task not found.');
            return redirect()->to(base_url('task-management'));
        }

        $newStatus = $this->request->getPost('status');

        if (!in_array($newStatus, ['Pending', 'In Progress', 'Completed', 'Review'])) {
            session()->setFlashdata('error', 'Invalid status.');
            return redirect()->back();
        }

        try {
            $this->taskModel->update($id, [
                'status' => $newStatus,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            session()->setFlashdata('success', 'Task status updated successfully!');
        } catch (\Exception $e) {
            log_message('error', 'Status Update Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to update status.');
        }

        return redirect()->back();
    }

    /**
     * Delete task (Admin only)
     */
    public function delete($id)
    {
        $task = $this->taskModel->find($id);

        if (!$task) {
            session()->setFlashdata('error', 'Task not found.');
            return redirect()->to(base_url('task-management'));
        }

        try {
            // Delete associated files
            if (!empty($task['files_upload'])) {
                $files = json_decode($task['files_upload'], true);
                if (is_array($files)) {
                    foreach ($files as $file) {
                        $filePath = FCPATH . 'uploads/task_files/' . $file;
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
            }

            // Delete task record
            $this->taskModel->delete($id);

            session()->setFlashdata('success', 'Task deleted successfully!');
        } catch (\Exception $e) {
            log_message('error', 'Task Deletion Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to delete task.');
        }

        return redirect()->to(base_url('task-management'));
    }

    /**
     * Filter tasks by employee or client (AJAX)
     */
    public function filter()
    {
        $employeeId = $this->request->getGet('employee_id');
        $clientId = $this->request->getGet('client_id');
        $status = $this->request->getGet('status');

        $builder = $this->taskModel
            ->select('employee_tasks.*, 
                     employees.first_name as emp_first_name, 
                     employees.last_name as emp_last_name,
                     clients.name as client_name,
                     departments.name as department_name')
            ->join('employees', 'employees.id = employee_tasks.employee_id', 'left')
            ->join('clients', 'clients.id = employee_tasks.client_id', 'left')
            ->join('departments', 'departments.id = employees.department_id', 'left');

        if (!empty($employeeId)) {
            $builder->where('employee_tasks.employee_id', $employeeId);
        }

        if (!empty($clientId)) {
            $builder->where('employee_tasks.client_id', $clientId);
        }

        if (!empty($status)) {
            $builder->where('employee_tasks.status', $status);
        }

        $tasks = $builder->orderBy('employee_tasks.submitted_at', 'DESC')->findAll();

        return $this->response->setJSON($tasks);
    }
}

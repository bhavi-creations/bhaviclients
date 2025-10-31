<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\EmployeeTask.php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeeTaskModel;
use App\Models\EmployeeModel;

class EmployeeTask extends BaseController
{
    protected $taskModel;
    protected $employeeModel;
    protected $validation;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->taskModel = new EmployeeTaskModel();
        $this->employeeModel = new EmployeeModel();
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);

        // Access control - Only Employees
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
        $userId = session()->get('id');
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        return $user['employee_id'] ?? null;
    }

    /**
     * Display all tasks assigned to employee from new task management system
     */
    public function myTasks()
    {
        // Get employee ID from logged-in user
        $userId = session()->get('id');
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        $employeeId = $user['employee_id'] ?? null;

        if (!$employeeId) {
            session()->setFlashdata('error', 'Employee profile not found.');
            return redirect()->to(base_url('employee-dashboard'));
        }

        // Get filter parameters
        $request = \Config\Services::request();
        $statusFilter = $request->getGet('status');
        $priorityFilter = $request->getGet('priority');

        // Build query for new task system
        $builder = $this->db->table('employee_tasks')
            ->select('employee_tasks.*, clients.name as client_name, users.first_name as assigned_by_name, users.last_name as assigned_by_lastname')
            ->join('clients', 'clients.id = employee_tasks.client_id', 'left')
            ->join('users', 'users.id = employee_tasks.assigned_by', 'left')
            ->where('employee_tasks.employee_id', $employeeId);

        // Apply filters
        if (!empty($statusFilter)) {
            $builder->where('employee_tasks.status', $statusFilter);
        }

        if (!empty($priorityFilter)) {
            $builder->where('employee_tasks.priority', $priorityFilter);
        }

        $tasks = $builder->orderBy('employee_tasks.created_at', 'DESC')->get()->getResultArray();

        $data = [
            'title' => 'My Tasks',
            'tasks' => $tasks
        ];

        return view('employee/my_tasks', $data);
    }


    /**
     * View task details
     */
    public function viewTask($id)
    {
        $employeeId = $this->getEmployeeId();
        $task = $this->taskModel->getTaskDetails($id);

        if (!$task || $task['employee_id'] != $employeeId) {
            session()->setFlashdata('error', 'Task not found or access denied.');
            return redirect()->to(base_url('my-tasks'));
        }

        $data = [
            'title' => 'Task Details',
            'task' => $task
        ];

        return view('employee/view_task', $data);
    }

    /**
     * Update task status
     */
    public function updateStatus($id)
    {
        $employeeId = $this->getEmployeeId();
        $task = $this->taskModel->find($id);

        if (!$task || $task['employee_id'] != $employeeId) {
            session()->setFlashdata('error', 'Task not found or access denied.');
            return redirect()->to(base_url('my-tasks'));
        }

        $newStatus = $this->request->getPost('status');

        if (!in_array($newStatus, ['Pending', 'In Progress', 'Completed', 'Review'])) {
            session()->setFlashdata('error', 'Invalid status.');
            return redirect()->back();
        }

        try {
            $updateData = [
                'status' => $newStatus
            ];

            // If marking as completed, set submitted_at
            if ($newStatus == 'Completed' && empty($task['submitted_at'])) {
                $updateData['submitted_at'] = date('Y-m-d H:i:s');
            }

            $this->taskModel->update($id, $updateData);
            session()->setFlashdata('success', 'Task status updated successfully!');
        } catch (\Exception $e) {
            log_message('error', 'Status Update Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to update status.');
        }

        return redirect()->back();
    }

    /**
     * Show submit work form
     */
    public function submitWork($id)
    {
        $employeeId = $this->getEmployeeId();
        $task = $this->taskModel->find($id);

        if (!$task || $task['employee_id'] != $employeeId) {
            session()->setFlashdata('error', 'Task not found or access denied.');
            return redirect()->to(base_url('my-tasks'));
        }

        $data = [
            'title' => 'Submit Work',
            'task' => $task,
            'validation' => $this->validation
        ];

        return view('employee/submit_work', $data);
    }

    /**
     * Store submitted work
     */
    public function storeWork($id)
    {
        $employeeId = $this->getEmployeeId();
        $task = $this->taskModel->find($id);

        if (!$task || $task['employee_id'] != $employeeId) {
            session()->setFlashdata('error', 'Task not found or access denied.');
            return redirect()->to(base_url('my-tasks'));
        }

        $rules = [
            'employee_remarks' => 'permit_empty',
            'status' => 'required|in_list[In Progress,Completed]'
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
            $updateData = [
                'employee_remarks' => !empty($input['employee_remarks']) ? trim($input['employee_remarks']) : $task['employee_remarks'],
                'status' => $input['status']
            ];

            // Handle employee work file uploads
            $files = $this->request->getFiles('work_files');

            if (!empty($files)) {
                $uploadPath = FCPATH . 'uploads/task_files/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Get existing files
                $existingFiles = !empty($task['employee_files']) ? json_decode($task['employee_files'], true) : [];
                $uploadedFiles = is_array($existingFiles) ? $existingFiles : [];

                foreach ($files as $file) {
                    if (is_array($file)) {
                        foreach ($file as $singleFile) {
                            if ($singleFile->isValid() && !$singleFile->hasMoved()) {
                                $newName = 'employee_' . time() . '_' . $singleFile->getRandomName();
                                $singleFile->move($uploadPath, $newName);
                                $uploadedFiles[] = $newName;
                            }
                        }
                    } else {
                        if ($file->isValid() && !$file->hasMoved()) {
                            $newName = 'employee_' . time() . '_' . $file->getRandomName();
                            $file->move($uploadPath, $newName);
                            $uploadedFiles[] = $newName;
                        }
                    }
                }

                if (!empty($uploadedFiles)) {
                    $updateData['employee_files'] = json_encode($uploadedFiles);
                }
            }

            // If marking as completed, set submitted_at
            if ($input['status'] == 'Completed' && empty($task['submitted_at'])) {
                $updateData['submitted_at'] = date('Y-m-d H:i:s');
            }

            $this->taskModel->update($id, $updateData);

            $this->db->transComplete();

            session()->setFlashdata('success', 'Work submitted successfully!');
            return redirect()->to(base_url('my-tasks'));
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Work Submission Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to submit work: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}

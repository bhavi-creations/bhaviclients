<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\TaskManagement.php

namespace App\Controllers;

use App\Models\EmployeeTaskModel;
use App\Models\ClientModel;
use App\Models\EmployeeModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;

class TaskManagement extends BaseController
{
    protected $taskModel;
    protected $clientModel;
    protected $employeeModel;
    protected $userModel;
    protected $departmentModel;
    protected $validation;
    protected $db;

    public function __construct()
    {
        $this->taskModel = new EmployeeTaskModel();
        $this->clientModel = new ClientModel();
        $this->employeeModel = new EmployeeModel();
        $this->userModel = new UserModel();
        $this->departmentModel = new DepartmentModel();
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
     * View all employee tasks (Admin & Admin Manager)
     */
    public function index()
    {
        // Get filter parameters
        $employeeId = $this->request->getGet('employee_id');
        $departmentId = $this->request->getGet('department_id');
        $clientId = $this->request->getGet('client_id');
        $status = $this->request->getGet('status');
        $priority = $this->request->getGet('priority');
        $fromDate = $this->request->getGet('from_date');
        $toDate = $this->request->getGet('to_date');

        // Build query with filters
        $builder = $this->taskModel
            ->select('employee_tasks.*, 
                 employees.first_name as emp_first_name, 
                 employees.last_name as emp_last_name,
                 clients.name as client_name,
                 departments.name as department_name,
                 users.first_name as assigned_by_name,
                 users.last_name as assigned_by_lastname')
            ->join('employees', 'employees.id = employee_tasks.employee_id', 'left')
            ->join('clients', 'clients.id = employee_tasks.client_id', 'left')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->join('users', 'users.id = employee_tasks.assigned_by', 'left');

        // Apply filters
        if (!empty($employeeId)) {
            $builder->where('employee_tasks.employee_id', $employeeId);
        }

        if (!empty($departmentId)) {
            $builder->where('employees.department_id', $departmentId);
        }

        if (!empty($clientId)) {
            $builder->where('employee_tasks.client_id', $clientId);
        }

        if (!empty($status)) {
            $builder->where('employee_tasks.status', $status);
        }

        if (!empty($priority)) {
            $builder->where('employee_tasks.priority', $priority);
        }

        if (!empty($fromDate)) {
            $builder->where('DATE(employee_tasks.created_at) >=', $fromDate);
        }

        if (!empty($toDate)) {
            $builder->where('DATE(employee_tasks.created_at) <=', $toDate);
        }

        $tasks = $builder->orderBy('employee_tasks.created_at', 'DESC')->findAll();

        // Get filter data
        $employees = $this->employeeModel->findAll();
        $clients = $this->clientModel->findAll();
        $departments = $this->departmentModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title' => 'Task Management',
            'tasks' => $tasks,
            'employees' => $employees,
            'clients' => $clients,
            'departments' => $departments
        ];

        return view('task_management/index', $data);
    }

    /**
     * Show create task form
     */
    public function create()
    {
        $employees = $this->employeeModel->orderBy('first_name', 'ASC')->findAll();
        $clients = $this->clientModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title' => 'Assign New Task',
            'employees' => $employees,
            'clients' => $clients,
            'validation' => $this->validation
        ];

        return view('task_management/create', $data);
    }

    /**
     * Store new task
     */
    /**
     * Store new task
     */
    public function store()
    {
        $rules = [
            'employee_id' => 'required|integer',
            'client_id' => 'permit_empty|integer',
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]',
            'admin_remarks' => 'permit_empty',
            'due_date' => 'permit_empty',
            'priority' => 'required|in_list[Low,Medium,High,Urgent]',
            'admin_files' => 'permit_empty' // Allow multiple files
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validation)
                ->with('error', 'Validation failed. Please check the form.');
        }

        $input = $this->request->getPost();

        $this->db->transStart();

        try {
            $taskData = [
                'employee_id' => $input['employee_id'],
                'assigned_by' => session()->get('id'),
                'client_id' => !empty($input['client_id']) ? $input['client_id'] : null,
                'title' => trim($input['title']),
                'description' => trim($input['description']),
                'admin_remarks' => !empty($input['admin_remarks']) ? trim($input['admin_remarks']) : null,
                'due_date' => !empty($input['due_date']) ? $input['due_date'] : null,
                'priority' => $input['priority'],
                'status' => 'Pending',
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Handle admin reference file uploads
            $files = $this->request->getFiles('admin_files');

            if (!empty($files)) {
                $uploadPath = FCPATH . 'uploads/task_files/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $uploadedFiles = [];

                foreach ($files as $file) {
                    if (is_array($file)) {
                        foreach ($file as $singleFile) {
                            if ($singleFile->isValid() && !$singleFile->hasMoved()) {
                                $newName = 'admin_' . time() . '_' . $singleFile->getRandomName();
                                $singleFile->move($uploadPath, $newName);
                                $uploadedFiles[] = $newName;
                            }
                        }
                    } else {
                        if ($file->isValid() && !$file->hasMoved()) {
                            $newName = 'admin_' . time() . '_' . $file->getRandomName();
                            $file->move($uploadPath, $newName);
                            $uploadedFiles[] = $newName;
                        }
                    }
                }

                if (!empty($uploadedFiles)) {
                    $taskData['admin_files'] = json_encode($uploadedFiles);
                }
            }

            $this->taskModel->insert($taskData);

            $this->db->transComplete();

            session()->setFlashdata('success', 'Task assigned successfully with reference files!');
            return redirect()->to(base_url('task-management'));
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Task Creation Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to assign task.');
            return redirect()->back()->withInput();
        }
    }


    /**
     * View task details
     */
    public function view($id)
    {
        $task = $this->taskModel->getTaskDetails($id);

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
     * Show edit task form
     */
    public function edit($id)
    {
        $task = $this->taskModel->find($id);

        if (!$task) {
            session()->setFlashdata('error', 'Task not found.');
            return redirect()->to(base_url('task-management'));
        }

        $employees = $this->employeeModel->orderBy('first_name', 'ASC')->findAll();
        $clients = $this->clientModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title' => 'Edit Task',
            'task' => $task,
            'employees' => $employees,
            'clients' => $clients,
            'validation' => $this->validation
        ];

        return view('task_management/edit', $data);
    }

    /**
     * Update task
     */
    public function update($id)
    {
        $task = $this->taskModel->find($id);

        if (!$task) {
            session()->setFlashdata('error', 'Task not found.');
            return redirect()->to(base_url('task-management'));
        }

        $rules = [
            'employee_id' => 'required|integer',
            'client_id' => 'permit_empty|integer',
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]',
            'admin_remarks' => 'permit_empty',
            'due_date' => 'permit_empty',
            'priority' => 'required|in_list[Low,Medium,High,Urgent]',
            'status' => 'required|in_list[Pending,In Progress,Completed,Review]'
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
                'employee_id' => $input['employee_id'],
                'client_id' => !empty($input['client_id']) ? $input['client_id'] : null,
                'title' => trim($input['title']),
                'description' => trim($input['description']),
                'admin_remarks' => !empty($input['admin_remarks']) ? trim($input['admin_remarks']) : null,
                'due_date' => !empty($input['due_date']) ? $input['due_date'] : null,
                'priority' => $input['priority'],
                'status' => $input['status']
            ];

            // Handle admin reference files
            $existingFiles = $this->request->getPost('existing_files') ?? [];
            $newFiles = $this->request->getFiles('admin_files');

            $allFiles = $existingFiles;

            // Upload new files
            if (!empty($newFiles)) {
                $uploadPath = FCPATH . 'uploads/task_files/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                foreach ($newFiles as $file) {
                    if (is_array($file)) {
                        foreach ($file as $singleFile) {
                            if ($singleFile->isValid() && !$singleFile->hasMoved()) {
                                $newName = 'admin_' . time() . '_' . $singleFile->getRandomName();
                                $singleFile->move($uploadPath, $newName);
                                $allFiles[] = $newName;
                            }
                        }
                    } else {
                        if ($file->isValid() && !$file->hasMoved()) {
                            $newName = 'admin_' . time() . '_' . $file->getRandomName();
                            $file->move($uploadPath, $newName);
                            $allFiles[] = $newName;
                        }
                    }
                }
            }

            // Update admin_files if there are any files
            if (!empty($allFiles)) {
                $updateData['admin_files'] = json_encode(array_values($allFiles));
            } else {
                $updateData['admin_files'] = null;
            }

            // Delete removed files from server
            if (!empty($task['admin_files'])) {
                $oldFiles = json_decode($task['admin_files'], true);
                if (is_array($oldFiles)) {
                    $removedFiles = array_diff($oldFiles, $existingFiles);
                    foreach ($removedFiles as $removedFile) {
                        $filePath = FCPATH . 'uploads/task_files/' . $removedFile;
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
            }

            $this->taskModel->update($id, $updateData);

            $this->db->transComplete();

            session()->setFlashdata('success', 'Task updated successfully!');
            return redirect()->to(base_url('task-management'));
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Task Update Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to update task.');
            return redirect()->back()->withInput();
        }
    }


    /**
     * Update task status (Quick update)
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
            $this->taskModel->update($id, ['status' => $newStatus]);
            session()->setFlashdata('success', 'Task status updated successfully!');
        } catch (\Exception $e) {
            log_message('error', 'Status Update Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to update status.');
        }

        return redirect()->back();
    }

    /**
     * Delete task
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

            $this->taskModel->delete($id);
            session()->setFlashdata('success', 'Task deleted successfully!');
        } catch (\Exception $e) {
            log_message('error', 'Task Deletion Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to delete task.');
        }

        return redirect()->to(base_url('task-management'));
    }
}

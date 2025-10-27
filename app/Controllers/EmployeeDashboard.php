<?php

namespace App\Controllers;

use App\Models\EmployeeTaskModel;
use App\Models\ClientModel;
use App\Models\UserModel;

class EmployeeDashboard extends BaseController
{
    protected $taskModel;
    protected $clientModel;
    protected $userModel;
    protected $validation;

    public function __construct()
    {
        $this->taskModel = new EmployeeTaskModel();
        $this->clientModel = new ClientModel();
        $this->userModel = new UserModel();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url']);
    }


    /**
     * Employee Dashboard - Overview with widgets
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !isset($user['employee_id'])) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Employee record not found.');
        }

        $employeeId = $user['employee_id'];

        // Get task statistics for this employee
        $totalTasks = $this->taskModel->where('employee_id', $employeeId)->countAllResults();
        $completedTasks = $this->taskModel->where('employee_id', $employeeId)->where('status', 'Completed')->countAllResults();
        $pendingTasks = $this->taskModel->where('employee_id', $employeeId)->whereIn('status', ['Pending', 'In Progress', 'Review'])->countAllResults();

        // Get recent tasks with client info
        $recentTasks = $this->taskModel
            ->select('employee_tasks.*, clients.name as client_name')
            ->join('clients', 'clients.id = employee_tasks.client_id', 'left')
            ->where('employee_tasks.employee_id', $employeeId)
            ->orderBy('employee_tasks.submitted_at', 'DESC')
            ->limit(5)
            ->findAll();

        $data = [
            'title' => 'Employee Dashboard',
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'pendingTasks' => $pendingTasks,
            'recentTasks' => $recentTasks,
            'employeeId' => $employeeId
        ];

        return view('employee/dashboard', $data);
    }

    /**
     * Display employee's tasks (My Tasks)
     */
    public function myTasks()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !isset($user['employee_id'])) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Employee record not found.');
        }

        $employeeId = $user['employee_id'];

        // Fetch tasks for this employee with client info
        $tasks = $this->taskModel
            ->select('employee_tasks.*, clients.name as client_name, clients.email as client_email')
            ->join('clients', 'clients.id = employee_tasks.client_id', 'left')
            ->where('employee_tasks.employee_id', $employeeId)
            ->orderBy('employee_tasks.submitted_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'My Tasks',
            'tasks' => $tasks,
            'employeeId' => $employeeId
        ];

        return view('employee/my_tasks', $data);
    }

    /**
     * Show form to submit new work/task
     */
    public function submitWork()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !isset($user['employee_id'])) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Employee record not found.');
        }

        $clients = $this->clientModel->findAll();

        $data = [
            'title' => 'Submit Work',
            'clients' => $clients,
            'validation' => $this->validation
        ];

        return view('employee/submit_work', $data);
    }

    /**
     * Store submitted work/task
     */
    public function storeWork()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !isset($user['employee_id'])) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Employee record not found.');
        }

        $employeeId = $user['employee_id'];

        // Validation rules
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]',
            'client_id' => 'permit_empty|integer',
            'status' => 'required|in_list[Pending,In Progress,Completed,Review]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validation);
        }

        $input = $this->request->getPost();

        // Handle file upload
        $uploadedFiles = [];
        $files = $this->request->getFileMultiple('files');

        if ($files) {
            foreach ($files as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(FCPATH . 'uploads/task_files', $newName);
                    $uploadedFiles[] = $newName;
                }
            }
        }

        // Prepare task data
        $taskData = [
            'employee_id' => $employeeId,
            'client_id' => !empty($input['client_id']) ? $input['client_id'] : null,
            'title' => trim($input['title']),
            'description' => trim($input['description']),
            'status' => $input['status'],
            'files_upload' => !empty($uploadedFiles) ? json_encode($uploadedFiles) : null,
            'submitted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        try {
            $this->taskModel->insert($taskData);
            session()->setFlashdata('success', 'Work submitted successfully!');
            return redirect()->to(base_url('my-tasks'));
        } catch (\Exception $e) {
            log_message('error', 'Task Submission Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to submit work. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Edit task
     */
    public function editTask($id)
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !isset($user['employee_id'])) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Employee record not found.');
        }

        $employeeId = $user['employee_id'];

        $task = $this->taskModel->where('id', $id)->where('employee_id', $employeeId)->first();

        if (!$task) {
            return redirect()->to(base_url('my-tasks'))->with('error', 'Task not found or access denied.');
        }

        $clients = $this->clientModel->findAll();

        $data = [
            'title' => 'Edit Task',
            'task' => $task,
            'clients' => $clients,
            'validation' => $this->validation
        ];

        return view('employee/edit_task', $data);
    }

    /**
     * Update task
     */
    public function updateTask($id)
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !isset($user['employee_id'])) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Employee record not found.');
        }

        $employeeId = $user['employee_id'];

        $task = $this->taskModel->where('id', $id)->where('employee_id', $employeeId)->first();

        if (!$task) {
            return redirect()->to(base_url('my-tasks'))->with('error', 'Task not found or access denied.');
        }

        // Validation
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]',
            'client_id' => 'permit_empty|integer',
            'status' => 'required|in_list[Pending,In Progress,Completed,Review]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validation);
        }

        $input = $this->request->getPost();

        // Handle new file uploads
        $existingFiles = json_decode($task['files_upload'], true) ?? [];
        $files = $this->request->getFileMultiple('files');

        if ($files) {
            foreach ($files as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(FCPATH . 'uploads/task_files', $newName);
                    $existingFiles[] = $newName;
                }
            }
        }

        // Update task data
        $taskData = [
            'client_id' => !empty($input['client_id']) ? $input['client_id'] : null,
            'title' => trim($input['title']),
            'description' => trim($input['description']),
            'status' => $input['status'],
            'files_upload' => !empty($existingFiles) ? json_encode($existingFiles) : null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        try {
            $this->taskModel->update($id, $taskData);
            session()->setFlashdata('success', 'Task updated successfully!');
            return redirect()->to(base_url('my-tasks'));
        } catch (\Exception $e) {
            log_message('error', 'Task Update Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to update task.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Delete task
     */
    public function deleteTask($id)
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !isset($user['employee_id'])) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Employee record not found.');
        }

        $employeeId = $user['employee_id'];

        $task = $this->taskModel->where('id', $id)->where('employee_id', $employeeId)->first();

        if (!$task) {
            return redirect()->to(base_url('my-tasks'))->with('error', 'Task not found or access denied.');
        }

        try {
            $this->taskModel->delete($id);
            session()->setFlashdata('success', 'Task deleted successfully!');
        } catch (\Exception $e) {
            log_message('error', 'Task Deletion Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to delete task.');
        }

        return redirect()->to(base_url('my-tasks'));
    }



    /**
     * Delete a specific file from a task
     */
    public function deleteFile($taskId, $fileIndex)
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !isset($user['employee_id'])) {
            session()->setFlashdata('error', 'Employee record not found.');
            return redirect()->to(base_url('dashboard'));
        }

        $employeeId = $user['employee_id'];

        // Find task
        $task = $this->taskModel->where('id', $taskId)->where('employee_id', $employeeId)->first();

        if (!$task) {
            session()->setFlashdata('error', 'Task not found or access denied.');
            return redirect()->to(base_url('my-tasks'));
        }

        // Get files array
        $files = json_decode($task['files_upload'], true);

        if (!is_array($files)) {
            $files = [];
        }

        if (isset($files[$fileIndex])) {
            $fileName = $files[$fileIndex];

            // Delete physical file
            $filePath = FCPATH . 'uploads/task_files/' . $fileName;

            if (file_exists($filePath)) {
                if (unlink($filePath)) {
                    log_message('info', 'File deleted: ' . $filePath);
                } else {
                    log_message('error', 'Failed to delete file: ' . $filePath);
                }
            }

            // Remove from array
            unset($files[$fileIndex]);
            $files = array_values($files); // Re-index array

            // Update database
            $updateData = [
                'files_upload' => !empty($files) ? json_encode($files) : null,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            try {
                $this->taskModel->update($taskId, $updateData);
                session()->setFlashdata('success', 'File deleted successfully!');
            } catch (\Exception $e) {
                log_message('error', 'File Deletion Error: ' . $e->getMessage());
                session()->setFlashdata('error', 'Failed to delete file from database.');
            }
        } else {
            session()->setFlashdata('error', 'File not found in task.');
        }

        return redirect()->to(base_url('edit-task/' . $taskId));
    }
}

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
    protected $db;


    public function __construct()
    {
        $this->taskModel = new EmployeeTaskModel();
        $this->clientModel = new ClientModel();
        $this->userModel = new UserModel();
        $this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect();

        helper(['form', 'url']);
    }




    /**
     * Employee Dashboard - Overview with widgets
     */
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

        // Get employee details
        $employee = $this->db->table('employees')
            ->select('employees.*, departments.name as department_name')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->where('employees.id', $employeeId)
            ->get()
            ->getRowArray();

        // Get task statistics for this employee
        $totalTasks = $this->taskModel->where('employee_id', $employeeId)->countAllResults();
        $completedTasks = $this->taskModel->where('employee_id', $employeeId)->where('status', 'Completed')->countAllResults();
        $pendingTasks = $this->taskModel->where('employee_id', $employeeId)->whereIn('status', ['Pending', 'In Progress'])->countAllResults();
        $reviewTasks = $this->taskModel->where('employee_id', $employeeId)->where('status', 'Review')->countAllResults();

        // Get recent tasks with client info
        $recentTasks = $this->taskModel
            ->select('employee_tasks.*, clients.name as client_name')
            ->join('clients', 'clients.id = employee_tasks.client_id', 'left')
            ->where('employee_tasks.employee_id', $employeeId)
            ->orderBy('employee_tasks.submitted_at', 'DESC')
            ->limit(8)
            ->findAll();

        // Get latest salary
        $latestSalary = $this->db->table('employee_salary_history')
            ->where('employee_id', $employeeId)
            ->orderBy('effective_date', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        // Get payslip count
        $payslipCount = $this->db->table('employee_payslips')
            ->where('employee_id', $employeeId)
            ->countAllResults();

        // Get client assets count
        $clientAssetsCount = $this->db->table('client_assets')->countAllResults();

        $data = [
            'title' => 'Employee Dashboard',
            'employee' => $employee,
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'pendingTasks' => $pendingTasks,
            'reviewTasks' => $reviewTasks,
            'recentTasks' => $recentTasks,
            'latestSalary' => $latestSalary,
            'payslipCount' => $payslipCount,
            'clientAssetsCount' => $clientAssetsCount,
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

    /**
     * View task details - NEW SYSTEM
     */
    public function viewTask($id)
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !isset($user['employee_id'])) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Employee record not found.');
        }

        $employeeId = $user['employee_id'];

        // Get task with all details
        $task = $this->taskModel
            ->select('employee_tasks.*, 
                     clients.name as client_name,
                     clients.email as client_email,
                     users.first_name as assigned_by_name,
                     users.last_name as assigned_by_lastname')
            ->join('clients', 'clients.id = employee_tasks.client_id', 'left')
            ->join('users', 'users.id = employee_tasks.assigned_by', 'left')
            ->where('employee_tasks.id', $id)
            ->first();

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
     * Show submit work form - NEW SYSTEM
     */
    public function submitWorkForm($id)
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !isset($user['employee_id'])) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Employee record not found.');
        }

        $employeeId = $user['employee_id'];

        $task = $this->taskModel->where('id', $id)->where('employee_id', $employeeId)->first();

        if (!$task) {
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
     * Store submitted work - NEW SYSTEM
     */
    public function storeTaskWork($id)
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !isset($user['employee_id'])) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Employee record not found.');
        }

        $employeeId = $user['employee_id'];

        $task = $this->taskModel->where('id', $id)->where('employee_id', $employeeId)->first();

        if (!$task) {
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

        try {
            $updateData = [
                'employee_remarks' => !empty($input['employee_remarks']) ? trim($input['employee_remarks']) : $task['employee_remarks'],
                'status' => $input['status'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Handle employee work file uploads
            $files = $this->request->getFiles('work_files');

            if (!empty($files)) {
                $uploadPath = FCPATH . 'uploads/task_files/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Get existing files from employee_files column
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

            session()->setFlashdata('success', 'Work submitted successfully!');
            return redirect()->to(base_url('my-tasks'));
        } catch (\Exception $e) {
            log_message('error', 'Work Submission Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to submit work: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Update task status quickly - NEW SYSTEM
     */
    public function updateTaskStatus($id)
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !isset($user['employee_id'])) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Employee record not found.');
        }

        $employeeId = $user['employee_id'];

        $task = $this->taskModel->where('id', $id)->where('employee_id', $employeeId)->first();

        if (!$task) {
            session()->setFlashdata('error', 'Task not found or access denied.');
            return redirect()->to(base_url('my-tasks'));
        }

        $newStatus = $this->request->getPost('status');

        if (!in_array($newStatus, ['Pending', 'In Progress', 'Completed'])) {
            session()->setFlashdata('error', 'Invalid status.');
            return redirect()->back();
        }

        try {
            $updateData = [
                'status' => $newStatus,
                'updated_at' => date('Y-m-d H:i:s')
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
     * Show edit work form - NEW SYSTEM
     */
    public function editTaskWork($id)
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !isset($user['employee_id'])) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Employee record not found.');
        }

        $employeeId = $user['employee_id'];

        $task = $this->taskModel->where('id', $id)->where('employee_id', $employeeId)->first();

        if (!$task) {
            session()->setFlashdata('error', 'Task not found or access denied.');
            return redirect()->to(base_url('my-tasks'));
        }

        // REMOVED: Can't edit completed tasks check

        $data = [
            'title' => 'Edit Work Submission',
            'task' => $task,
            'validation' => $this->validation
        ];

        return view('employee/edit_task_work', $data);
    }

    /**
     * Update work submission - NEW SYSTEM
     */
    public function updateTaskWork($id)
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !isset($user['employee_id'])) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Employee record not found.');
        }

        $employeeId = $user['employee_id'];

        $task = $this->taskModel->where('id', $id)->where('employee_id', $employeeId)->first();

        if (!$task) {
            session()->setFlashdata('error', 'Task not found or access denied.');
            return redirect()->to(base_url('my-tasks'));
        }

        // REMOVED: Can't edit completed tasks check

        $rules = [
            'employee_remarks' => 'permit_empty',
            'status' => 'required|in_list[Pending,In Progress,Completed]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validation)
                ->with('error', 'Validation failed.');
        }

        $input = $this->request->getPost();

        try {
            $updateData = [
                'employee_remarks' => !empty($input['employee_remarks']) ? trim($input['employee_remarks']) : null,
                'status' => $input['status'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Handle file management
            $existingFiles = !empty($task['employee_files']) ? json_decode($task['employee_files'], true) : [];
            $keepFiles = $this->request->getPost('keep_files') ?? [];

            // Filter existing files to keep only selected ones
            $keptFiles = [];
            foreach ($existingFiles as $index => $file) {
                if (in_array($index, $keepFiles)) {
                    $keptFiles[] = $file;
                } else {
                    // Delete file from server
                    $filePath = FCPATH . 'uploads/task_files/' . $file;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            // Handle new file uploads
            $files = $this->request->getFiles('work_files');

            if (!empty($files)) {
                $uploadPath = FCPATH . 'uploads/task_files/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                foreach ($files as $file) {
                    if (is_array($file)) {
                        foreach ($file as $singleFile) {
                            if ($singleFile->isValid() && !$singleFile->hasMoved()) {
                                $newName = 'employee_' . time() . '_' . $singleFile->getRandomName();
                                $singleFile->move($uploadPath, $newName);
                                $keptFiles[] = $newName;
                            }
                        }
                    } else {
                        if ($file->isValid() && !$file->hasMoved()) {
                            $newName = 'employee_' . time() . '_' . $file->getRandomName();
                            $file->move($uploadPath, $newName);
                            $keptFiles[] = $newName;
                        }
                    }
                }
            }

            // Update files
            $updateData['employee_files'] = !empty($keptFiles) ? json_encode($keptFiles) : null;

            // Update submitted_at timestamp when editing
            if ($input['status'] == 'Completed') {
                $updateData['submitted_at'] = date('Y-m-d H:i:s');
            }

            $this->taskModel->update($id, $updateData);

            session()->setFlashdata('success', 'Work updated successfully!');
            return redirect()->to(base_url('my-tasks'));
        } catch (\Exception $e) {
            log_message('error', 'Work Update Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to update work: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}

<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\ClientFileModel;
use App\Models\EmployeeTaskModel;
use App\Models\UserModel;

class ClientDashboard extends BaseController
{
    protected $clientModel;
    protected $clientFileModel;
    protected $taskModel;
    protected $userModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->clientFileModel = new ClientFileModel();
        $this->taskModel = new EmployeeTaskModel();
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    /**
     * Client Dashboard
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $clientId = session()->get('client_id');

        if (!$clientId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client information not found.');
        }

        // Get client details
        $client = $this->clientModel->find($clientId);

        if (!$client) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client not found.');
        }

        // Get statistics
        $totalFiles = $this->clientFileModel->where('client_id', $clientId)->countAllResults();

        $totalTasks = $this->taskModel->where('client_id', $clientId)->countAllResults();

        $completedTasks = $this->taskModel
            ->where('client_id', $clientId)
            ->where('status', 'Completed')
            ->countAllResults();

        $pendingTasks = $this->taskModel
            ->where('client_id', $clientId)
            ->whereIn('status', ['Pending', 'In Progress', 'Review'])
            ->countAllResults();

        // Get recent tasks
        $recentTasks = $this->taskModel
            ->select('employee_tasks.*, employees.first_name as emp_first_name, employees.last_name as emp_last_name')
            ->join('employees', 'employees.id = employee_tasks.employee_id', 'left')
            ->where('employee_tasks.client_id', $clientId)
            ->orderBy('employee_tasks.submitted_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Get recent admin Excel files
        $recentFiles = $this->clientFileModel
            ->where('client_id', $clientId)
            ->where('uploaded_by', 'admin')
            ->groupStart()
            ->like('original_name', '.xls', 'both')
            ->orLike('original_name', '.xlsx', 'both')
            ->groupEnd()
            ->orderBy('uploaded_at', 'DESC')
            ->limit(5)
            ->findAll();

        $data = [
            'title' => 'Client Dashboard',
            'client' => $client,
            'totalFiles' => $totalFiles,
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'pendingTasks' => $pendingTasks,
            'recentTasks' => $recentTasks,
            'recentFiles' => $recentFiles
        ];

        return view('client_portal/dashboard', $data);
    }

    /**
     * Work Updates
     */
    public function workUpdates()
    {
        $clientId = session()->get('client_id');

        if (!$clientId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client information not found.');
        }

        $status = $this->request->getGet('status');
        $fromDate = $this->request->getGet('from_date');
        $toDate = $this->request->getGet('to_date');

        $builder = $this->taskModel
            ->select('employee_tasks.*, employees.first_name as emp_first_name, employees.last_name as emp_last_name, departments.name as department_name')
            ->join('employees', 'employees.id = employee_tasks.employee_id', 'left')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->where('employee_tasks.client_id', $clientId);

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

        $data = [
            'title' => 'Work Updates',
            'tasks' => $tasks
        ];

        return view('client_portal/work_updates', $data);
    }

    /**
     * View Single Task
     */
    public function viewTask($taskId)
    {
        $clientId = session()->get('client_id');

        $task = $this->taskModel
            ->select('employee_tasks.*, employees.first_name as emp_first_name, employees.last_name as emp_last_name, employees.email as emp_email, employees.phone as emp_phone, departments.name as department_name')
            ->join('employees', 'employees.id = employee_tasks.employee_id', 'left')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->where('employee_tasks.id', $taskId)
            ->where('employee_tasks.client_id', $clientId)
            ->first();

        if (!$task) {
            return redirect()->to(base_url('work-updates'))->with('error', 'Task not found or access denied.');
        }

        $data = [
            'title' => 'Work Details',
            'task' => $task
        ];

        return view('client_portal/view_task', $data);
    }

    /**
     * Download only admin-uploaded Excel files (to download_files.php)
     */
    public function downloadFiles()
    {
        $clientId = session()->get('client_id');

        if (!$clientId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client information not found.');
        }

        $files = $this->clientFileModel
            ->where('client_id', $clientId)
            ->where('uploaded_by', 'admin')
            ->groupStart()
            ->like('original_name', '.xls', 'both')
            ->orLike('original_name', '.xlsx', 'both')
            ->groupEnd()
            ->orderBy('uploaded_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Download Excel Files',
            'files' => $files
        ];

        return view('client_portal/download_files', $data);
    }

    /**
     * Download client's own uploads (to self_uploads.php)
     */
    public function selfUploads()
    {
        $clientId = session()->get('client_id');
        if (!$clientId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client information not found.');
        }

        $files = $this->clientFileModel
            ->where('client_id', $clientId)
            ->where('uploaded_by', $clientId) // or 'client' if that's your logic
            ->orderBy('uploaded_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Your Uploaded Files',
            'files' => $files
        ];

        return view('client_portal/self_uploads', $data);
    }

    /**
     * Download file (protect both admin/client uploads)
     */
    public function downloadFile($fileId)
    {
        $clientId = session()->get('client_id');

        $file = $this->clientFileModel
            ->where('id', $fileId)
            ->where('client_id', $clientId)
            ->first();

        if (!$file) {
            return redirect()->back()->with('error', 'File not found or access denied.');
        }

        $uploadBase = ($file['uploaded_by'] === 'admin')
            ? 'uploads/clients/'
            : 'uploads/client_uploads/';

        $filePath = FCPATH . $uploadBase . $file['file_name'];

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        return $this->response->download($filePath, null)->setFileName($file['original_name']);
    }

    /**
     * Show upload files form
     */
    public function uploadFiles()
    {
        $clientId = session()->get('client_id');
        if (!$clientId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client information not found.');
        }
        $client = $this->clientModel->find($clientId);

        $data = [
            'title' => 'Upload Files',
            'client' => $client
        ];

        return view('client_portal/upload_files', $data);
    }

    /**
     * Store uploaded files (self-upload)
     */
    public function storeFiles()
    {
        $clientId = session()->get('client_id');
        if (!$clientId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client information not found.');
        }

        $files = $this->request->getFiles();

        if (empty($files['client_files'])) {
            return redirect()->back()->with('error', 'No files selected.');
        }

        $uploadPath = FCPATH . 'uploads/client_uploads/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $uploadedCount = 0;

        foreach ($files['client_files'] as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);

                $fileData = [
                    'client_id' => $clientId,
                    'file_name' => $newName,
                    'original_name' => $file->getClientName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => $clientId, // store the client's user_id!
                    'uploaded_at' => date('Y-m-d H:i:s'),
                ];

                $this->clientFileModel->insert($fileData);
                $uploadedCount++;
            }
        }

        return redirect()->to(base_url('upload-files'))
            ->with('success', $uploadedCount . ' file(s) uploaded successfully! Admin and employees can now view them.');
    }

    public function deleteSelfUpload($fileId)
    {
        $clientId = session()->get('client_id');
        if (!$clientId) {
            return redirect()->to(base_url('self-uploads'))->with('error', 'Client information not found.');
        }

        // Find the file and ensure it's the client's self-upload
        $file = $this->clientFileModel
            ->where('id', $fileId)
            ->where('client_id', $clientId)
            ->where('uploaded_by', $clientId) // only allow deleting own uploads!
            ->first();

        if (!$file) {
            return redirect()->to(base_url('self-uploads'))->with('error', 'File not found or not permitted.');
        }

        $filePath = FCPATH . 'uploads/client_uploads/' . $file['file_name'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $this->clientFileModel->delete($fileId);

        return redirect()->to(base_url('self-uploads'))->with('success', 'File deleted successfully.');
    }
}

<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\ClientFileModel;
use App\Models\EmployeeTaskModel;
use App\Models\UserModel;

class ClientManager extends BaseController
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
    public function index()
    {
        $user = $this->userModel->find(session()->get('user_id'));
        $clientId = $user['client_id'];

        if (!$clientId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'No assigned client.');
        }

        // Get client details
        $client = $this->clientModel->find($clientId);

        if (!$client) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client not found.');
        }

        // Get statistics - FIXED: Count only admin uploaded Excel files
        $totalFiles = $this->clientFileModel
            ->where('client_id', $clientId)
            ->where('uploaded_by', 'admin')
            ->groupStart()
            ->like('original_name', '.xls', 'both')
            ->orLike('original_name', '.xlsx', 'both')
            ->groupEnd()
            ->countAllResults();

        $totalTasks = $this->taskModel->where('client_id', $clientId)->countAllResults();

        $completedTasks = $this->taskModel
            ->where('client_id', $clientId)
            ->where('status', 'Completed')
            ->countAllResults();

        $pendingTasks = $this->taskModel
            ->where('client_id', $clientId)
            ->whereIn('status', ['Pending', 'In Progress', 'Review'])
            ->countAllResults();

        // NEW: Get total weekly schedules count
        $db = \Config\Database::connect();
        $totalSchedules = $db->table('client_weekly_schedules')
            ->where('client_id', $clientId)
            ->where('status', 'published')
            ->countAllResults();

        // Get current week's schedule
        $today = new \DateTime();
        $weekDay = $today->format('N');
        $daysToSubtract = $weekDay - 1;
        $monday = clone $today;
        $monday->sub(new \DateInterval("P{$daysToSubtract}D"));
        $weekStartDate = $monday->format('Y-m-d');

        $sunday = clone $monday;
        $sunday->add(new \DateInterval('P6D'));
        $weekEndDate = $sunday->format('Y-m-d');

        // Get weekly schedule from database
        $weeklySchedule = $db->table('client_weekly_schedules')
            ->where('client_id', $clientId)
            ->where('week_start_date', $weekStartDate)
            ->where('status', 'published')
            ->get()
            ->getRowArray();

        // Prepare week schedule data
        $weekScheduleData = null;
        $departments = [];

        if ($weeklySchedule) {
            $departments = json_decode($weeklySchedule['department_columns'], true);
            $scheduleData = json_decode($weeklySchedule['schedule_data'], true);
            $currentDay = $today->format('l');

            $weekScheduleData = [
                'week_start' => $weekStartDate,
                'week_end' => $weekEndDate,
                'departments' => $departments,
                'schedule' => $scheduleData,
                'current_day' => $currentDay,
                'notes' => $weeklySchedule['notes'] ?? null
            ];
        }

        // Get recent tasks
        $recentTasks = $this->taskModel
            ->select('employee_tasks.*, employees.first_name as emp_first_name, employees.last_name as emp_last_name')
            ->join('employees', 'employees.id = employee_tasks.employee_id', 'left')
            ->where('employee_tasks.client_id', $clientId)
            ->orderBy('employee_tasks.submitted_at', 'DESC')
            ->limit(5)
            ->findAll();

        // NEW: Get project details count
        $totalProjects = $db->table('maintenance')
            ->where('client_id', $clientId)
            ->countAllResults();

        $data = [
            'title' => 'Client Manager Dashboard',
            'client' => $client,
            'totalFiles' => $totalFiles,
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'pendingTasks' => $pendingTasks,
            'totalSchedules' => $totalSchedules, // NEW
            'totalProjects' => $totalProjects, // NEW
            'recentTasks' => $recentTasks,
            'weekSchedule' => $weekScheduleData // NEW
        ];

        return view('client_portal/dashboard', $data);
    }



    /**
     * View all clients
     */
    public function clients()
    {
        $clients = $this->clientModel->findAll();

        $data = [
            'title' => 'Manage Clients',
            'clients' => $clients
        ];

        return view('client_manager/clients', $data);
    }

    /**
     * View work updates for all clients
     */
    public function workUpdates()
    {
        // Get filter parameters
        $clientId = $this->request->getGet('client_id');
        $status = $this->request->getGet('status');
        $fromDate = $this->request->getGet('from_date');
        $toDate = $this->request->getGet('to_date');

        // Build query
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

        // Get all clients for filter
        $clients = $this->clientModel->findAll();

        $data = [
            'title' => 'Client Work Updates',
            'tasks' => $tasks,
            'clients' => $clients
        ];

        return view('client_manager/work_updates', $data);
    }

    /**
     * Upload files for clients
     */
    public function uploadFiles()
    {
        $clients = $this->clientModel->findAll();

        $data = [
            'title' => 'Upload Files for Clients',
            'clients' => $clients
        ];

        return view('client_manager/upload_files', $data);
    }

    /**
     * Store uploaded files
     */
    public function storeFiles()
    {
        $clientId = $this->request->getPost('client_id');

        if (empty($clientId)) {
            return redirect()->back()->with('error', 'Please select a client.');
        }

        $files = $this->request->getFiles();

        if (empty($files['client_files'])) {
            return redirect()->back()->with('error', 'No files selected.');
        }

        $uploadPath = FCPATH . 'uploads/clients/';
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
                    'uploaded_by' => 'manager',
                    'uploaded_at' => date('Y-m-d H:i:s'),
                ];

                $this->clientFileModel->insert($fileData);
                $uploadedCount++;
            }
        }

        return redirect()->to(base_url('manager/upload-files'))
            ->with('success', $uploadedCount . ' file(s) uploaded successfully!');
    }

    /**
     * View files for a specific client
     */
    public function clientFiles($clientId)
    {
        $client = $this->clientModel->find($clientId);

        if (!$client) {
            return redirect()->back()->with('error', 'Client not found.');
        }

        $files = $this->clientFileModel
            ->where('client_id', $clientId)
            ->orderBy('uploaded_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Client Files - ' . $client['name'],
            'client' => $client,
            'files' => $files
        ];

        return view('client_manager/client_files', $data);
    }

    /**
     * Download file
     */
    public function downloadFile($fileId)
    {
        $file = $this->clientFileModel->find($fileId);

        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filePath = FCPATH . 'uploads/clients/' . $file['file_name'];

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        return $this->response->download($filePath, null)->setFileName($file['original_name']);
    }

    /**
     * Delete file (Manager only)
     */
    public function deleteFile($fileId)
    {
        $file = $this->clientFileModel->find($fileId);

        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filePath = FCPATH . 'uploads/clients/' . $file['file_name'];

        // Delete physical file
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete database record
        $this->clientFileModel->delete($fileId);

        return redirect()->back()->with('success', 'File deleted successfully.');
    }
}

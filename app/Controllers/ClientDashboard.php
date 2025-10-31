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
    protected $db;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->clientFileModel = new ClientFileModel();
        $this->taskModel = new EmployeeTaskModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }

    /**
     * Get client ID - works for both clients and client managers
     */
    private function getClientId()
    {
        $roleId = session()->get('role_id');

        // Regular client (role_id = 3) - get from session
        if ($roleId == 3) {
            return session()->get('client_id');
        }

        // Client manager (role_id = 4) - get from users table
        if ($roleId == 4) {
            $userId = session()->get('user_id');
            $user = $this->userModel->find($userId);
            return $user['client_id'] ?? null;
        }

        return null;
    }

    /**
     * Client Dashboard
     */
    public function index()
    {
        $userId = session()->get('user_id');
         $clientId = $this->getClientId();

        if (!$clientId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client information not found.');
        }

        // Get client details
        $client = $this->clientModel->find($clientId);

        if (!$client) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client not found.');
        }

        // Get statistics
        // FIXED: Count only admin uploaded Excel files
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
        $totalSchedules = $this->db->table('client_weekly_schedules')
            ->where('client_id', $clientId)
            ->where('status', 'published')
            ->countAllResults();

        // Get current week's schedule
        $today = new \DateTime();
        $weekDay = $today->format('N'); // 1 (Monday) to 7 (Sunday)
        $daysToSubtract = $weekDay - 1; // Days to go back to Monday
        $monday = clone $today;
        $monday->sub(new \DateInterval("P{$daysToSubtract}D"));
        $weekStartDate = $monday->format('Y-m-d');

        $sunday = clone $monday;
        $sunday->add(new \DateInterval('P6D'));
        $weekEndDate = $sunday->format('Y-m-d');

        // Get weekly schedule from database
        $weeklySchedule = $this->db->table('client_weekly_schedules')
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

            // Get current day name
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
        $totalProjects = $this->db->table('maintenance')
            ->where('client_id', $clientId)
            ->countAllResults();

        $data = [
            'title' => 'Client Dashboard',
            'client' => $client,
            'totalFiles' => $totalFiles,
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'pendingTasks' => $pendingTasks,
            'totalSchedules' => $totalSchedules, // NEW
            'totalProjects' => $totalProjects, // NEW
            'recentTasks' => $recentTasks,
            'weekSchedule' => $weekScheduleData
        ];

        return view('client_portal/dashboard', $data);
    }



    /**
     * Work Updates
     */
    public function workUpdates()
    {
        $clientId = $this->getClientId();

        if (!$clientId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client information not found.');
        }

        // Get filter parameters
        $status = $this->request->getGet('status');
        $department = $this->request->getGet('department');
        $fromDate = $this->request->getGet('from_date');
        $toDate = $this->request->getGet('to_date');

        // Build query
        $builder = $this->taskModel
            ->select('employee_tasks.*, 
                     employees.first_name as emp_first_name, 
                     employees.last_name as emp_last_name, 
                     employees.department_id,
                     departments.name as department_name')
            ->join('employees', 'employees.id = employee_tasks.employee_id', 'left')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->where('employee_tasks.client_id', $clientId);

        // Apply filters
        if (!empty($status)) {
            $builder->where('employee_tasks.status', $status);
        }

        // NEW: Department filter
        if (!empty($department)) {
            $builder->where('employees.department_id', $department);
        }

        if (!empty($fromDate)) {
            $builder->where('DATE(employee_tasks.submitted_at) >=', $fromDate);
        }

        if (!empty($toDate)) {
            $builder->where('DATE(employee_tasks.submitted_at) <=', $toDate);
        }

        $tasks = $builder->orderBy('employee_tasks.submitted_at', 'DESC')->findAll();

        // Get all departments for dropdown - NEW
        $departments = $this->db->table('departments')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Work Updates',
            'tasks' => $tasks,
            'departments' => $departments // NEW: Pass departments to view
        ];

        return view('client_portal/work_updates', $data);
    }


    /**
     * View Single Task
     */
    public function viewTask($taskId)
    {
        $clientId = $this->getClientId();

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
        $clientId = $this->getClientId();

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
        $clientId = $this->getClientId();
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
        $clientId = $this->getClientId();

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
        $clientId = $this->getClientId();
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
        $clientId = $this->getClientId();
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
        $clientId = $this->getClientId();
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

    /**
     * My Weekly Schedule - List all schedules
     */
    public function myWeeklySchedule()
    {
        $clientId = $this->getClientId();

        if (!$clientId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client information not found.');
        }

        // Get all published schedules for this client
        $schedules = $this->db->table('client_weekly_schedules')
            ->select('client_weekly_schedules.*, clients.name as client_name')
            ->join('clients', 'clients.id = client_weekly_schedules.client_id', 'left')
            ->where('client_weekly_schedules.client_id', $clientId)
            ->where('client_weekly_schedules.status', 'published')
            ->orderBy('client_weekly_schedules.week_start_date', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'My Weekly Schedules',
            'schedules' => $schedules
        ];

        return view('client_portal/weekly_schedule_index', $data);
    }

    /**
     * View specific weekly schedule
     */
    public function viewWeeklySchedule($id)
    {
        $clientId = $this->getClientId();

        if (!$clientId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client information not found.');
        }

        // Get schedule
        $schedule = $this->db->table('client_weekly_schedules')
            ->where('id', $id)
            ->where('client_id', $clientId)
            ->where('status', 'published')
            ->get()
            ->getRowArray();

        if (!$schedule) {
            return redirect()->to(base_url('my-weekly-schedule'))->with('error', 'Schedule not found or access denied.');
        }

        // Get client details
        $client = $this->clientModel->find($clientId);

        // Decode JSON data
        $departments = json_decode($schedule['department_columns'], true);
        $scheduleData = json_decode($schedule['schedule_data'], true);

        $data = [
            'title' => 'View Weekly Schedule',
            'schedule' => $schedule,
            'client' => $client,
            'departments' => $departments,
            'scheduleData' => $scheduleData
        ];

        return view('client_portal/weekly_schedule_view', $data);
    }


    /**
     * Client Maintenance - List all project details
     */
    public function clientMaintenance()
    {
        $clientId = $this->getClientId();

        if (!$clientId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client information not found.');
        }

        // Get client details
        $client = $this->clientModel->find($clientId);

        if (!$client) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client not found.');
        }

        // Get all maintenance records for this client - FIXED TABLE NAME
        $records = $this->db->table('maintenance')
            ->where('client_id', $clientId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Project Details',
            'client' => $client,
            'records' => $records
        ];

        return view('client_portal/project_details', $data);
    }

    /**
     * View single project detail
     */
    public function viewProjectDetail($id)
    {
        $clientId = $this->getClientId();

        if (!$clientId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client information not found.');
        }

        // Get maintenance record - FIXED TABLE NAME
        $record = $this->db->table('maintenance')
            ->where('id', $id)
            ->where('client_id', $clientId)
            ->get()
            ->getRowArray();

        if (!$record) {
            return redirect()->to(base_url('client-maintenance'))->with('error', 'Project detail not found or access denied.');
        }

        // Get client details
        $client = $this->clientModel->find($clientId);

        $data = [
            'title' => 'View Project Details',
            'record' => $record,
            'client' => $client
        ];

        return view('client_portal/view_project_detail', $data);
    }

    /**
     * Download maintenance file
     */
    public function downloadMaintenanceFile($id, $filename)
    {
        $clientId = $this->getClientId();

        if (!$clientId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client information not found.');
        }

        // Verify the record belongs to this client - FIXED TABLE NAME
        $record = $this->db->table('maintenance')
            ->where('id', $id)
            ->where('client_id', $clientId)
            ->get()
            ->getRowArray();

        if (!$record) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $filePath = FCPATH . 'uploads/maintenance/' . $filename;

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return $this->response->download($filePath, null)->setFileName($filename);
    }
}

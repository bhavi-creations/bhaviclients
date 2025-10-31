<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\ClientWeeklySchedule.php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClientWeeklyScheduleModel;
use App\Models\ClientModel;

class ClientWeeklySchedule extends BaseController
{
    protected $scheduleModel;
    protected $clientModel;
    protected $validation;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->scheduleModel = new ClientWeeklyScheduleModel();
        $this->clientModel = new ClientModel();
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }

    /**
     * Check admin/admin manager access
     */
    private function checkAdminAccess()
    {
        if (!in_array(session()->get('role_id'), [1, 5])) {
            header('Location: ' . base_url('dashboard'));
            exit;
        }
    }

    /**
     * Display all schedules (Admin/Admin Manager)
     */
    /**
     * Display all clients with schedule count
     */
    /**
     * Display only clients who have weekly schedules
     */
    public function index()
    {
        $this->checkAdminAccess();

        // Get ONLY clients who have at least one schedule
        $clients = $this->db->query("
        SELECT 
            c.id,
            c.name,
            c.email,
            c.phone,
            COUNT(cws.id) as schedule_count,
            MAX(cws.created_at) as latest_schedule
        FROM clients c
        INNER JOIN client_weekly_schedules cws ON c.id = cws.client_id
        GROUP BY c.id
        HAVING schedule_count > 0
        ORDER BY latest_schedule DESC
    ")->getResultArray();

        $data = [
            'title' => 'Client Weekly Schedules',
            'clients' => $clients
        ];

        return view('client_weekly_schedule/index', $data);
    }


    /**
     * Show schedules for a specific client
     */
    public function clientSchedules($clientId)
    {
        $this->checkAdminAccess();

        $client = $this->clientModel->find($clientId);

        if (!$client) {
            session()->setFlashdata('error', 'Client not found.');
            return redirect()->to(base_url('weekly-schedule'));
        }

        $schedules = $this->scheduleModel->getClientSchedules($clientId);

        $data = [
            'title' => 'Schedules for ' . $client['name'],
            'client' => $client,
            'schedules' => $schedules
        ];

        return view('client_weekly_schedule/client_schedules', $data);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->checkAdminAccess();

        $clients = $this->clientModel->findAll();

        // Default department columns
        $defaultDepartments = [
            'Website Development',
            'Design',
            'Social Media',
            'SEO',
            'Marketing',
            'Content Writing',
            'Support'
        ];

        $data = [
            'title' => 'Create Weekly Schedule',
            'clients' => $clients,
            'defaultDepartments' => $defaultDepartments,
            'validation' => $this->validation
        ];

        return view('client_weekly_schedule/create', $data);
    }

    /**
     * Store new schedule
     */
    public function store()
    {
        $this->checkAdminAccess();

        $rules = [
            'client_id' => 'required|integer',
            'week_start_date' => 'required',
            'status' => 'required|in_list[draft,published,archived]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validation)
                ->with('error', 'Validation failed. Please check the form.');
        }

        $input = $this->request->getPost();

        // Calculate week end date (6 days after start)
        $weekStart = new \DateTime($input['week_start_date']);
        $weekEnd = clone $weekStart;
        $weekEnd->modify('+6 days');

        // Check if schedule already exists
        if ($this->scheduleModel->scheduleExists($input['client_id'], $input['week_start_date'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A schedule already exists for this client and week.');
        }

        $this->db->transStart();

        try {
            // Get department columns
            $departments = [];
            for ($i = 0; $i < 7; $i++) {
                if (!empty($input['dept_' . $i])) {
                    $departments[] = trim($input['dept_' . $i]);
                }
            }

            // Build schedule data array
            $scheduleData = [];
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

            foreach ($days as $dayIndex => $day) {
                $scheduleData[$day] = [];
                foreach ($departments as $deptIndex => $dept) {
                    $fieldName = 'task_' . $dayIndex . '_' . $deptIndex;
                    $scheduleData[$day][$dept] = $input[$fieldName] ?? '';
                }
            }

            // Create schedule
            $scheduleRecord = [
                'client_id' => $input['client_id'],
                'week_start_date' => $weekStart->format('Y-m-d'),
                'week_end_date' => $weekEnd->format('Y-m-d'),
                'department_columns' => json_encode($departments),
                'schedule_data' => json_encode($scheduleData),
                'status' => $input['status'],
                'remarks' => !empty($input['remarks']) ? trim($input['remarks']) : null,
                'created_by' => session()->get('id'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->scheduleModel->insert($scheduleRecord);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed.');
            }

            session()->setFlashdata('success', 'Weekly schedule created successfully!');
            return redirect()->to(base_url('weekly-schedule'));
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Schedule Creation Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to create schedule: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * View schedule details
     */
    public function view($id)
    {
        $schedule = $this->scheduleModel->find($id);

        if (!$schedule) {
            session()->setFlashdata('error', 'Schedule not found.');
            return redirect()->to(base_url('weekly-schedule'));
        }

        // Check access
        $userRole = session()->get('role_id');
        $userId = session()->get('id');

        // Admin and Admin Manager can view all
        if (in_array($userRole, [1, 5])) {
            // Allow access
        }
        // Clients can only view their own published schedules
        elseif (in_array($userRole, [3, 4])) {
            $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
            if ($user['client_id'] != $schedule['client_id'] || $schedule['status'] != 'published') {
                session()->setFlashdata('error', 'Access denied.');
                return redirect()->to(base_url('dashboard'));
            }
        } else {
            session()->setFlashdata('error', 'Access denied.');
            return redirect()->to(base_url('dashboard'));
        }

        $client = $this->clientModel->find($schedule['client_id']);
        $departments = json_decode($schedule['department_columns'], true);
        $scheduleData = json_decode($schedule['schedule_data'], true);

        $data = [
            'title' => 'Weekly Schedule Details',
            'schedule' => $schedule,
            'client' => $client,
            'departments' => $departments,
            'scheduleData' => $scheduleData
        ];

        return view('client_weekly_schedule/view', $data);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $this->checkAdminAccess();

        $schedule = $this->scheduleModel->find($id);

        if (!$schedule) {
            session()->setFlashdata('error', 'Schedule not found.');
            return redirect()->to(base_url('weekly-schedule'));
        }

        $clients = $this->clientModel->findAll();
        $departments = json_decode($schedule['department_columns'], true);
        $scheduleData = json_decode($schedule['schedule_data'], true);

        $data = [
            'title' => 'Edit Weekly Schedule',
            'schedule' => $schedule,
            'clients' => $clients,
            'departments' => $departments,
            'scheduleData' => $scheduleData,
            'validation' => $this->validation
        ];

        return view('client_weekly_schedule/edit', $data);
    }

    /**
     * Update schedule
     */
    public function update($id)
    {
        $this->checkAdminAccess();

        $schedule = $this->scheduleModel->find($id);

        if (!$schedule) {
            session()->setFlashdata('error', 'Schedule not found.');
            return redirect()->to(base_url('weekly-schedule'));
        }

        $rules = [
            'client_id' => 'required|integer',
            'week_start_date' => 'required',
            'status' => 'required|in_list[draft,published,archived]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validation)
                ->with('error', 'Validation failed.');
        }

        $input = $this->request->getPost();

        // Calculate week end date
        $weekStart = new \DateTime($input['week_start_date']);
        $weekEnd = clone $weekStart;
        $weekEnd->modify('+6 days');

        // Check if another schedule exists for same week
        if ($this->scheduleModel->scheduleExists($input['client_id'], $input['week_start_date'], $id)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Another schedule already exists for this week.');
        }

        $this->db->transStart();

        try {
            // Get department columns
            $departments = [];
            for ($i = 0; $i < 7; $i++) {
                if (!empty($input['dept_' . $i])) {
                    $departments[] = trim($input['dept_' . $i]);
                }
            }

            // Build schedule data
            $scheduleData = [];
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

            foreach ($days as $dayIndex => $day) {
                $scheduleData[$day] = [];
                foreach ($departments as $deptIndex => $dept) {
                    $fieldName = 'task_' . $dayIndex . '_' . $deptIndex;
                    $scheduleData[$day][$dept] = $input[$fieldName] ?? '';
                }
            }

            // Update schedule
            $updateData = [
                'client_id' => $input['client_id'],
                'week_start_date' => $weekStart->format('Y-m-d'),
                'week_end_date' => $weekEnd->format('Y-m-d'),
                'department_columns' => json_encode($departments),
                'schedule_data' => json_encode($scheduleData),
                'status' => $input['status'],
                'remarks' => !empty($input['remarks']) ? trim($input['remarks']) : null
            ];

            $this->scheduleModel->update($id, $updateData);

            $this->db->transComplete();

            session()->setFlashdata('success', 'Schedule updated successfully!');
            return redirect()->to(base_url('weekly-schedule'));
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Schedule Update Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to update schedule.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Delete schedule
     */
    public function delete($id)
    {
        $this->checkAdminAccess();

        $schedule = $this->scheduleModel->find($id);

        if (!$schedule) {
            session()->setFlashdata('error', 'Schedule not found.');
            return redirect()->to(base_url('weekly-schedule'));
        }

        $this->scheduleModel->delete($id);

        session()->setFlashdata('success', 'Schedule deleted successfully.');
        return redirect()->to(base_url('weekly-schedule'));
    }

    /**
     * Client view - Show their current/latest schedule
     */
    public function clientView()
    {
        $userRole = session()->get('role_id');
        $userId = session()->get('id');

        // Only clients can access this
        if (!in_array($userRole, [3, 4])) {
            session()->setFlashdata('error', 'Access denied.');
            return redirect()->to(base_url('dashboard'));
        }

        // Get client ID from user
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        $clientId = $user['client_id'] ?? null;

        if (!$clientId) {
            session()->setFlashdata('error', 'No client associated with your account.');
            return redirect()->to(base_url('client-dashboard'));
        }

        $schedule = $this->scheduleModel->getCurrentSchedule($clientId);

        if (!$schedule) {
            $data = [
                'title' => 'My Weekly Schedule',
                'schedule' => null
            ];
            return view('client_weekly_schedule/client_view', $data);
        }

        $client = $this->clientModel->find($schedule['client_id']);
        $departments = json_decode($schedule['department_columns'], true);
        $scheduleData = json_decode($schedule['schedule_data'], true);

        $data = [
            'title' => 'My Weekly Schedule',
            'schedule' => $schedule,
            'client' => $client,
            'departments' => $departments,
            'scheduleData' => $scheduleData
        ];

        return view('client_weekly_schedule/client_view', $data);
    }
}

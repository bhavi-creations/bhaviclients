<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\HolidayManagement.php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\HolidayModel;
use App\Models\NotificationModel;



class HolidayManagement extends Controller
{
    protected $holidayModel;
    protected $notificationModel;
    protected $db;

    public function __construct()
    {
        $this->holidayModel = new HolidayModel();
        $this->notificationModel = new NotificationModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);

        // Access control: Only admins (role_id = 1, 5)
        if (!in_array(session()->get('role_id'), [1, 5])) {
            header('Location: ' . base_url('dashboard'));
            exit;
        }
    }

    /**
     * List all holidays
     */
    public function index()
    {
        $holidays = $this->holidayModel->getAllHolidaysWithCreator();

        return view('holiday_management/index', [
            'title' => 'Holiday Management',
            'holidays' => $holidays
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('holiday_management/create', [
            'title' => 'Add New Holiday',
            'validation' => \Config\Services::validation()
        ]);
    }

    /**
     * Store new holiday
     */
    public function store()
    {
        $input = $this->request->getPost();

        $rules = [
            'holiday_name' => 'required|min_length[3]|max_length[255]',
            'holiday_date' => 'required|valid_date',
            'description' => 'permit_empty|max_length[500]',
            'is_recurring' => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validator);
        }

        $holidayData = [
            'holiday_name' => $input['holiday_name'],
            'holiday_date' => $input['holiday_date'],
            'description' => $input['description'] ?? null,
            'is_recurring' => $input['is_recurring'] ?? 0,
            'created_by' => session()->get('user_id')
        ];

        if ($this->holidayModel->insert($holidayData)) {
            // Get the inserted holiday ID
            $holidayId = $this->holidayModel->getInsertID();

            // SEND NOTIFICATIONS to all employees, clients, and client managers
            $this->notificationModel->notifyHoliday($holidayId, $input['holiday_name'], $input['holiday_date']);

            return redirect()->to(base_url('holidays'))
                ->with('message', 'Holiday added successfully and notifications sent!');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add holiday.');
        }
    }


    /**
     * Show edit form
     */
    public function edit($holidayId)
    {
        $holiday = $this->holidayModel->find($holidayId);

        if (!$holiday) {
            return redirect()->to(base_url('holidays'))
                ->with('error', 'Holiday not found.');
        }

        return view('holiday_management/edit', [
            'title' => 'Edit Holiday',
            'holiday' => $holiday,
            'validation' => \Config\Services::validation()
        ]);
    }

    /**
     * Update holiday
     */
    public function update($holidayId)
    {
        $holiday = $this->holidayModel->find($holidayId);
        if (!$holiday) {
            return redirect()->to(base_url('holidays'))
                ->with('error', 'Holiday not found.');
        }

        $input = $this->request->getPost();

        $rules = [
            'holiday_name' => 'required|min_length[3]|max_length[255]',
            'holiday_date' => 'required|valid_date',
            'description' => 'permit_empty|max_length[500]',
            'is_recurring' => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validator);
        }

        $updateData = [
            'holiday_name' => $input['holiday_name'],
            'holiday_date' => $input['holiday_date'],
            'description' => $input['description'] ?? null,
            'is_recurring' => $input['is_recurring'] ?? 0
        ];

        if ($this->holidayModel->update($holidayId, $updateData)) {
            return redirect()->to(base_url('holidays'))
                ->with('message', 'Holiday updated successfully!');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update holiday.');
        }
    }

    /**
     * Delete holiday
     */
    public function delete($holidayId)
    {
        $holiday = $this->holidayModel->find($holidayId);

        if (!$holiday) {
            return redirect()->to(base_url('holidays'))
                ->with('error', 'Holiday not found.');
        }

        // Delete the holiday
        if ($this->holidayModel->delete($holidayId)) {

            // DELETE RELATED NOTIFICATIONS
            $this->db->table('notifications')
                ->where('related_type', 'holiday')
                ->where('related_id', $holidayId)
                ->delete();

            return redirect()->to(base_url('holidays'))
                ->with('message', 'Holiday and related notifications deleted successfully!');
        } else {
            return redirect()->back()
                ->with('error', 'Failed to delete holiday.');
        }
    }
}

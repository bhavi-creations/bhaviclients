<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\EmployeeLeave.php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\LeaveRequestModel;

class EmployeeLeave extends Controller
{
    protected $leaveModel;

    public function __construct()
    {
        $this->leaveModel = new LeaveRequestModel();
        helper(['form', 'url']);

        // Access control: Only employees (role_id = 2)
        if (session()->get('role_id') != 2) {
            header('Location: ' . base_url('dashboard'));
            exit;
        }
    }

    /**
     * View employee's own leave requests
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $leaves = $this->leaveModel->getEmployeeLeaves($userId);

        return view('employee/leave/index', [
            'title' => 'My Leave Requests',
            'leaves' => $leaves
        ]);
    }

    /**
     * Apply for leave form
     */
    public function apply()
    {
        return view('employee/leave/apply', [
            'title' => 'Apply for Leave',
            'validation' => \Config\Services::validation()
        ]);
    }

    /**
     * Store leave request
     */
    public function store()
    {
        $input = $this->request->getPost();

        $rules = [
            'leave_type' => 'required|in_list[sick,casual,earned,unpaid,emergency]',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date',
            'reason' => 'required|min_length[10]|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validator)
                ->with('error', 'Validation failed.');
        }

        // Calculate total days
        $startDate = new \DateTime($input['start_date']);
        $endDate = new \DateTime($input['end_date']);
        $interval = $startDate->diff($endDate);
        $totalDays = $interval->days + 1;

        // Validate dates
        if ($endDate < $startDate) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'End date must be after start date.');
        }

        $leaveData = [
            'employee_id' => session()->get('user_id'),
            'leave_type' => $input['leave_type'],
            'start_date' => $input['start_date'],
            'end_date' => $input['end_date'],
            'total_days' => $totalDays,
            'reason' => $input['reason'],
            'status' => 'pending'
        ];

        if ($this->leaveModel->insert($leaveData)) {
            // CREATE NOTIFICATION FOR ADMINS
            $notificationModel = new \App\Models\NotificationModel();
            $employeeName = session()->get('first_name') . ' ' . session()->get('last_name');
            $leaveId = $this->leaveModel->getInsertID();
            $notificationModel->notifyLeaveRequest($leaveId, $employeeName);

            return redirect()->to(base_url('my-leaves'))
                ->with('message', 'Leave request submitted successfully!');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit leave request.');
        }
    }


    /**
     * Edit leave request (only pending)
     */
    public function edit($leaveId)
    {
        $userId = session()->get('user_id');
        $leave = $this->leaveModel->find($leaveId);

        if (!$leave || $leave['employee_id'] != $userId) {
            return redirect()->to(base_url('my-leaves'))
                ->with('error', 'Leave request not found.');
        }

        if ($leave['status'] != 'pending') {
            return redirect()->to(base_url('my-leaves'))
                ->with('error', 'Only pending leaves can be edited.');
        }

        return view('employee/leave/edit', [
            'title' => 'Edit Leave Request',
            'leave' => $leave,
            'validation' => \Config\Services::validation()
        ]);
    }

    /**
     * Update leave request
     */
    public function update($leaveId)
    {
        $userId = session()->get('user_id');
        $leave = $this->leaveModel->find($leaveId);

        if (!$leave || $leave['employee_id'] != $userId || $leave['status'] != 'pending') {
            return redirect()->to(base_url('my-leaves'))
                ->with('error', 'Cannot update this leave request.');
        }

        $input = $this->request->getPost();

        $rules = [
            'leave_type' => 'required|in_list[sick,casual,earned,unpaid,emergency]',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date',
            'reason' => 'required|min_length[10]|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validation failed.');
        }

        // Calculate total days
        $startDate = new \DateTime($input['start_date']);
        $endDate = new \DateTime($input['end_date']);
        $totalDays = $startDate->diff($endDate)->days + 1;

        if ($endDate < $startDate) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'End date must be after start date.');
        }

        $updateData = [
            'leave_type' => $input['leave_type'],
            'start_date' => $input['start_date'],
            'end_date' => $input['end_date'],
            'total_days' => $totalDays,
            'reason' => $input['reason']
        ];

        if ($this->leaveModel->update($leaveId, $updateData)) {
            return redirect()->to(base_url('my-leaves'))
                ->with('message', 'Leave request updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to update leave request.');
        }
    }

    /**
     * Delete leave request (only pending)
     */
    public function delete($leaveId)
    {
        $userId = session()->get('user_id');
        $leave = $this->leaveModel->find($leaveId);

        if (!$leave || $leave['employee_id'] != $userId) {
            return redirect()->back()->with('error', 'Leave request not found.');
        }

        if ($leave['status'] != 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending leaves can be deleted.');
        }

        if ($this->leaveModel->delete($leaveId)) {
            return redirect()->back()->with('message', 'Leave request deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to delete leave request.');
        }
    }

    /**
     * View specific leave details
     */
    public function view($leaveId)
    {
        $userId = session()->get('user_id');
        $leave = $this->leaveModel->find($leaveId);

        if (!$leave || $leave['employee_id'] != $userId) {
            return redirect()->to(base_url('my-leaves'))
                ->with('error', 'Leave request not found.');
        }

        return view('employee/leave/view', [
            'title' => 'Leave Request Details',
            'leave' => $leave
        ]);
    }
}

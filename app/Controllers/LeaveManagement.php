<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\LeaveManagement.php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\LeaveRequestModel;
use App\Models\EmployeeModel;

class LeaveManagement extends Controller
{
    protected $leaveModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->leaveModel = new LeaveRequestModel();
        $this->employeeModel = new EmployeeModel();
        helper(['form', 'url']);

        // Access control: Only admins (role_id = 1, 5)
        if (!in_array(session()->get('role_id'), [1, 5])) {
            header('Location: ' . base_url('dashboard'));
            exit;
        }
    }

    /**
     * List employees with leave statistics
     */
    public function index()
    {
        $employees = $this->leaveModel->getEmployeesWithLeaveStats();
        $pendingCount = $this->leaveModel->getPendingCount();

        return view('leave_management/index', [
            'title' => 'Leave Management',
            'employees' => $employees,
            'pendingCount' => $pendingCount
        ]);
    }

    /**
     * View specific employee's leave requests
     */
    public function employeeLeaves($employeeId)
    {
        // Get employee with user details
        $employee = $this->employeeModel->select('employees.*, 
                                             users.first_name, 
                                             users.last_name,
                                             users.email,
                                             departments.name as department_name')
            ->join('users', 'users.id = employees.user_id', 'left')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->where('employees.user_id', $employeeId)
            ->first();

        if (!$employee) {
            return redirect()->to(base_url('leave-management'))
                ->with('error', 'Employee not found.');
        }

        $leaves = $this->leaveModel->getEmployeeLeaves($employeeId);

        return view('leave_management/employee_leaves', [
            'title' => 'Leave Requests - ' . $employee['first_name'] . ' ' . $employee['last_name'],
            'employee' => $employee,
            'leaves' => $leaves
        ]);
    }


    /**
     * View specific leave request details
     */
    public function viewLeave($leaveId)
    {
        $leave = $this->leaveModel->select('leave_requests.*, 
                                       users.first_name, 
                                       users.last_name,
                                       users.email,
                                       employees.employee_code,
                                       departments.name as department_name,
                                       approver.first_name as approver_first_name,
                                       approver.last_name as approver_last_name')
            ->join('users', 'users.id = leave_requests.employee_id', 'left')
            ->join('employees', 'employees.user_id = leave_requests.employee_id', 'left')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->join('users as approver', 'approver.id = leave_requests.approved_by', 'left')
            ->find($leaveId);

        if (!$leave) {
            return redirect()->back()->with('error', 'Leave request not found.');
        }

        // Add combined employee name
        $leave['employee_name'] = $leave['first_name'] . ' ' . $leave['last_name'];

        return view('leave_management/view_leave', [
            'title' => 'Leave Request Details',
            'leave' => $leave
        ]);
    }


    /**
     * Update leave status (approve/reject)
     */
    public function updateStatus($leaveId)
    {
        $input = $this->request->getPost();

        $rules = [
            'status' => 'required|in_list[pending,approved,rejected]',
            'admin_remarks' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Invalid input.');
        }

        $leave = $this->leaveModel->find($leaveId);
        if (!$leave) {
            return redirect()->back()->with('error', 'Leave request not found.');
        }

        $updateData = [
            'status' => $input['status'],
            'admin_remarks' => $input['admin_remarks'] ?? null,
            'approved_by' => session()->get('user_id'),
            'approved_at' => date('Y-m-d H:i:s')
        ];

        if ($this->leaveModel->update($leaveId, $updateData)) {
            // CREATE NOTIFICATION FOR EMPLOYEE (only if approved or rejected)
            if (in_array($input['status'], ['approved', 'rejected'])) {
                $notificationModel = new \App\Models\NotificationModel();
                $adminName = session()->get('first_name') . ' ' . session()->get('last_name');
                $notificationModel->notifyLeaveStatus($leave['employee_id'], $input['status'], $adminName);
            }

            $statusText = ucfirst($input['status']);
            return redirect()->back()
                ->with('message', "Leave request {$statusText} successfully!");
        } else {
            return redirect()->back()->with('error', 'Failed to update status.');
        }
    }


    /**
     * Delete leave request
     */
    public function delete($leaveId)
    {
        if ($this->leaveModel->delete($leaveId)) {
            return redirect()->back()->with('message', 'Leave request deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to delete leave request.');
        }
    }
}

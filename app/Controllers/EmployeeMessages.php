<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeeMessageModel;
use App\Models\DepartmentModel;
use App\Models\UserModel;

class EmployeeMessages extends BaseController
{
    protected $msgModel, $deptModel, $userModel, $session;

    public function __construct()
    {
        $this->msgModel = new EmployeeMessageModel();
        $this->deptModel = new DepartmentModel();
        $this->userModel = new UserModel();
        $this->session   = \Config\Services::session();
    }

    // Admin/Manager - Send message/create page
    public function create()
    {
        // fetch all departments and employees
        $departments = $this->deptModel->findAll();
        $employees = $this->userModel->where('role_id', 2)->findAll();

        return view('employee_messages/create', [
            'title' => 'Send Employee Message',
            'departments' => $departments,
            'employees' => $employees
        ]);
    }

    public function store()
    {
        $input = $this->request->getPost();

        $data = [
            'department_id' => $input['department_id'],
            'employee_id'   => $input['employee_id'],
            'sender_id'     => $this->session->get('user_id'),
            'sender_role_id' => $this->session->get('role_id'),
            'message'       => $input['message'],
            'created_at'    => date('Y-m-d H:i:s')
        ];

        $this->msgModel->insert($data);
        return redirect()->to(base_url('employee-messages/list'))->with('success', 'Message sent!');
    }

    // Admin/Manager - List with filters + actions
    public function index()
    {
        // Default fetch & send all filterable data
        $departments = $this->deptModel->findAll();
        $departmentId = $this->request->getGet('department_id');
        $employeeId = $this->request->getGet('employee_id');

        $messagesQuery = $this->msgModel->select('employee_messages.*, users.first_name, users.last_name, departments.name as department_name')
            ->join('users', 'users.id = employee_messages.employee_id', 'left')
            ->join('departments', 'departments.id = employee_messages.department_id', 'left');

        if ($departmentId) $messagesQuery->where('employee_messages.department_id', $departmentId);
        if ($employeeId) $messagesQuery->where('employee_messages.employee_id', $employeeId);

        $messages = $messagesQuery->orderBy('employee_messages.created_at', 'desc')->findAll();
        $employees = $departmentId ? $this->userModel->where('department_id', $departmentId)->where('role_id', 2)->findAll() : [];

        return view('employee_messages/index', [
            'title' => 'Messages to Employees',
            'messages' => $messages,
            'departments' => $departments,
            'employees' => $employees,
            'departmentId' => $departmentId,
            'employeeId' => $employeeId,
        ]);
    }

    // Edit and Delete actions
    public function edit($id)
    {
        $msg = $this->msgModel->find($id);

        if (!$msg) return redirect()->to(base_url('employee-messages/list'))->with('error', 'Message not found!');
        $departments = $this->deptModel->findAll();
        $employees = $this->userModel->where('department_id', $msg['department_id'])->where('role_id', 2)->findAll();

        return view('employee_messages/edit', [
            'title' => 'Edit Message',
            'msg' => $msg,
            'departments' => $departments,
            'employees' => $employees
        ]);
    }

    public function update($id)
    {
        $input = $this->request->getPost();
        $this->msgModel->update($id, [
            'department_id' => $input['department_id'],
            'employee_id' => $input['employee_id'],
            'message' => $input['message']
        ]);
        return redirect()->to(base_url('employee-messages/list'))->with('success', 'Message updated!');
    }

    public function delete($id)
    {
        $this->msgModel->delete($id);
        return redirect()->to(base_url('employee-messages/list'))->with('success', 'Message deleted!');
    }

    public function myMessages()
    {
        $employeeId = $this->session->get('user_id');
        $fromDate = $this->request->getGet('from_date');
        $toDate   = $this->request->getGet('to_date');

        $messagesQuery = $this->msgModel->where('employee_id', $employeeId);

        if ($fromDate) {
            $messagesQuery->where('created_at >=', $fromDate . ' 00:00:00');
        }
        if ($toDate) {
            $messagesQuery->where('created_at <=', $toDate . ' 23:59:59');
        }

        $msgs = $messagesQuery->orderBy('created_at', 'desc')->findAll();

        return view('employee_messages/employee_view', [
            'title' => 'Messages from Admin',
            'messages' => $msgs,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);
    }
}

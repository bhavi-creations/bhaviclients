<?php 
// C:\xampp\htdocs\bhaviclients\app\Controllers\Work.php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeeTaskModel;
use App\Models\ClientModel; // <-- NEW: Import Client Model
use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;

class Work extends BaseController
{
    protected $employeeTaskModel;
    protected $clientModel; // <-- NEW: Property for Client Model
    protected $session;
    protected $employeeId; // To hold the logged-in employee's ID

    public function __construct()
    {
        // Initialize Models and Services
        $this->employeeTaskModel = new EmployeeTaskModel();
        $this->clientModel = new ClientModel(); // <-- NEW: Initialize Client Model
        $this->session = \Config\Services::session();

        // Ensure the employee is logged in and set the ID. 
        // This is crucial for filtering data.
        $this->employeeId = $this->session->get('employee_id');

        // Check for logged-in employee in the controller's logic 
        // as a secondary safety measure (primary is the AuthFilter).
        if (empty($this->employeeId)) {
            // If somehow the filter failed, redirect to login
            return redirect()->to(base_url('login'));
        }

        helper(['form', 'url']);
    }

    /**
     * Display a list of tasks submitted by the logged-in employee.
     */
    public function myTasks()
    {
        $data = [
            'title' => 'My Work Submissions',
            // Fetch tasks only belonging to the logged-in employee
            'tasks' => $this->employeeTaskModel
                                ->where('employee_id', $this->employeeId)
                                // Optional: You might want to join the clients table 
                                // here to display the client name.
                                ->findAll(),
        ];
        return view('work/mytasks', $data);
    }

    /**
     * Display the new task creation form.
     */
    public function create()
    {
        // Fetch all clients to populate the dropdown
        $clients = $this->clientModel->findAll();

        $data = [
            'title' => 'Submit New Work/Task',
            'clients' => $clients, // <-- NEW: Pass clients to the view
            'validation' => \Config\Services::validation(),
        ];
        return view('work/create', $data);
    }

    /**
     * Handle the submission of a new task.
     */
    public function store()
    {
        // 1. Define Validation Rules (NEW: Added client_id)
        $rules = [
            'client_id'   => 'required|integer|is_not_unique[clients.id]', // Must select a valid client
            'title'       => 'required|min_length[5]|max_length[255]',
            'description' => 'required|min_length[10]',
            'due_date'    => 'permit_empty|valid_date',
            'status'      => 'required|in_list[Pending,In Progress,Completed,Review]',
        ];

        if (!$this->validate($rules)) {
            // Validation failed, redirect back to create form with errors
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // 2. Prepare Data for Saving
        $data = [
            'employee_id' => $this->employeeId, // Automatically assigned from session
            'client_id'   => $this->request->getPost('client_id'), // <-- NEW: Get client ID
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'due_date'    => $this->request->getPost('due_date'),
            'status'      => $this->request->getPost('status') ?? 'Pending',
        ];

        // 3. Save Data
        if ($this->employeeTaskModel->save($data)) {
            session()->setFlashdata('message', 'Work submission created successfully.');
            return redirect()->to(base_url('work/mytasks'));
        } else {
            session()->setFlashdata('error', 'Could not save the work submission due to an internal error.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the task edit form.
     */
    public function edit(int $id)
    {
        $task = $this->employeeTaskModel->find($id);

        // Security Check: Does the task exist AND does it belong to the logged-in employee?
        if (empty($task) || $task['employee_id'] != $this->employeeId) {
            throw new PageNotFoundException('Cannot find the work submission or you do not have permission to edit it.');
        }

        // Fetch all clients (needed in the edit form for changing clients if required)
        $clients = $this->clientModel->findAll();

        $data = [
            'title' => 'Edit Work Submission',
            'task'  => $task,
            'clients' => $clients, // <-- NEW: Pass clients to the view
            'validation' => \Config\Services::validation(),
        ];
        return view('work/edit', $data);
    }

    /**
     * Handle the update submission of an existing task.
     */
    public function update(int $id)
    {
        // 1. Initial Security Check: Does the task belong to the employee?
        $task = $this->employeeTaskModel->find($id);
        if (empty($task) || $task['employee_id'] != $this->employeeId) {
            session()->setFlashdata('error', 'Access denied: You cannot update this submission.');
            return redirect()->to(base_url('work/mytasks'));
        }

        // 2. Define Validation Rules (NEW: Added client_id, removed unique constraint for update)
        $rules = [
            'client_id'   => 'required|integer|is_not_unique[clients.id]',
            'title'       => 'required|min_length[5]|max_length[255]',
            'description' => 'required|min_length[10]',
            'due_date'    => 'permit_empty|valid_date',
            'status'      => 'required|in_list[Pending,In Progress,Completed,Review]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // 3. Prepare Data for Update
        $data = [
            'id'          => $id, // ID must be included for update
            'client_id'   => $this->request->getPost('client_id'), // <-- NEW: Get client ID
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'due_date'    => $this->request->getPost('due_date'),
            'status'      => $this->request->getPost('status'),
        ];

        // 4. Update Data
        if ($this->employeeTaskModel->save($data)) {
            session()->setFlashdata('message', 'Work submission updated successfully.');
            return redirect()->to(base_url('work/mytasks'));
        } else {
            session()->setFlashdata('error', 'Could not update the work submission.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Handle the deletion of a task.
     */
    public function delete(int $id)
    {
        // 1. Initial Security Check: Does the task belong to the employee?
        $task = $this->employeeTaskModel->find($id);
        if (empty($task) || $task['employee_id'] != $this->employeeId) {
            session()->setFlashdata('error', 'Access denied: You cannot delete this submission.');
            return redirect()->to(base_url('work/mytasks'));
        }

        // 2. Delete Data
        if ($this->employeeTaskModel->delete($id)) {
            session()->setFlashdata('message', 'Work submission deleted successfully.');
        } else {
            session()->setFlashdata('error', 'Could not delete the work submission.');
        }

        return redirect()->to(base_url('work/mytasks'));
    }
}

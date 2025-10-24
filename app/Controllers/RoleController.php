<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RoleModel;

class RoleController extends BaseController
{
    protected $roleModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        // Initialize the RoleModel
        $this->roleModel = new RoleModel();
    }

    // Displays the list of all roles
    public function index()
    {
        $data = [
            'title' => 'Manage Roles',
            'roles' => $this->roleModel->findAll(),
            'session' => session(),
        ];
        return view('roles/index', $data);
    }

    // Displays the role creation form
    public function create()
    {
        $data = [
            'title' => 'Create New Role',
            'validation' => \Config\Services::validation(),
        ];
        return view('roles/create', $data);
    }

    // Handles the submission for creating a new role
    public function store()
    {
        // Use validation rules defined in the Model
        if (!$this->validate($this->roleModel->getValidationRules())) {
            // If validation fails, redirect back with input and errors
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->roleModel->save([
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to(base_url('roles'))->with('success', 'Role created successfully.');
    }

    // Displays the role editing form
    public function edit($id)
    {
        $role = $this->roleModel->find($id);

        if (!$role) {
            return redirect()->to(base_url('roles'))->with('error', 'Role not found.');
        }

        $data = [
            'title' => 'Edit Role',
            'role' => $role,
            'validation' => \Config\Services::validation(),
        ];
        return view('roles/edit', $data);
    }

    // Handles the submission for updating an existing role
    public function update($id = null)
    {
        $roleModel = new \App\Models\RoleModel();

        // 1. Get the role to ensure it exists before proceeding
        $role = $roleModel->find($id);
        if (!$role) {
            return redirect()->to(base_url('roles'))->with('error', 'Role not found.');
        }

        // 2. Define Validation Rules
        // The 'is_unique' rule must be updated to ignore the current role ID
        $validationRules = [
            'name' => 'required|min_length[3]|max_length[100]|is_unique[roles.name,id,' . $id . ']',
            // 'permit_empty' allows the field to be submitted as an empty string, which is fine for description
            'description' => 'permit_empty|max_length[255]',
        ];

        // 3. Validate the input
        if (!$this->validate($validationRules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // 4. Prepare data for model update
        $dataToUpdate = [
            'name'          => $this->request->getPost('name'),
            'description'   => $this->request->getPost('description'), // <-- This ensures description is grabbed
        ];

        // 5. Perform the update
        $roleModel->update($id, $dataToUpdate);

        // 6. Redirect on success
        return redirect()->to(base_url('roles'))->with('success', 'Role "' . esc($dataToUpdate['name']) . '" updated successfully.');
    }


    // Handles role deletion
    public function delete($id)
    {
        if ($this->roleModel->delete($id)) {
            return redirect()->to(base_url('roles'))->with('success', 'Role deleted successfully.');
        } else {
            return redirect()->to(base_url('roles'))->with('error', 'Could not delete role.');
        }
    }
}

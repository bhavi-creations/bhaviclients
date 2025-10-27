<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\ClientModel;

class UserManagement extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $clientModel;
    protected $validation;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->clientModel = new ClientModel();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url']);
    }

    /**
     * List all users
     */
    public function index()
    {
        $users = $this->userModel
            ->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->findAll();

        $data = [
            'title' => 'User Management',
            'users' => $users
        ];

        return view('user_management/index', $data);
    }

    /**
     * Create new user
     */
    public function create()
    {
        $roles = $this->roleModel->findAll();
        $clients = $this->clientModel->findAll();

        $data = [
            'title' => 'Create User',
            'roles' => $roles,
            'clients' => $clients,
            'validation' => $this->validation
        ];

        return view('user_management/create', $data);
    }

    /**
     * Store new user
     */
    public function store()
    {
        // Base validation rules
        $rules = [
            'role_id'    => 'required|integer',
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name'  => 'required|min_length[2]|max_length[100]',
            'email'      => 'required|valid_email|is_unique[users.email]',
            'username'   => 'required|min_length[3]|is_unique[users.username]',
            'phone'      => 'required|min_length[10]|max_length[20]',
            'password'   => 'required|min_length[6]'
        ];

        $roleId = $this->request->getPost('role_id');
        if ($roleId == 4) {
            // Client manager must have a client assigned
            $rules['assigned_client'] = 'required|integer';
        }

        if (!$this->validate($rules)) {
            $errors = $this->validation->getErrors();
            log_message('error', 'User Creation Validation Failed: ' . json_encode($errors));
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validation)
                ->with('error', 'Validation failed: ' . implode(', ', $errors));
        }

        $input = $this->request->getPost();
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Prepare user data
            $userData = [
                'role_id'    => $roleId,
                'first_name' => trim($input['first_name']),
                'last_name'  => trim($input['last_name']),
                'email'      => trim($input['email']),
                'username'   => trim($input['username']),
                'phone'      => trim($input['phone']),
                'password'   => $input['password'], // UserModel should hash as needed
                'created_at' => date('Y-m-d H:i:s'),
            ];

            // For client manager, save selected client id
            if ($roleId == 4) {
                $userData['client_id'] = $input['assigned_client'];
            }

            $this->userModel->insert($userData);
            $userId = $this->userModel->getInsertID();

            if (!$userId) {
                throw new \Exception('Failed to create user.');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            session()->setFlashdata('success', 'User created successfully!');
            return redirect()->to(base_url('user-management'));
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'User Creation Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to create user: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function delete($id)
    {
        // (Optional: Additional admin-only check)
        if (session()->get('role_id') != 1) {
            return redirect()->to(base_url('user-management'))->with('error', 'Unauthorized.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(base_url('user-management'))->with('error', 'User not found.');
        }

        // (Optional: Prevent deletion of super admin or self)
        if ($id == session()->get('user_id')) {
            return redirect()->to(base_url('user-management'))->with('error', 'You cannot delete your own account.');
        }

        $this->userModel->delete($id);

        return redirect()->to(base_url('user-management'))->with('success', 'User deleted successfully.');
    }
}

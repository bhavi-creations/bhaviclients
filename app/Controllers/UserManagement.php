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

 
 
  

    public function delete($id)
    {
        if (session()->get('role_id') != 1) {
            return redirect()->to(base_url('user-management'))->with('error', 'Unauthorized.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(base_url('user-management'))->with('error', 'User not found.');
        }

        if ($id == session()->get('user_id')) {
            return redirect()->to(base_url('user-management'))->with('error', 'You cannot delete your own account.');
        }

        $this->userModel->delete($id);

        return redirect()->to(base_url('user-management'))->with('success', 'User deleted successfully.');
    }
}

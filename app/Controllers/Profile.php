<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\DepartmentModel;

class Profile extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $departmentModel;
    protected $validation;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->departmentModel = new DepartmentModel();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url']);
    }

    /**
     * Display the user's profile
     */
    public function index()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to(base_url('login'))->with('error', 'Please login first.');
        }

        // Fetch user details with role and department info
        $user = $this->userModel
            ->select('users.*, roles.name as role_name, departments.name as department_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->find($userId);

        if (!$user) {
            return redirect()->to(base_url('dashboard'))->with('error', 'User not found.');
        }

        $data = [
            'title' => 'My Profile',
            'user' => $user
        ];

        return view('profile/index', $data);
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to(base_url('login'))->with('error', 'Please login first.');
        }

        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('dashboard'))->with('error', 'User not found.');
        }

        $data = [
            'title' => 'Edit Profile',
            'user' => $user,
            'departments' => $this->departmentModel->findAll(),
            'validation' => $this->validation
        ];

        return view('profile/edit', $data);
    }

    /**
     * Update profile information
     */
    public function update()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to(base_url('login'))->with('error', 'Please login first.');
        }

        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('profile'))->with('error', 'User not found.');
        }

        // Validation rules (allow username edit)
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'username' => [
                'label' => 'Username',
                'rules' => [
                    'required',
                    'min_length[3]',
                    function ($value) use ($userId) {
                        $db = \Config\Database::connect();
                        $builder = $db->table('users');
                        $builder->where('username', $value);
                        $builder->where('id !=', $userId);
                        return $builder->countAllResults() === 0;
                    }
                ],
                'errors' => [
                    'checkUniqueExcludingId' => 'This username is already in use.'
                ]
            ],
            'phone' => [
                'label' => 'Phone',
                'rules' => [
                    'required',
                    'numeric',
                    'min_length[10]',
                    'max_length[20]',
                    function ($value) use ($userId) {
                        return $this->checkUniqueExcludingId($value, 'phone', 'users', $userId);
                    }
                ],
                'errors' => [
                    'checkUniqueExcludingId' => 'The phone number is already in use.',
                ],
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validation);
        }

        $input = $this->request->getPost();

        // Update user data
        $updateData = [
            'first_name' => trim($input['first_name']),
            'last_name'  => trim($input['last_name']),
            'username'   => trim($input['username']),
            'phone'      => trim($input['phone']),
        ];

        try {
            $this->userModel->update($userId, $updateData);

            // Update session data
            session()->set([
                'first_name' => $updateData['first_name'],
                'last_name'  => $updateData['last_name'],
                'username'   => $updateData['username']
            ]);

            session()->setFlashdata('success', 'Profile updated successfully!');
            return redirect()->to(base_url('profile'));
        } catch (\Exception $e) {
            log_message('error', 'Profile Update Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to update profile. Please try again.');
            return redirect()->back()->withInput();
        }
    }


    /**
     * Show change password form
     */
    public function changePassword()
    {
        $data = [
            'title' => 'Change Password',
            'validation' => $this->validation
        ];

        return view('profile/change_password', $data);
    }

    /**
     * Update password
     */
    public function updatePassword()
    {
        $userId = session()->get('user_id');

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('validation', $this->validation);
        }

        $user = $this->userModel->find($userId);
        $input = $this->request->getPost();

        // Verify current password
        if (!password_verify($input['current_password'], $user['password'])) {
            session()->setFlashdata('error', 'Current password is incorrect.');
            return redirect()->back();
        }

        // Update password
        try {
            $this->userModel->update($userId, [
                'password' => $input['new_password'] // Will be hashed by model
            ]);

            session()->setFlashdata('success', 'Password changed successfully!');
            return redirect()->to(base_url('profile'));
        } catch (\Exception $e) {
            log_message('error', 'Password Update Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to update password.');
            return redirect()->back();
        }
    }

    /**
     * Helper function for unique validation
     */
    protected function checkUniqueExcludingId($value, $field, $table, $ignoreId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($table);
        $builder->where($field, $value);
        $builder->where('id !=', $ignoreId);
        $result = $builder->countAllResults();

        return $result === 0;
    }


    /**
     * Show edit username form
     */
    public function editUsername()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to(base_url('login'))->with('error', 'Please login first.');
        }

        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('dashboard'))->with('error', 'User not found.');
        }

        $data = [
            'title' => 'Change Username',
            'user' => $user,
            'validation' => $this->validation
        ];

        return view('profile/edit_username', $data);
    }

    /**
     * Update username only
     */
    public function updateUsername()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return redirect()->to(base_url('login'))->with('error', 'Please login first.');
        }

        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('profile'))->with('error', 'User not found.');
        }

        // Validation rules for username
        $rules = [
            'username' => [
                'label' => 'Username',
                'rules' => [
                    'required',
                    'min_length[3]',
                    'max_length[100]',
                    function ($value) use ($userId) {
                        $db = \Config\Database::connect();
                        $builder = $db->table('users');
                        $builder->where('username', $value);
                        $builder->where('id !=', $userId);
                        return $builder->countAllResults() === 0;
                    }
                ],
                'errors' => [
                    'required' => 'Username is required.',
                    'min_length' => 'Username must be at least 3 characters.',
                    'max_length' => 'Username cannot exceed 100 characters.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'This username is already taken. Please choose another one.');
            return redirect()->back()->withInput()->with('validation', $this->validation);
        }

        $input = $this->request->getPost();

        // Update username only
        $updateData = [
            'username' => trim($input['username'])
        ];

        try {
            $this->userModel->update($userId, $updateData);

            session()->setFlashdata('success', 'Username updated successfully! Use your new username on next login.');
            return redirect()->to(base_url('profile'));
        } catch (\Exception $e) {
            log_message('error', 'Username Update Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to update username. Please try again.');
            return redirect()->back()->withInput();
        }
    }
}

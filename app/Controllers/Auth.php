<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class Auth extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Show the login page.
     */
    public function login()
    {
        // If the user is already logged in, redirect them to the dashboard.
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        $data = [
            'title' => 'Login',
            'validation' => \Config\Services::validation(),
        ];
        return view('auth/login', $data);
    }

    /**
     * Handle the form submission and authenticate the user against the database.
     */
    public function authenticate()
    {
        $session = session();
        $input = $this->request->getPost();

        // 1. Validation Rules
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput();
        }

        $username = $input['username'];
        $rawPassword = $input['password'];

        // 2. Look up user by username
        $user = $this->userModel->where('username', $username)->first();

        if ($user) {
            // 3. Verify the password hash
            if (password_verify($rawPassword, $user['password'])) {

                // 4. Check if employee is active (for role_id = 2)
                if ($user['role_id'] == 2 && !empty($user['employee_id'])) {
                    $db = \Config\Database::connect();
                    $employee = $db->table('employees')
                        ->where('id', $user['employee_id'])
                        ->get()
                        ->getRow();

                    // Block login if employee not found or inactive
                    if (!$employee) {
                        $session->setFlashdata('error', 'Employee record not found. Please contact administrator.');
                        return redirect()->back()->withInput();
                    }

                    if ($employee->status == 'inactive') {
                        $session->setFlashdata('error', 'Your account has been deactivated. Please contact administrator.');
                        return redirect()->back()->withInput();
                    }
                }

                // 5. Fetch the role name
                $db = \Config\Database::connect();
                $role = $db->table('roles')
                    ->select('name')
                    ->where('id', $user['role_id'])
                    ->get()
                    ->getRow();

                $roleName = $role ? $role->name : 'unknown';

                // Success: Set Session Data
                $ses_data = [
                    'user_id'     => $user['id'],
                    'first_name'  => $user['first_name'],
                    'last_name'   => $user['last_name'],
                    'email'       => $user['email'],
                    'role_id'     => $user['role_id'],
                    'role_name'   => $roleName,
                    'client_id'   => $user['client_id'] ?? null,       // Client-specific info
                    'employee_id' => $user['employee_id'] ?? null,     // Employee-specific info
                    'isLoggedIn'  => TRUE
                ];
                $session->set($ses_data);

                // ---- Role-based redirect ----
                if ($user['role_id'] == 1 || $user['role_id'] == 5) {
                    return redirect()->to(base_url('dashboard'));
                } elseif ($user['role_id'] == 2) {
                    return redirect()->to(base_url('employee-dashboard'));
                } elseif ($user['role_id'] == 3 || $user['role_id'] == 4) {
                    return redirect()->to(base_url('client-dashboard'));
                } else {
                    // Unknown role, fallback to login
                    return redirect()->to(base_url('login'))->with('error', 'Role not recognized');
                }

            } else {
                $session->setFlashdata('error', 'Invalid Username or Password.');
                return redirect()->back()->withInput();
            }
        }

        $session->setFlashdata('error', 'Invalid Username or Password.');
        return redirect()->back()->withInput();
    }

    /**
     * Log the user out and destroy the session.
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'))->with('message', 'You have been successfully logged out.');
    }
}

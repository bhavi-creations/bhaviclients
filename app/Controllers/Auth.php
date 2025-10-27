<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\Auth.php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel; // Import the UserModel

class Auth extends Controller
{
    protected $userModel;

    public function __construct()
    {
        // Load the UserModel
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

                // 4. Fetch the role name
                $db = \Config\Database::connect();
                $role = $db->table('roles')
                    ->select('name')
                    ->where('id', $user['role_id'])
                    ->get()
                    ->getRow();

                $roleName = $role ? $role->name : 'unknown';

                // Success: Set Session Data
                $ses_data = [
                    'user_id'    => $user['id'],
                    'first_name' => $user['first_name'],
                    'last_name'  => $user['last_name'],
                    'email'      => $user['email'],
                    'role_id'    => $user['role_id'],
                    'role_name'  => $roleName,
                    'client_id'  => $user['client_id'] ?? null,      // Add this for clients
                    'employee_id' => $user['employee_id'] ?? null,   // Add this for employees
                    'isLoggedIn' => TRUE
                ];
                $session->set($ses_data);

                // Redirect to dashboard
                return redirect()->to(base_url('dashboard'));
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

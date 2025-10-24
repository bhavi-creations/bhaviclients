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
            'email'    => 'required|valid_email',
            'password' => 'required', // We removed min_length[5] to allow 10-digit phone numbers as passwords
        ];

        if (!$this->validate($rules)) {
            // Validation failed, return to form with errors
            return redirect()->back()->withInput();
        }

        $email = $input['email'];
        $rawPassword = $input['password']; // This is the raw phone number entered by the user
        
        // 2. Look up user by email (used as username)
        $user = $this->userModel->where('email', $email)->first();

        if ($user) {
            // 3. Verify the password hash
            if (password_verify($rawPassword, $user['password'])) {
                
                // 4. FIX: Fetch the role name directly from the database
                $db = \Config\Database::connect();
                $role = $db->table('roles')
                           // FIX: Assuming the column is 'name' instead of 'role_name'
                           ->select('name')
                           ->where('id', $user['role_id'])
                           ->get()
                           ->getRow();

                // Determine the role name for the session
                // We use $role->name assuming the select was successful and the column is 'name'
                $roleName = $role ? $role->name : 'unknown'; 

                // Success: Set Session Data
                $ses_data = [
                    'user_id'    => $user['id'], 
                    'first_name' => $user['first_name'],
                    'last_name'  => $user['last_name'],
                    'email'      => $user['email'],
                    'role_id'    => $user['role_id'], 
                    'role_name'  => $roleName, // Added role_name to session
                    'isLoggedIn' => TRUE
                ];
                $session->set($ses_data);
                
                // Redirect based on role or to a default dashboard
                return redirect()->to(base_url('dashboard')); 

            } else {
                // Failure: Password does not match hash
                $session->setFlashdata('error', 'Invalid Email or Password.');
                return redirect()->back()->withInput();
            }
        } 
        
        // Failure: User not found
        $session->setFlashdata('error', 'Invalid Email or Password.');
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

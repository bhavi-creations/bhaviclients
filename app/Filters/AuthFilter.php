<?php 
// C:\xampp\htdocs\bhaviclients\app\Filters\AuthFilter.php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        // --- ADMIN BYPASS: Admin (role_id = 1) can access EVERYTHING ---
        if (session()->get('role_id') == 1) {
            // Admin has full access, skip all role checks
            return $request;
        }

        // If role-based filtering is requested for non-admin users
        if (!empty($arguments)) {
            $userRoleId = session()->get('role_id');
            // Fix: properly split comma-separated argument strings to a flat array of allowed roles
            $allowedRoles = [];
            foreach ($arguments as $arg) {
                $allowedRoles = array_merge($allowedRoles, explode(',', $arg));
            }
            $allowedRoles = array_map('trim', $allowedRoles); // Remove spaces

            if (!in_array($userRoleId, $allowedRoles)) {
                session()->setFlashdata('error', 'Access denied. You do not have permission to access this page.');
                return redirect()->to(base_url('dashboard'));
            }
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}

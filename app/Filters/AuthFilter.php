<?php 
// C:\xampp\htdocs\bhaviclients\app\Filters\AuthFilter.php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Do before the controller is executed.
     * This checks if the user is logged in and handles role separation.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // --- 1. Authentication Check ---
        if (!$session->get('isLoggedIn')) {
            // User is NOT logged in. Redirect to the login page.
            // This is the necessary step to protect routes.
            return redirect()->to(base_url('login'));
        }

        // --- 2. Role-based Check (Handles 'auth:admin' type filters) ---
        if ($arguments !== null && !empty($arguments)) {
            $requiredRole = $arguments[0]; // e.g., 'admin' from 'auth:admin'

            // Check if the user's current session role matches the required role
            if ($session->get('role') !== $requiredRole) {
                // User is logged in but does not have the required role.
                $session->setFlashdata('error', 'Access denied. You do not have the required permissions.');
                
                // Redirect them to the general dashboard, preventing them from accessing admin-only pages.
                return redirect()->to(base_url('dashboard'));
            }
        }
        
        // If logged in and role matches (or no role specified), allow access.
    }

    /**
     * Do after the controller is executed.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after controller execution for this filter
    }
}

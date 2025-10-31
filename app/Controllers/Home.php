<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RoleModel;        // Import the RoleModel

class Home extends BaseController
{
    /**
     * This is the main dashboard page, typically for admins or general users.
     */
    public function index()
    {
        $session = session();
        $roleId = $session->get('role_id');

        // 1. Get the Role Name for the view (check session first)
        $roleName = $session->get('role_name');
        if (!$roleName && $roleId) {
            // Lookup role name if missing from session
            $roleModel = new RoleModel();
            $roleName = $roleModel->getRoleNameById($roleId);
        }

        // The AuthFilter already handles the isLoggedIn check for this route.
        $data['title'] = 'Admin Dashboard';
        // Add the role name to the data array
        $data['user_role_name'] = $roleName ?? 'Guest';
        // You can fetch overall statistics here for the admin dashboard

        return view('dashboard/index', $data); // Assuming you have an admin dashboard view
    }

    /**
     * Doctor's specific dashboard.
     * Displays appointments relevant to the logged-in doctor.
     */
    

    // You can add other public methods for other roles or general pages here
}

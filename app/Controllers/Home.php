<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AppointmentModel; // Import this model
use App\Models\PatientModel;     // Import this model
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
    public function doctorDashboard()
    {
        $session = session();
        $doctor_id = $session->get('doctor_id'); // Get the logged-in doctor's ID from session
        $role_id = $session->get('role_id');
        
        // Retrieve the role name from the session or look it up
        $roleName = $session->get('role_name');
        if (!$roleName && $role_id) {
            $roleModel = new RoleModel();
            $roleName = $roleModel->getRoleNameById($role_id);
        }

        // Security Check: Ensure it's a doctor accessing this page
        if ($role_id != 2 || !$doctor_id) { // Assuming role_id 2 is for Doctors
            session()->setFlashdata('error', 'Access denied. You do not have permission to view the doctor dashboard.');
            return redirect()->to('/dashboard'); // Redirect to general dashboard or login
        }

        $appointmentModel = new AppointmentModel();

        // Fetch appointments specifically for this doctor
        $data['appointments'] = $appointmentModel
            ->select('appointments.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name')
            ->join('patients', 'appointments.patient_id = patients.id')
            ->where('appointments.doctor_id', $doctor_id) // Filter by logged-in doctor's ID
            ->orderBy('appointment_date', 'DESC')
            ->orderBy('appointment_time', 'ASC')
            ->findAll();

        $data['title'] = 'Doctor Dashboard';
        $data['doctor_name'] = $session->get('first_name') . ' ' . $session->get('last_name');
        // Add the role name to the data array for the doctor dashboard
        $data['user_role_name'] = $roleName ?? 'Doctor';

        // --- IMPORTANT: CORRECTED VIEW PATH HERE ---
        return view('doctors/dashboard', $data); // Corrected path from 'doctor/dashboard' to 'doctors/dashboard'
    }

    // You can add other public methods for other roles or general pages here
}

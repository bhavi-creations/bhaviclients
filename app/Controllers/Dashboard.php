<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\Dashboard.php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\EmployeeModel;
use App\Models\UserModel;
use App\Models\EmployeeTaskModel;
use App\Models\ClientFileModel;
use CodeIgniter\Controller;

class Dashboard extends BaseController
{
    protected $clientModel;
    protected $employeeModel;
    protected $userModel;
    protected $employeeTaskModel;
    protected $clientFileModel;
    protected $db;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->employeeModel = new EmployeeModel();
        $this->userModel = new UserModel();
        $this->employeeTaskModel = new EmployeeTaskModel();
        $this->clientFileModel = new ClientFileModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $roleId = session()->get('role_id');
        
        // Admin Dashboard
        if ($roleId == 1) {
            return $this->adminDashboard();
        }
        // Admin Manager Dashboard
        elseif ($roleId == 5) {
            return $this->adminManagerDashboard();
        } 
        else {
            // Redirect other roles to their respective dashboards
            return redirect()->to('client-dashboard');
        }
    }

    /**
     * Admin Dashboard (Full access)
     */
    private function adminDashboard()
    {
        // Get total counts
        $totalClients = $this->clientModel->countAllResults();
        $totalEmployees = $this->employeeModel->countAllResults();

        // Get pending payments (schedules with status pending or overdue)
        $pendingPayments = $this->db->query("
            SELECT COUNT(*) as count, SUM(expected_amount) as total_amount
            FROM client_payment_schedule
            WHERE status IN ('pending', 'overdue')
        ")->getRowArray();

        // Get recent clients (last 10)
        $recentClients = $this->clientModel
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Get recent employees (last 5)
        $recentEmployees = $this->db->query("
            SELECT e.*, d.name as department_name
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            ORDER BY e.created_at DESC
            LIMIT 5
        ")->getResultArray();

        // Get pending payment schedules with client info
        $upcomingPayments = $this->db->query("
            SELECT 
                cps.id,
                cps.expected_amount,
                cps.expected_date,
                cps.status,
                c.name as client_name
            FROM client_payment_schedule cps
            JOIN clients c ON cps.client_id = c.id
            WHERE cps.status IN ('pending', 'overdue')
            ORDER BY cps.expected_date ASC
            LIMIT 10
        ")->getResultArray();

        $data = [
            'title' => 'Admin Dashboard',
            'totalClients' => $totalClients,
            'totalEmployees' => $totalEmployees,
            'pendingPaymentsCount' => $pendingPayments['count'] ?? 0,
            'pendingPaymentsAmount' => $pendingPayments['total_amount'] ?? 0,
            'recentClients' => $recentClients,
            'recentEmployees' => $recentEmployees,
            'upcomingPayments' => $upcomingPayments
        ];

        return view('dashboard/index', $data);
    }

    /**
     * Admin Manager Dashboard (Limited access)
     */
    private function adminManagerDashboard()
    {
        // Get total counts
        $totalClients = $this->clientModel->countAllResults();
        $totalEmployees = $this->employeeModel->countAllResults();

        // Get total tasks count
        $totalTasks = $this->db->query("
            SELECT COUNT(*) as count
            FROM employee_tasks
        ")->getRowArray();

        // Get pending tasks count
        $pendingTasks = $this->db->query("
            SELECT COUNT(*) as count
            FROM employee_tasks
            WHERE status = 'Pending'
        ")->getRowArray();

        // Get completed tasks count
        $completedTasks = $this->db->query("
            SELECT COUNT(*) as count
            FROM employee_tasks
            WHERE status = 'Completed'
        ")->getRowArray();

        // Get total payslips uploaded
        $totalPayslips = $this->db->query("
            SELECT COUNT(*) as count
            FROM employee_payslips
        ")->getResultArray();

        // Get recent tasks (last 10)
        $recentTasks = $this->db->query("
            SELECT 
                et.*,
                e.first_name as emp_first_name,
                e.last_name as emp_last_name,
                c.name as client_name
            FROM employee_tasks et
            LEFT JOIN employees e ON e.id = et.employee_id
            LEFT JOIN clients c ON c.id = et.client_id
            ORDER BY et.created_at DESC
            LIMIT 10
        ")->getResultArray();

        // Get recent uploaded payslips (last 5)
        $recentPayslips = $this->db->query("
            SELECT 
                ep.*,
                e.first_name,
                e.last_name
            FROM employee_payslips ep
            JOIN employees e ON e.id = ep.employee_id
            ORDER BY ep.created_at DESC
            LIMIT 5
        ")->getResultArray();

        // Get recent clients (last 10)
        $recentClients = $this->clientModel
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'title' => 'Admin Manager Dashboard',
            'totalClients' => $totalClients,
            'totalEmployees' => $totalEmployees,
            'totalTasks' => $totalTasks['count'] ?? 0,
            'pendingTasks' => $pendingTasks['count'] ?? 0,
            'completedTasks' => $completedTasks['count'] ?? 0,
            'totalPayslips' => $totalPayslips[0]['count'] ?? 0,
            'recentTasks' => $recentTasks,
            'recentPayslips' => $recentPayslips,
            'recentClients' => $recentClients
        ];

        return view('dashboard/admin_manager', $data);
    }
}

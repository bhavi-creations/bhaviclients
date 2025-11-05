<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\ClientPaymentView.php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ClientPaymentModel;
use App\Models\ClientPaymentScheduleModel;
use App\Models\ClientProjectSummaryModel;
use App\Models\ClientProjectModel;

class ClientPaymentView extends Controller
{
    protected $paymentModel;
    protected $scheduleModel;
    protected $summaryModel;
    protected $projectModel;
    protected $db;

    public function __construct()
    {
        $this->paymentModel = new ClientPaymentModel();
        $this->scheduleModel = new ClientPaymentScheduleModel();
        $this->summaryModel = new ClientProjectSummaryModel();
        $this->projectModel = new ClientProjectModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);

        // Access control: Only allow clients (role_id = 3)
        if (session()->get('role_id') != 3) {
            header('Location: ' . base_url('dashboard'));
            exit;
        }
    }

    /**
     * View payment details for logged-in client
     */
    public function index()
    {

        
        // Get client_id from user's record
        $userId = session()->get('user_id');
        
        $client = $this->db->table('clients')
                          ->where('user_id', $userId)
                          ->get()
                          ->getRowArray();

        if (!$client) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Client profile not found.');
        }

        $clientId = $client['id'];

        // Mark overdue schedules
        $this->scheduleModel->markOverdue();

        // Get all projects for this client
        $projects = $this->projectModel->getClientProjects($clientId);

        // If no projects exist, show empty state
        if (empty($projects)) {
            return view('client/my_payments/index', [
                'title' => 'My Payment Information',
                'client' => $client,
                'projects' => [],
                'selectedProject' => null,
                'summary' => null,
                'payments' => [],
                'schedules' => []
            ]);
        }

        // Get selected project (default to first active project)
        $selectedProjectId = $this->request->getGet('project_id') ?? $projects[0]['id'];
        $selectedProject = $this->projectModel->find($selectedProjectId);

        // Get summary for selected project
        $summary = $this->summaryModel->getOrCreateSummary($clientId, $selectedProjectId);

        // Get payments for selected project
        $payments = $this->paymentModel->getClientPaymentsWithTotal($clientId, $selectedProjectId);

        // Get schedules for selected project
        $schedules = $this->scheduleModel->getClientSchedules($clientId, $selectedProjectId);

        return view('client/my_payments/index', [
            'title' => 'My Payment Information',
            'client' => $client,
            'projects' => $projects,
            'selectedProject' => $selectedProject,
            'summary' => $summary,
            'payments' => $payments,
            'schedules' => $schedules
        ]);
    }

    /**
     * Download payment file
     */
    public function downloadPaymentFile($paymentId)
    {
        $userId = session()->get('user_id');
        
        // Get client_id
        $client = $this->db->table('clients')->where('user_id', $userId)->get()->getRowArray();
        if (!$client) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $payment = $this->paymentModel->find($paymentId);
        
        // Verify this payment belongs to this client
        if (!$payment || $payment['client_id'] != $client['id'] || empty($payment['transaction_file'])) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filePath = FCPATH . 'uploads/payment_receipts/' . $payment['transaction_file'];
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        $binary = file_get_contents($filePath);
        
        return $this->response
                    ->setHeader('Content-Type', 'application/octet-stream')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $payment['transaction_file'] . '"')
                    ->setBody($binary);
    }

    /**
     * Download schedule file
     */
    public function downloadScheduleFile($scheduleId)
    {
        $userId = session()->get('user_id');
        
        // Get client_id
        $client = $this->db->table('clients')->where('user_id', $userId)->get()->getRowArray();
        if (!$client) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $schedule = $this->scheduleModel->find($scheduleId);
        
        // Verify this schedule belongs to this client
        if (!$schedule || $schedule['client_id'] != $client['id'] || empty($schedule['schedule_file'])) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filePath = FCPATH . 'uploads/payment_schedules/' . $schedule['schedule_file'];
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        $binary = file_get_contents($filePath);
        
        return $this->response
                    ->setHeader('Content-Type', 'application/octet-stream')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $schedule['schedule_file'] . '"')
                    ->setBody($binary);
    }
}

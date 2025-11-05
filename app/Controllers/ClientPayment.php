<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\ClientPayment.php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ClientModel;
use App\Models\ClientPaymentModel;
use App\Models\ClientPaymentScheduleModel;
use App\Models\ClientProjectSummaryModel;
use App\Models\ClientProjectModel;

class ClientPayment extends Controller
{
    protected $clientModel;
    protected $paymentModel;
    protected $scheduleModel;
    protected $summaryModel;
    protected $projectModel;
    protected $db;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->paymentModel = new ClientPaymentModel();
        $this->scheduleModel = new ClientPaymentScheduleModel();
        $this->summaryModel = new ClientProjectSummaryModel();
        $this->projectModel = new ClientProjectModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);

        // Access control: Only allow admins
        if (!in_array(session()->get('role_id'), [1, 5])) {
            header('Location: ' . base_url('dashboard'));
            exit;
        }
    }

    /**
     * View payment details for a client
     */
    public function index($clientId)
    {
        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return redirect()->to(base_url('client'))->with('error', 'Client not found.');
        }

        // Mark overdue schedules
        $this->scheduleModel->markOverdue();

        // Get all projects for this client
        $projects = $this->projectModel->getClientProjects($clientId);

        // If no projects exist, create default one
        if (empty($projects)) {
            $defaultProjectId = $this->projectModel->createDefaultProject(
                $clientId, 
                $client['name'], 
                $client['started_date']
            );
            $projects = $this->projectModel->getClientProjects($clientId);
        }

        // Get selected project (default to first active project)
        $selectedProjectId = $this->request->getGet('project_id') ?? $projects[0]['id'];
        $selectedProject = $this->projectModel->find($selectedProjectId);

        // Get or create summary for selected project
        $summary = $this->summaryModel->getOrCreateSummary($clientId, $selectedProjectId);

        // Get payments for selected project
        $payments = $this->paymentModel->getClientPaymentsWithTotal($clientId, $selectedProjectId);

        // Get schedules for selected project
        $schedules = $this->scheduleModel->getClientSchedules($clientId, $selectedProjectId);

        return view('client_payment/index', [
            'title' => 'Payment Management - ' . $client['name'],
            'client' => $client,
            'projects' => $projects,
            'selectedProject' => $selectedProject,
            'summary' => $summary,
            'payments' => $payments,
            'schedules' => $schedules
        ]);
    }

    /**
     * Add new project
     */
    public function addProject($clientId)
    {
        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return redirect()->to(base_url('client'))->with('error', 'Client not found.');
        }

        $input = $this->request->getPost();

        $rules = [
            'project_name' => 'required|max_length[255]',
            'project_value' => 'required|decimal|greater_than_equal_to[0]',
            'project_start_date' => 'permit_empty|valid_date',
            'project_end_date' => 'permit_empty|valid_date',
            'remarks' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
        }

        $projectData = [
            'client_id' => $clientId,
            'project_name' => $input['project_name'],
            'project_value' => $input['project_value'],
            'project_start_date' => $input['project_start_date'] ?? null,
            'project_end_date' => $input['project_end_date'] ?? null,
            'total_paid' => 0.00,
            'total_due' => $input['project_value'],
            'status' => 'active',
            'remarks' => $input['remarks'] ?? null
        ];

        $projectId = $this->projectModel->insert($projectData);

        if ($projectId) {
            // Create summary for this project
            $this->summaryModel->insert([
                'client_id' => $clientId,
                'project_id' => $projectId,
                'project_start_date' => $input['project_start_date'] ?? null,
                'project_end_date' => $input['project_end_date'] ?? null,
                'total_project_value' => $input['project_value'],
                'total_paid' => 0.00,
                'total_due' => $input['project_value']
            ]);

            return redirect()->to(base_url('client-payment/' . $clientId . '?project_id=' . $projectId))
                           ->with('message', 'Project added successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to add project.');
        }
    }

    /**
     * Update project value
     */
    public function updateProjectValue($clientId)
    {
        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return redirect()->to(base_url('client'))->with('error', 'Client not found.');
        }

        $projectId = $this->request->getPost('project_id');
        $projectValue = $this->request->getPost('total_project_value');

        if (!$projectValue || $projectValue < 0) {
            return redirect()->back()->with('error', 'Invalid project value.');
        }

        // Update project value in projects table
        $this->projectModel->update($projectId, ['project_value' => $projectValue]);

        // Update summary
        $this->summaryModel->updateProjectValue($clientId, $projectValue, $projectId);

        // Recalculate totals
        $this->projectModel->recalculateProjectTotals($projectId);

        return redirect()->back()->with('message', 'Project value updated successfully!');
    }

    /**
     * Update project timeline
     */
    public function updateTimeline($clientId)
    {
        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return redirect()->to(base_url('client'))->with('error', 'Client not found.');
        }

        $projectId = $this->request->getPost('project_id');
        $startDate = $this->request->getPost('project_start_date');
        $endDate = $this->request->getPost('project_end_date');

        $rules = [
            'project_start_date' => 'permit_empty|valid_date',
            'project_end_date' => 'permit_empty|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Invalid date format.');
        }

        // Update project timeline
        $this->projectModel->update($projectId, [
            'project_start_date' => $startDate,
            'project_end_date' => $endDate
        ]);

        // Update summary timeline
        $this->summaryModel->updateTimeline($clientId, $startDate, $endDate, $projectId);

        return redirect()->back()->with('message', 'Timeline updated successfully!');
    }

    /**
     * Add new payment
     */
    public function addPayment($clientId)
    {
        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return redirect()->to(base_url('client'))->with('error', 'Client not found.');
        }

        $input = $this->request->getPost();

        $rules = [
            'project_id' => 'required|integer',
            'payment_type' => 'required|in_list[advance,installment,final]',
            'amount' => 'required|decimal|greater_than[0]',
            'payment_date' => 'required',
            'payment_method' => 'permit_empty|max_length[50]',
            'transaction_reference' => 'permit_empty|max_length[100]',
            'remarks' => 'permit_empty',
            'schedule_id' => 'permit_empty|integer',
            'transaction_file' => 'permit_empty|uploaded[transaction_file]|max_size[transaction_file,5120]|ext_in[transaction_file,pdf,jpg,jpeg,png,doc,docx]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
        }

        $this->db->transStart();

        try {
            // Handle file upload
            $transactionFile = null;
            $file = $this->request->getFile('transaction_file');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $uploadPath = FCPATH . 'uploads/payment_receipts/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $fileName = time() . '_' . $file->getRandomName();
                $file->move($uploadPath, $fileName);
                $transactionFile = $fileName;
            }

            // Insert payment
            $paymentData = [
                'client_id' => $clientId,
                'project_id' => $input['project_id'],
                'payment_type' => $input['payment_type'],
                'amount' => $input['amount'],
                'payment_date' => $input['payment_date'],
                'payment_method' => $input['payment_method'] ?? null,
                'transaction_reference' => $input['transaction_reference'] ?? null,
                'transaction_file' => $transactionFile,
                'remarks' => $input['remarks'] ?? null
            ];

            $paymentId = $this->paymentModel->insert($paymentData);

            if (!$paymentId) {
                throw new \Exception('Payment insert failed.');
            }

            // Update project summary
            $this->summaryModel->recalculateTotals($clientId, $input['project_id']);
            $this->projectModel->recalculateProjectTotals($input['project_id']);

            // Mark schedule as paid if schedule_id provided
            if (!empty($input['schedule_id'])) {
                $this->scheduleModel->markAsPaid($input['schedule_id'], $paymentId);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed.');
            }

            return redirect()->back()->with('message', 'Payment added successfully!');
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Payment add error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to add payment: ' . $e->getMessage());
        }
    }

    /**
     * Delete payment
     */
    public function deletePayment($paymentId)
    {
        $payment = $this->paymentModel->find($paymentId);
        if (!$payment) {
            return redirect()->back()->with('error', 'Payment not found.');
        }

        $this->db->transStart();

        try {
            // Delete file if exists
            if (!empty($payment['transaction_file'])) {
                $filePath = FCPATH . 'uploads/payment_receipts/' . $payment['transaction_file'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Delete payment
            $this->paymentModel->delete($paymentId);

            // Recalculate totals
            $this->summaryModel->recalculateTotals($payment['client_id'], $payment['project_id']);
            $this->projectModel->recalculateProjectTotals($payment['project_id']);

            // Update schedule if linked
            $this->scheduleModel->where('payment_id', $paymentId)
                ->set(['status' => 'pending', 'payment_id' => null])
                ->update();

            $this->db->transComplete();

            return redirect()->back()->with('message', 'Payment deleted successfully!');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Failed to delete payment.');
        }
    }

    /**
     * Download payment file
     */
    public function downloadPaymentFile($paymentId)
    {
        $payment = $this->paymentModel->find($paymentId);
        if (!$payment || empty($payment['transaction_file'])) {
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
     * Add payment schedule
     */
    public function addSchedule($clientId)
    {
        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return redirect()->to(base_url('client'))->with('error', 'Client not found.');
        }

        $input = $this->request->getPost();

        $rules = [
            'project_id' => 'required|integer',
            'expected_amount' => 'required|decimal|greater_than[0]',
            'expected_date' => 'required',
            'remarks' => 'permit_empty',
            'schedule_file' => 'permit_empty|uploaded[schedule_file]|max_size[schedule_file,5120]|ext_in[schedule_file,pdf,jpg,jpeg,png,doc,docx]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
        }

        // Handle file upload
        $scheduleFile = null;
        $file = $this->request->getFile('schedule_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/payment_schedules/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $fileName = time() . '_' . $file->getRandomName();
            $file->move($uploadPath, $fileName);
            $scheduleFile = $fileName;
        }

        $scheduleData = [
            'client_id' => $clientId,
            'project_id' => $input['project_id'],
            'expected_amount' => $input['expected_amount'],
            'expected_date' => $input['expected_date'],
            'status' => 'pending',
            'schedule_file' => $scheduleFile,
            'remarks' => $input['remarks'] ?? null
        ];

        if ($this->scheduleModel->insert($scheduleData)) {
            return redirect()->back()->with('message', 'Payment schedule added successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to add schedule.');
        }
    }

    /**
     * Delete schedule
     */
    public function deleteSchedule($scheduleId)
    {
        $schedule = $this->scheduleModel->find($scheduleId);
        if (!$schedule) {
            return redirect()->back()->with('error', 'Schedule not found.');
        }

        // Delete file if exists
        if (!empty($schedule['schedule_file'])) {
            $filePath = FCPATH . 'uploads/payment_schedules/' . $schedule['schedule_file'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        if ($this->scheduleModel->delete($scheduleId)) {
            return redirect()->back()->with('message', 'Schedule deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to delete schedule.');
        }
    }

    /**
     * Download schedule file
     */
    public function downloadScheduleFile($scheduleId)
    {
        $schedule = $this->scheduleModel->find($scheduleId);
        if (!$schedule || empty($schedule['schedule_file'])) {
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

    /**
     * Edit payment
     */
    public function editPayment($paymentId)
    {
        $payment = $this->paymentModel->find($paymentId);
        if (!$payment) {
            return redirect()->back()->with('error', 'Payment not found.');
        }

        $input = $this->request->getPost();

        $rules = [
            'payment_type' => 'required|in_list[advance,installment,final]',
            'amount' => 'required|decimal|greater_than[0]',
            'payment_date' => 'required',
            'payment_method' => 'permit_empty|max_length[50]',
            'transaction_reference' => 'permit_empty|max_length[100]',
            'remarks' => 'permit_empty',
            'transaction_file' => 'permit_empty|uploaded[transaction_file]|max_size[transaction_file,5120]|ext_in[transaction_file,pdf,jpg,jpeg,png,doc,docx]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
        }

        $this->db->transStart();

        try {
            // Handle file upload
            $transactionFile = $payment['transaction_file'];
            $file = $this->request->getFile('transaction_file');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Delete old file
                if (!empty($transactionFile)) {
                    $oldPath = FCPATH . 'uploads/payment_receipts/' . $transactionFile;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $uploadPath = FCPATH . 'uploads/payment_receipts/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $fileName = time() . '_' . $file->getRandomName();
                $file->move($uploadPath, $fileName);
                $transactionFile = $fileName;
            }

            // Update payment
            $paymentData = [
                'payment_type' => $input['payment_type'],
                'amount' => $input['amount'],
                'payment_date' => $input['payment_date'],
                'payment_method' => $input['payment_method'] ?? null,
                'transaction_reference' => $input['transaction_reference'] ?? null,
                'transaction_file' => $transactionFile,
                'remarks' => $input['remarks'] ?? null
            ];

            $this->paymentModel->update($paymentId, $paymentData);

            // Recalculate totals
            $this->summaryModel->recalculateTotals($payment['client_id'], $payment['project_id']);
            $this->projectModel->recalculateProjectTotals($payment['project_id']);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed.');
            }

            return redirect()->back()->with('message', 'Payment updated successfully!');
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Payment edit error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update payment: ' . $e->getMessage());
        }
    }

    /**
     * Edit schedule
     */
    public function editSchedule($scheduleId)
    {
        $schedule = $this->scheduleModel->find($scheduleId);
        if (!$schedule) {
            return redirect()->back()->with('error', 'Schedule not found.');
        }

        $input = $this->request->getPost();

        $rules = [
            'expected_amount' => 'required|decimal|greater_than[0]',
            'expected_date' => 'required|valid_date',
            'status' => 'required|in_list[pending,paid,overdue,cancelled,received]',
            'remarks' => 'permit_empty',
            'schedule_file' => 'permit_empty|uploaded[schedule_file]|max_size[schedule_file,5120]|ext_in[schedule_file,pdf,jpg,jpeg,png,doc,docx]'
        ];

        if (isset($input['status']) && $input['status'] === 'received') {
            $rules['received_date'] = 'required|valid_date';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
        }

        $this->db->transStart();

        try {
            $oldStatus = $schedule['status'];
            $newStatus = $input['status'];

            // Handle file upload
            $scheduleFile = $schedule['schedule_file'];
            $file = $this->request->getFile('schedule_file');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Delete old file
                if (!empty($scheduleFile)) {
                    $oldPath = FCPATH . 'uploads/payment_schedules/' . $scheduleFile;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $uploadPath = FCPATH . 'uploads/payment_schedules/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $fileName = time() . '_' . $file->getRandomName();
                $file->move($uploadPath, $fileName);
                $scheduleFile = $fileName;
            }

            $scheduleData = [
                'expected_amount' => $input['expected_amount'],
                'expected_date' => $input['expected_date'],
                'status' => $newStatus,
                'schedule_file' => $scheduleFile,
                'remarks' => $input['remarks'] ?? null
            ];

            // Handle status change FROM "received" TO something else
            if ($oldStatus === 'received' && $newStatus !== 'received') {
                if (!empty($schedule['payment_id'])) {
                    $this->paymentModel->delete($schedule['payment_id']);
                    $scheduleData['payment_id'] = null;
                }
                $scheduleData['received_date'] = null;
            }

            // Handle status change TO "received"
            if ($newStatus === 'received') {
                $scheduleData['received_date'] = $input['received_date'];

                if (empty($schedule['payment_id'])) {
                    $paymentData = [
                        'client_id' => $schedule['client_id'],
                        'project_id' => $schedule['project_id'],
                        'payment_type' => 'installment',
                        'amount' => $input['expected_amount'],
                        'payment_date' => $input['received_date'],
                        'payment_method' => 'Schedule Payment',
                        'transaction_reference' => 'Schedule #' . $scheduleId,
                        'remarks' => 'Auto-created from payment schedule' . (!empty($input['remarks']) ? ': ' . $input['remarks'] : '')
                    ];

                    $paymentId = $this->paymentModel->insert($paymentData);

                    if (!$paymentId) {
                        throw new \Exception('Failed to create payment record.');
                    }

                    $scheduleData['payment_id'] = $paymentId;
                }
            } else {
                $scheduleData['received_date'] = null;
            }

            $this->scheduleModel->update($scheduleId, $scheduleData);

            // Recalculate project summary
            $this->summaryModel->recalculateTotals($schedule['client_id'], $schedule['project_id']);
            $this->projectModel->recalculateProjectTotals($schedule['project_id']);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed.');
            }

            $successMsg = 'Schedule updated successfully!';
            if ($newStatus === 'received' && $oldStatus !== 'received') {
                $successMsg .= ' Payment record created automatically.';
            } elseif ($oldStatus === 'received' && $newStatus !== 'received') {
                $successMsg .= ' Payment record deleted automatically.';
            }

            return redirect()->back()->with('message', $successMsg);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Schedule edit error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update schedule: ' . $e->getMessage());
        }
    }

    /**
     * Display list of all clients for payment management
     */
    public function list()
    {
        $clients = $this->clientModel->findAll();

        return view('client_payment/list', [
            'title' => 'Client Payments',
            'clients' => $clients
        ]);
    }
}

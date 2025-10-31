<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\ClientPayment.php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ClientModel;
use App\Models\ClientPaymentModel;
use App\Models\ClientPaymentScheduleModel;
use App\Models\ClientProjectSummaryModel;

class ClientPayment extends Controller
{
    protected $clientModel;
    protected $paymentModel;
    protected $scheduleModel;
    protected $summaryModel;
    protected $db;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->paymentModel = new ClientPaymentModel();
        $this->scheduleModel = new ClientPaymentScheduleModel();
        $this->summaryModel = new ClientProjectSummaryModel();
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

        // Get or create summary
        $summary = $this->summaryModel->getOrCreateSummary($clientId);

        // Get all payments
        $payments = $this->paymentModel->getClientPaymentsWithTotal($clientId);

        // Get all schedules
        $schedules = $this->scheduleModel->getClientSchedules($clientId);

        return view('client_payment/index', [
            'title' => 'Payment Management - ' . $client['name'],
            'client' => $client,
            'summary' => $summary,
            'payments' => $payments,
            'schedules' => $schedules
        ]);
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

        $projectValue = $this->request->getPost('total_project_value');

        if (!$projectValue || $projectValue < 0) {
            return redirect()->back()->with('error', 'Invalid project value.');
        }

        $this->summaryModel->updateProjectValue($clientId, $projectValue);

        return redirect()->back()->with('message', 'Project value updated successfully!');
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
            'payment_type' => 'required|in_list[advance,installment,final]',
            'amount' => 'required|decimal|greater_than[0]',
            'payment_date' => 'required',
            'payment_method' => 'permit_empty|max_length[50]',
            'transaction_reference' => 'permit_empty|max_length[100]',
            'remarks' => 'permit_empty',
            'schedule_id' => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
        }

        $this->db->transStart();

        try {
            // Insert payment
            $paymentData = [
                'client_id' => $clientId,
                'payment_type' => $input['payment_type'],
                'amount' => $input['amount'],
                'payment_date' => $input['payment_date'],
                'payment_method' => $input['payment_method'] ?? null,
                'transaction_reference' => $input['transaction_reference'] ?? null,
                'remarks' => $input['remarks'] ?? null
            ];

            $paymentId = $this->paymentModel->insert($paymentData);

            if (!$paymentId) {
                throw new \Exception('Payment insert failed.');
            }

            // Update project summary
            $this->summaryModel->recalculateTotals($clientId);

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
            // Delete payment
            $this->paymentModel->delete($paymentId);

            // Recalculate totals
            $this->summaryModel->recalculateTotals($payment['client_id']);

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
            'expected_amount' => 'required|decimal|greater_than[0]',
            'expected_date' => 'required',
            'remarks' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
        }

        $scheduleData = [
            'client_id' => $clientId,
            'expected_amount' => $input['expected_amount'],
            'expected_date' => $input['expected_date'],
            'status' => 'pending',
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

        if ($this->scheduleModel->delete($scheduleId)) {
            return redirect()->back()->with('message', 'Schedule deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to delete schedule.');
        }
    }

    /**
     * Update schedule status
     */
    public function updateScheduleStatus($scheduleId)
    {
        $schedule = $this->scheduleModel->find($scheduleId);
        if (!$schedule) {
            return redirect()->back()->with('error', 'Schedule not found.');
        }

        $status = $this->request->getPost('status');

        if (!in_array($status, ['pending', 'paid', 'overdue', 'cancelled'])) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        if ($this->scheduleModel->update($scheduleId, ['status' => $status])) {
            return redirect()->back()->with('message', 'Schedule status updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to update status.');
        }
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
            'remarks' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
        }

        $this->db->transStart();

        try {
            // Update payment
            $paymentData = [
                'payment_type' => $input['payment_type'],
                'amount' => $input['amount'],
                'payment_date' => $input['payment_date'],
                'payment_method' => $input['payment_method'] ?? null,
                'transaction_reference' => $input['transaction_reference'] ?? null,
                'remarks' => $input['remarks'] ?? null
            ];

            $this->paymentModel->update($paymentId, $paymentData);

            // Recalculate totals
            $this->summaryModel->recalculateTotals($payment['client_id']);

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
            'expected_date' => 'required',
            'status' => 'required|in_list[pending,paid,overdue,cancelled]',
            'remarks' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
        }

        $scheduleData = [
            'expected_amount' => $input['expected_amount'],
            'expected_date' => $input['expected_date'],
            'status' => $input['status'],
            'remarks' => $input['remarks'] ?? null
        ];

        if ($this->scheduleModel->update($scheduleId, $scheduleData)) {
            return redirect()->back()->with('message', 'Schedule updated successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update schedule.');
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

<?php
// C:\xampp\htdocs\bhaviclients\app\Models\ClientProjectSummaryModel.php

namespace App\Models;

use CodeIgniter\Model;

class ClientProjectSummaryModel extends Model
{
    protected $table = 'client_project_summary';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'client_id',
        'total_project_value',
        'total_paid',
        'total_due'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'client_id' => 'required|integer',
        'total_project_value' => 'required|decimal',
        'total_paid' => 'permit_empty|decimal',
        'total_due' => 'permit_empty|decimal'
    ];

    /**
     * Get or create project summary for a client
     */
    public function getOrCreateSummary($clientId)
    {
        $summary = $this->where('client_id', $clientId)->first();
        
        if (!$summary) {
            $this->insert([
                'client_id' => $clientId,
                'total_project_value' => 0.00,
                'total_paid' => 0.00,
                'total_due' => 0.00
            ]);
            $summary = $this->where('client_id', $clientId)->first();
        }
        
        return $summary;
    }

    /**
     * Update totals after payment
     */
    public function recalculateTotals($clientId)
    {
        $paymentModel = new \App\Models\ClientPaymentModel();
        $totalPaid = $paymentModel->where('client_id', $clientId)->selectSum('amount')->first();
        
        $summary = $this->where('client_id', $clientId)->first();
        
        if ($summary) {
            $paid = $totalPaid['amount'] ?? 0.00;
            $due = $summary['total_project_value'] - $paid;
            
            $this->update($summary['id'], [
                'total_paid' => $paid,
                'total_due' => $due
            ]);
        }
    }

    /**
     * Update project value
     */
    public function updateProjectValue($clientId, $newValue)
    {
        $summary = $this->getOrCreateSummary($clientId);
        
        $due = $newValue - $summary['total_paid'];
        
        $this->update($summary['id'], [
            'total_project_value' => $newValue,
            'total_due' => $due
        ]);
    }
}

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
        'project_id',               // NEW - link to specific project
        'project_start_date',       // NEW - timeline start
        'project_end_date',         // NEW - timeline end
        'total_project_value',
        'total_paid',
        'total_due'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'client_id' => 'required|integer',
        'project_id' => 'permit_empty|integer',
        'project_start_date' => 'permit_empty|valid_date',
        'project_end_date' => 'permit_empty|valid_date',
        'total_project_value' => 'required|decimal',
        'total_paid' => 'permit_empty|decimal',
        'total_due' => 'permit_empty|decimal'
    ];

    /**
     * Get or create project summary for a client/project
     */
    public function getOrCreateSummary($clientId, $projectId = null)
    {
        $builder = $this->where('client_id', $clientId);
        
        if ($projectId) {
            $builder->where('project_id', $projectId);
        } else {
            // Get first summary if no project specified
            $builder->orderBy('id', 'ASC');
        }
        
        $summary = $builder->first();
        
        if (!$summary) {
            $this->insert([
                'client_id' => $clientId,
                'project_id' => $projectId,
                'total_project_value' => 0.00,
                'total_paid' => 0.00,
                'total_due' => 0.00
            ]);
            $summary = $builder->first();
        }
        
        return $summary;
    }

    /**
     * Update totals after payment (for specific project if provided)
     */
    public function recalculateTotals($clientId, $projectId = null)
    {
        $paymentModel = new \App\Models\ClientPaymentModel();
        
        $builder = $paymentModel->where('client_id', $clientId);
        if ($projectId) {
            $builder->where('project_id', $projectId);
        }
        
        $totalPaid = $builder->selectSum('amount')->first();
        
        $summaryBuilder = $this->where('client_id', $clientId);
        if ($projectId) {
            $summaryBuilder->where('project_id', $projectId);
        }
        
        $summary = $summaryBuilder->first();
        
        if ($summary) {
            $paid = $totalPaid['amount'] ?? 0.00;
            $due = $summary['total_project_value'] - $paid;
            
            $this->update($summary['id'], [
                'total_paid' => $paid,
                'total_due' => max(0, $due)
            ]);
        }
    }

    /**
     * Update project value
     */
    public function updateProjectValue($clientId, $newValue, $projectId = null)
    {
        $summary = $this->getOrCreateSummary($clientId, $projectId);
        
        $due = $newValue - $summary['total_paid'];
        
        $this->update($summary['id'], [
            'total_project_value' => $newValue,
            'total_due' => max(0, $due)
        ]);
    }

    /**
     * Update project timeline
     */
    public function updateTimeline($clientId, $startDate, $endDate, $projectId = null)
    {
        $summary = $this->getOrCreateSummary($clientId, $projectId);
        
        return $this->update($summary['id'], [
            'project_start_date' => $startDate,
            'project_end_date' => $endDate
        ]);
    }
}

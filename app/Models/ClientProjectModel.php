<?php
// C:\xampp\htdocs\bhaviclients\app\Models\ClientProjectModel.php

namespace App\Models;

use CodeIgniter\Model;

class ClientProjectModel extends Model
{
    protected $table = 'client_projects';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'client_id',
        'project_name',
        'project_value',
        'project_start_date',
        'project_end_date',
        'total_paid',
        'total_due',
        'status',
        'remarks'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'array';

    /**
     * Get all projects for a client
     */
    public function getClientProjects($clientId)
    {
        return $this->where('client_id', $clientId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get active projects only
     */
    public function getActiveProjects($clientId)
    {
        return $this->where('client_id', $clientId)
                    ->where('status', 'active')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Recalculate project totals
     */
    public function recalculateProjectTotals($projectId)
    {
        $db = \Config\Database::connect();
        
        // Get total paid from payments
        $totalPaid = $db->table('client_payments')
                       ->selectSum('amount')
                       ->where('project_id', $projectId)
                       ->get()
                       ->getRow()
                       ->amount ?? 0;

        $project = $this->find($projectId);
        if (!$project) return false;

        $totalDue = $project['project_value'] - $totalPaid;

        return $this->update($projectId, [
            'total_paid' => $totalPaid,
            'total_due' => max(0, $totalDue)
        ]);
    }

    /**
     * Create default project for client
     */
    public function createDefaultProject($clientId, $clientName, $startedDate = null)
    {
        $data = [
            'client_id' => $clientId,
            'project_name' => $clientName . ' - Main Project',
            'project_value' => 0.00,
            'project_start_date' => $startedDate,
            'project_end_date' => null,
            'total_paid' => 0.00,
            'total_due' => 0.00,
            'status' => 'active'
        ];

        return $this->insert($data);
    }
}

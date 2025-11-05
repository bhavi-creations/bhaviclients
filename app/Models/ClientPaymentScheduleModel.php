<?php
// C:\xampp\htdocs\bhaviclients\app\Models\ClientPaymentScheduleModel.php

namespace App\Models;

use CodeIgniter\Model;

class ClientPaymentScheduleModel extends Model
{
    protected $table = 'client_payment_schedule';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'client_id',
        'project_id',           // NEW - for multi-project support
        'expected_amount',
        'expected_date',
        'status',
        'remarks',
        'schedule_file',        // NEW - for file upload
        'payment_id',
        'received_date'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'client_id' => 'required|integer',
        'project_id' => 'permit_empty|integer',
        'expected_amount' => 'required|decimal|greater_than[0]',
        'expected_date' => 'required',
        'status' => 'required|in_list[pending,paid,overdue,cancelled,received]',
        'remarks' => 'permit_empty',
        'schedule_file' => 'permit_empty|max_length[255]',
        'payment_id' => 'permit_empty|integer',
        'received_date' => 'permit_empty|valid_date'
    ];

    /**
     * Get all schedules for a client (optionally filtered by project)
     */
    public function getClientSchedules($clientId, $projectId = null)
    {
        $builder = $this->where('client_id', $clientId);
        
        if ($projectId) {
            $builder->where('project_id', $projectId);
        }
        
        return $builder->orderBy('expected_date', 'ASC')->findAll();
    }

    /**
     * Mark overdue schedules
     */
    public function markOverdue()
    {
        $today = date('Y-m-d');
        $this->where('status', 'pending')
             ->where('expected_date <', $today)
             ->set(['status' => 'overdue'])
             ->update();
    }

    /**
     * Get upcoming schedules (pending or overdue)
     */
    public function getUpcomingSchedules($clientId, $projectId = null)
    {
        $builder = $this->where('client_id', $clientId)
                        ->whereIn('status', ['pending', 'overdue']);
        
        if ($projectId) {
            $builder->where('project_id', $projectId);
        }
        
        return $builder->orderBy('expected_date', 'ASC')->findAll();
    }

    /**
     * Mark schedule as paid
     */
    public function markAsPaid($scheduleId, $paymentId)
    {
        return $this->update($scheduleId, [
            'status' => 'paid',
            'payment_id' => $paymentId
        ]);
    }

    /**
     * Get schedules for a specific project
     */
    public function getProjectSchedules($projectId)
    {
        return $this->where('project_id', $projectId)
                    ->orderBy('expected_date', 'ASC')
                    ->findAll();
    }
}
    
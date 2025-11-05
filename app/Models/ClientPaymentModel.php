<?php
// C:\xampp\htdocs\bhaviclients\app\Models\ClientPaymentModel.php

namespace App\Models;

use CodeIgniter\Model;

class ClientPaymentModel extends Model
{
    protected $table = 'client_payments';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'client_id',
        'project_id',           // NEW - for multi-project support
        'payment_type',
        'amount',
        'payment_date',
        'payment_method',
        'transaction_reference',
        'transaction_file',     // NEW - for file upload
        'remarks'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'client_id' => 'required|integer',
        'project_id' => 'permit_empty|integer',
        'payment_type' => 'required|in_list[advance,installment,final]',
        'amount' => 'required|decimal|greater_than[0]',
        'payment_date' => 'required',
        'payment_method' => 'permit_empty|max_length[50]',
        'transaction_reference' => 'permit_empty|max_length[100]',
        'transaction_file' => 'permit_empty|max_length[255]',
        'remarks' => 'permit_empty'
    ];

    /**
     * Get all payments for a client (optionally filtered by project)
     */
    public function getClientPaymentsWithTotal($clientId, $projectId = null)
    {
        $builder = $this->where('client_id', $clientId);
        
        if ($projectId) {
            $builder->where('project_id', $projectId);
        }
        
        return $builder->orderBy('payment_date', 'DESC')
                       ->orderBy('id', 'DESC')
                       ->findAll();
    }

    /**
     * Get total paid by client (optionally for specific project)
     */
    public function getTotalPaid($clientId, $projectId = null)
    {
        $builder = $this->where('client_id', $clientId);
        
        if ($projectId) {
            $builder->where('project_id', $projectId);
        }
        
        $result = $builder->selectSum('amount')->first();
        
        return $result['amount'] ?? 0.00;
    }

    /**
     * Get payments for a specific project
     */
    public function getProjectPayments($projectId)
    {
        return $this->where('project_id', $projectId)
                    ->orderBy('payment_date', 'DESC')
                    ->findAll();
    }
}

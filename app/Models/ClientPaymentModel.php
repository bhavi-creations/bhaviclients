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
        'payment_type',
        'amount',
        'payment_date',
        'payment_method',
        'transaction_reference',
        'remarks'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'client_id' => 'required|integer',
        'payment_type' => 'required|in_list[advance,installment,final]',
        'amount' => 'required|decimal|greater_than[0]',
        'payment_date' => 'required',
        'payment_method' => 'permit_empty|max_length[50]',
        'transaction_reference' => 'permit_empty|max_length[100]',
        'remarks' => 'permit_empty'
    ];

    /**
     * Get all payments for a client with calculated running total
     */
    public function getClientPaymentsWithTotal($clientId)
    {
        return $this->where('client_id', $clientId)
                    ->orderBy('payment_date', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }

    /**
     * Get total paid by client
     */
    public function getTotalPaid($clientId)
    {
        $result = $this->where('client_id', $clientId)
                       ->selectSum('amount')
                       ->first();
        
        return $result['amount'] ?? 0.00;
    }
}

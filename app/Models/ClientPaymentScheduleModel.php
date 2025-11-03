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
        'expected_amount',
        'expected_date',
        'status',
        'remarks',
        'payment_id',
        'received_date'    // ← MAKE SURE THIS IS HERE
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'client_id' => 'required|integer',
        'expected_amount' => 'required|decimal|greater_than[0]',
        'expected_date' => 'required',
        'status' => 'required|in_list[pending,paid,overdue,cancelled,received]',  // ← MAKE SURE "received" IS HERE
        'remarks' => 'permit_empty',
        'payment_id' => 'permit_empty|integer',
        'received_date' => 'permit_empty|valid_date'  // ← MAKE SURE THIS IS HERE
    ];

    /**
     * Get all schedules for a client
     */
    public function getClientSchedules($clientId)
    {
        return $this->where('client_id', $clientId)
                    ->orderBy('expected_date', 'ASC')
                    ->findAll();
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
    public function getUpcomingSchedules($clientId)
    {
        return $this->where('client_id', $clientId)
                    ->whereIn('status', ['pending', 'overdue'])
                    ->orderBy('expected_date', 'ASC')
                    ->findAll();
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
}

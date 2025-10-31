<?php
// C:\xampp\htdocs\bhaviclients\app\Models\ClientWeeklyScheduleModel.php

namespace App\Models;

use CodeIgniter\Model;

class ClientWeeklyScheduleModel extends Model
{
    protected $table = 'client_weekly_schedules';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'client_id',
        'week_start_date',
        'week_end_date',
        'department_columns',
        'schedule_data',
        'status',
        'remarks',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Get all schedules with client info
     */
    public function getAllSchedulesWithClient()
    {
        return $this->select('client_weekly_schedules.*, clients.name as client_name, users.first_name, users.last_name')
                    ->join('clients', 'clients.id = client_weekly_schedules.client_id', 'left')
                    ->join('users', 'users.id = client_weekly_schedules.created_by', 'left')
                    ->orderBy('client_weekly_schedules.week_start_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get schedules for a specific client
     */
    public function getClientSchedules($clientId)
    {
        return $this->where('client_id', $clientId)
                    ->orderBy('week_start_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get schedule by client and week
     */
    public function getScheduleByWeek($clientId, $weekStartDate)
    {
        return $this->where('client_id', $clientId)
                    ->where('week_start_date', $weekStartDate)
                    ->first();
    }

    /**
     * Get department columns as array
     */
    public function getDepartmentColumns($id)
    {
        $schedule = $this->find($id);
        if ($schedule && !empty($schedule['department_columns'])) {
            return json_decode($schedule['department_columns'], true);
        }
        return [];
    }

    /**
     * Get schedule data as array
     */
    public function getScheduleData($id)
    {
        $schedule = $this->find($id);
        if ($schedule && !empty($schedule['schedule_data'])) {
            return json_decode($schedule['schedule_data'], true);
        }
        return [];
    }

    /**
     * Get current/latest schedule for client
     */
    public function getCurrentSchedule($clientId)
    {
        $today = date('Y-m-d');
        
        // Try to find schedule containing today
        $schedule = $this->where('client_id', $clientId)
                         ->where('week_start_date <=', $today)
                         ->where('week_end_date >=', $today)
                         ->where('status', 'published')
                         ->first();
        
        // If not found, get the latest published schedule
        if (!$schedule) {
            $schedule = $this->where('client_id', $clientId)
                             ->where('status', 'published')
                             ->orderBy('week_start_date', 'DESC')
                             ->first();
        }
        
        return $schedule;
    }

    /**
     * Check if schedule exists for client and week
     */
    public function scheduleExists($clientId, $weekStartDate, $excludeId = null)
    {
        $builder = $this->where('client_id', $clientId)
                        ->where('week_start_date', $weekStartDate);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }
}

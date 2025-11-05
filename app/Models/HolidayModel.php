<?php
// C:\xampp\htdocs\bhaviclients\app\Models\HolidayModel.php

namespace App\Models;

use CodeIgniter\Model;

class HolidayModel extends Model
{
    protected $table = 'holidays';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'holiday_name',
        'holiday_date',
        'description',
        'is_recurring',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'holiday_name' => 'required|min_length[3]|max_length[255]',
        'holiday_date' => 'required|valid_date',
        'description' => 'permit_empty|max_length[500]',
        'is_recurring' => 'permit_empty|in_list[0,1]',
        'created_by' => 'required|integer'
    ];

    /**
     * Get upcoming holidays
     */
    public function getUpcomingHolidays($limit = 10)
    {
        return $this->where('holiday_date >=', date('Y-m-d'))
                    ->orderBy('holiday_date', 'ASC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get all holidays with creator info
     */
    public function getAllHolidaysWithCreator()
    {
        return $this->select('holidays.*, 
                            users.first_name as creator_first_name, 
                            users.last_name as creator_last_name')
                    ->join('users', 'users.id = holidays.created_by', 'left')
                    ->orderBy('holidays.holiday_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get holidays by year
     */
    public function getHolidaysByYear($year)
    {
        return $this->where('YEAR(holiday_date)', $year)
                    ->orderBy('holiday_date', 'ASC')
                    ->findAll();
    }
}

<?php
// C:\xampp\htdocs\bhaviclients\app\Models\EmployeeSalaryHistoryModel.php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeSalaryHistoryModel extends Model
{
    protected $table = 'employee_salary_history';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'employee_id',
        'salary_amount',
        'effective_date',
        'increment_type',
        'increment_percentage',
        'previous_salary',
        'reason',
        'approved_by',
        'remarks'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'employee_id'    => 'required|integer',
        'salary_amount'  => 'required|decimal|greater_than[0]',
        'effective_date' => 'required|valid_date',
        'increment_type' => 'required|in_list[initial,increment,promotion,annual_review,adjustment]',
        'increment_percentage' => 'permit_empty|decimal',
        'previous_salary' => 'permit_empty|decimal',
        'reason'         => 'permit_empty',
        'approved_by'    => 'permit_empty|integer',
        'remarks'        => 'permit_empty'
    ];

    /**
     * Get salary history for an employee
     */
    public function getEmployeeSalaryHistory($employeeId)
    {
        return $this->where('employee_id', $employeeId)
                    ->orderBy('effective_date', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }

    /**
     * Get current salary for an employee
     */
    public function getCurrentSalary($employeeId)
    {
        return $this->where('employee_id', $employeeId)
                    ->orderBy('effective_date', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->first();
    }

    /**
     * Add salary record with auto-calculation
     */
    public function addSalaryRecord($data)
    {
        // Get previous salary if exists
        $previousSalary = $this->getCurrentSalary($data['employee_id']);
        
        if ($previousSalary && $data['increment_type'] !== 'initial') {
            $data['previous_salary'] = $previousSalary['salary_amount'];
            
            // Calculate increment percentage
            $increase = $data['salary_amount'] - $previousSalary['salary_amount'];
            $data['increment_percentage'] = round(($increase / $previousSalary['salary_amount']) * 100, 2);
        }

        return $this->insert($data);
    }

    /**
     * Get salary statistics
     */
    public function getSalaryStats($employeeId)
    {
        $history = $this->getEmployeeSalaryHistory($employeeId);
        
        if (empty($history)) {
            return null;
        }

        $current = $history[0];
        $initial = end($history);
        
        $totalIncrease = $current['salary_amount'] - $initial['salary_amount'];
        $totalPercentage = $initial['salary_amount'] > 0 
            ? round(($totalIncrease / $initial['salary_amount']) * 100, 2) 
            : 0;

        return [
            'current_salary' => $current['salary_amount'],
            'initial_salary' => $initial['salary_amount'],
            'total_increase' => $totalIncrease,
            'total_percentage' => $totalPercentage,
            'total_increments' => count($history) - 1,
            'last_increment_date' => count($history) > 1 ? $history[0]['effective_date'] : null
        ];
    }
}

<?php 
// C:\xampp\htdocs\bhaviclients\app\Models\EmployeeTaskModel.php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeTaskModel extends Model
{
    protected $table = 'employee_tasks';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'employee_id',
        'client_id', // <-- CRITICAL: Added to allow saving the client association
        'title',
        'description',
        'due_date',
        'status',
    ];

    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Retrieves tasks associated with an employee, along with the linked client's name.
     * * This method joins the 'clients' table using the foreign key 'client_id'.
     * * @param int|null $employeeId Optional ID to filter by
     * @return array
     */
    public function getEmployeeTasksWithClient(int $employeeId = null): array
    {
        $builder = $this->select('employee_tasks.*, clients.name AS client_name')
                        ->join('clients', 'clients.id = employee_tasks.client_id', 'left');

        if ($employeeId !== null) {
            $builder->where('employee_tasks.employee_id', $employeeId);
        }

        return $builder->findAll();
    }
}

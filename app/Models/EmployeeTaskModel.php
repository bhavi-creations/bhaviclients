<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeTaskModel extends Model
{
    protected $table            = 'employee_tasks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'employee_id',
        'client_id',
        'title',
        'description',
        'status',
        'submitted_at',
        'files_upload',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'submitted_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'employee_id' => 'required|integer',
        'title'       => 'required|min_length[3]|max_length[255]',
        'description' => 'required|min_length[10]',
        'status'      => 'in_list[Pending,In Progress,Completed,Review]',
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Task title is required',
            'min_length' => 'Title must be at least 3 characters',
        ],
        'description' => [
            'required' => 'Task description is required',
            'min_length' => 'Description must be at least 10 characters',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setSubmittedAt'];
    protected $beforeUpdate   = ['setUpdatedAt'];

    protected function setSubmittedAt(array $data)
    {
        if (!isset($data['data']['submitted_at'])) {
            $data['data']['submitted_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    protected function setUpdatedAt(array $data)
    {
        $data['data']['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }
}

<?php 
// C:\xampp\htdocs\bhaviclients\app\Models\EmployeeModel.php
namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false; 

    // IMPORTANT: Added 'role_id' to the allowed fields.
    protected $allowedFields = [
        'user_id', 
        'department_id', 
        'role_id', // Added
        'first_name', 
        'last_name', 
        'email', 
        'phone'
    ];

    protected $useTimestamps = true; 
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at'; 
    protected $updatedField  = 'updated_at'; 

    // Basic Validation Rules (as provided by you)
    protected $validationRules = [
        'first_name'    => 'required|min_length[2]|max_length[100]',
        'last_name'     => 'required|min_length[2]|max_length[100]',
        'email'         => 'required|max_length[255]|valid_email|is_unique[employees.email]',
        'phone'         => 'permit_empty|max_length[20]',
        'department_id' => 'required|integer',
        
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
}

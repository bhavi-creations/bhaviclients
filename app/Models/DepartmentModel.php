<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    // Define the table this model primarily works with
    protected $table = 'departments';

    // Define the primary key
    protected $primaryKey = 'id';

    // The CRITICAL FIX: Set return type to 'array' to match how your views access $department['name']
    protected $returnType = 'array'; 
    
    // Enable timestamps (assuming you have created_at/updated_at fields)
    protected $useTimestamps = true;
    
    // Define the fields allowed to be inserted/updated by the save() and update() methods
    // We only include fields managed by the user, not auto-managed timestamps
    protected $allowedFields = ['name', 'description', 'is_active']; 

    // Define the timestamp field names for clarity and robustness
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    // Add other CI4 properties for robustness (optional, but recommended)
    protected $useSoftDeletes = false; 
    
    protected $validationRules = [
        'name'        => 'required|min_length[3]|max_length[255]',
        'description' => 'permit_empty',
    ];
}

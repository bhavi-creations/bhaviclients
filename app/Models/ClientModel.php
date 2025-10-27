<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table = 'clients';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    /**
     * Fields allowed for mass assignment.
     */
    protected $allowedFields = [
        'user_id',             // Foreign Key to the users table
        'client_manager_id',   // ← ADD THIS LINE
        'name',                // Company Name
        'owner_first_name',
        'owner_last_name',
        'email',               // NEW: Added Email
        'phone',               // NEW: Added Phone
        'role_id',             // NEW: Added Role ID (must be 3)
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Updated validation rules to include the new fields
    protected $validationRules = [
        'user_id'          => 'permit_empty|integer',
        'client_manager_id'=> 'permit_empty|integer', // ← ADD THIS LINE TOO (optional)
        'name'             => 'required|min_length[3]|max_length[255]',
        'owner_first_name' => 'required|min_length[2]|max_length[100]',
        'owner_last_name'  => 'required|min_length[2]|max_length[100]',
        'email'            => 'required|valid_email',
        'phone'            => 'required|min_length[10]|max_length[20]',
        'role_id'          => 'required|integer',
    ];

    protected $validationMessages = [];
    protected $skipValidation    = false;
}

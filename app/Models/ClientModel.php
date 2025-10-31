<?php
// C:\xampp\htdocs\bhaviclients\app\Models\ClientModel.php

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
        'user_id',
        'client_manager_id',
        'name',
        'owner_first_name',
        'owner_last_name',
        'email',
        'phone',
        'manager_name',
        'manager_phone',
        'reference',
        'started_date',
        'remarks',
        'role_id',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Updated validation rules to include the new fields
    protected $validationRules = [
        'user_id'          => 'permit_empty|integer',
        'client_manager_id'=> 'permit_empty|integer',
        'name'             => 'required|min_length[3]|max_length[255]',
        'owner_first_name' => 'required|min_length[2]|max_length[100]',
        'owner_last_name'  => 'required|min_length[2]|max_length[100]',
        'email'            => 'required|valid_email',
        'phone'            => 'required|min_length[10]|max_length[20]',
        'manager_name'     => 'permit_empty|max_length[100]',
        'manager_phone'    => 'permit_empty|max_length[20]',
        'reference'        => 'permit_empty|max_length[255]',
        'started_date'     => 'permit_empty|valid_date',
        'remarks'          => 'permit_empty',
        'role_id'          => 'required|integer',
    ];

    protected $validationMessages = [];
    protected $skipValidation    = false;
}

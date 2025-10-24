<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table          = 'users';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    // CRITICAL FIX: Removed 'address' from allowedFields as it is not in the 'users' table.
    protected $allowedFields    = [
        'role_id',
        'first_name',
        'last_name',
        'company_name',
        'username',
        'password',
        'phone',
        'email',
        'department_id',
        'employee_id',
        'client_id',
    ];

    // Dates
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    // Callbacks
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hashes the password before it is saved to the database.
     */
    protected function hashPassword(array $data)
    {
        // Only hash if password field is present and not empty
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } else {
            // Prevent re-hashing or overwriting blank passwords
            unset($data['data']['password']);
        }
        return $data;
    }
}

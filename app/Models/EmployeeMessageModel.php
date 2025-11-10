<?php
namespace App\Models;

use CodeIgniter\Model;

class EmployeeMessageModel extends Model
{
    protected $table = 'employee_messages';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'department_id',
        'employee_id',
        'sender_id',
        'sender_role_id',
        'message',
        'created_at'
    ];
    protected $useTimestamps = false;
}

<?php

namespace App\Models;
use CodeIgniter\Model;

class MaintenanceModel extends Model
{
    protected $table      = 'maintenance';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'client_id', 'title', 'description', 'file_uploads', 'remarks'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}

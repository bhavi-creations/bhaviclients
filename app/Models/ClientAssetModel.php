<?php
// C:\xampp\htdocs\bhaviclients\app\Models\ClientAssetModel.php

namespace App\Models;

use CodeIgniter\Model;

class ClientAssetModel extends Model
{
    protected $table = 'client_assets';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'client_id',
        'logo_file',
        'template_files',
        'social_media',
        'remarks',
        'uploaded_by',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = false;
    protected $returnType = 'array';
}

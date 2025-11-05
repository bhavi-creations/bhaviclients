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
        'logo_file',      // Old field - keep for backward compatibility
        'logo_png',       // NEW
        'logo_jpg',       // NEW
        'logo_psd',       // NEW
        'logo_pdf',       // NEW
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

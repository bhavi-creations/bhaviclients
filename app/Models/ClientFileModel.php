<?php
namespace App\Models;

use CodeIgniter\Model;

class ClientFileModel extends Model
{
    protected $table = 'client_files';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'client_id',
        'file_name',      // Saved file name on disk (random/generated)
        'original_name',  // Original uploaded file name (for display)
        'file_type',
        'file_size',
        'uploaded_by',    // ← ADD THIS LINE
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'uploaded_at';
    protected $updatedField  = false;

    protected $validationRules = [
        'client_id'    => 'required|integer',
        'file_name'    => 'required|max_length[255]',
        'original_name'=> 'required|max_length[255]',
        'file_type'    => 'required|max_length[100]',
        'file_size'    => 'required|integer',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
}

<?php
// C:\xampp\htdocs\bhaviclients\app\Models\AdminClientUploadModel.php

namespace App\Models;

use CodeIgniter\Model;

class AdminClientUploadModel extends Model
{
    protected $table = 'admin_client_uploads';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'client_id',
        'file_name',
        'original_name',
        'file_size',
        'file_type',
        'file_extension',
        'category',
        'description',
        'uploaded_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'uploaded_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'client_id' => 'required|integer',
        'file_name' => 'required|max_length[255]',
        'original_name' => 'required|max_length[255]',
        'file_size' => 'required|integer',
        'file_type' => 'required|max_length[100]',
        'file_extension' => 'required|max_length[10]',
        'category' => 'permit_empty|in_list[document,image,spreadsheet,presentation,other]',
        'description' => 'permit_empty',
        'uploaded_by' => 'required|integer'
    ];

    /**
     * Get all files for a client with uploader info
     */
    public function getClientFilesWithUploader($clientId)
    {
        return $this->select('admin_client_uploads.*, 
                            users.first_name as uploader_first_name, 
                            users.last_name as uploader_last_name')
                    ->join('users', 'users.id = admin_client_uploads.uploaded_by', 'left')
                    ->where('admin_client_uploads.client_id', $clientId)
                    ->orderBy('admin_client_uploads.uploaded_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get all files with client and uploader info
     */
    public function getAllFilesWithDetails()
    {
        return $this->select('admin_client_uploads.*, 
                            clients.name as client_name,
                            users.first_name as uploader_first_name, 
                            users.last_name as uploader_last_name')
                    ->join('clients', 'clients.id = admin_client_uploads.client_id', 'left')
                    ->join('users', 'users.id = admin_client_uploads.uploaded_by', 'left')
                    ->orderBy('admin_client_uploads.uploaded_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get file count by client
     */
    public function getFileCountByClient($clientId)
    {
        return $this->where('client_id', $clientId)->countAllResults();
    }

    /**
     * Get total storage used by client (in bytes)
     */
    public function getTotalStorageByClient($clientId)
    {
        $result = $this->selectSum('file_size')
                       ->where('client_id', $clientId)
                       ->first();
        
        return $result['file_size'] ?? 0;
    }
}

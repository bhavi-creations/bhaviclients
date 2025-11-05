<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\AdminClientUpload.php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\AdminClientUploadModel;
use App\Models\ClientModel;

class AdminClientUpload extends Controller
{
    protected $uploadModel;
    protected $clientModel;
    protected $db;

    public function __construct()
    {
        $this->uploadModel = new AdminClientUploadModel();
        $this->clientModel = new ClientModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url', 'filesystem']);

        // Access control: Only allow admins (role_id = 1, 5)
        if (!in_array(session()->get('role_id'), [1, 5])) {
            header('Location: ' . base_url('dashboard'));
            exit;
        }
    }

    /**
     * List all uploaded files
     */
    public function index()
    {
        $files = $this->uploadModel->getAllFilesWithDetails();

        return view('admin_client_uploads/index', [
            'title' => 'Client File Uploads',
            'files' => $files
        ]);
    }

    /**
     * Upload form for specific client
     */
    public function upload($clientId = null)
    {
        $clients = $this->clientModel->findAll();

        $selectedClient = null;
        if ($clientId) {
            $selectedClient = $this->clientModel->find($clientId);
        }

        return view('admin_client_uploads/upload', [
            'title' => 'Upload Files to Client',
            'clients' => $clients,
            'selectedClient' => $selectedClient,
            'validation' => \Config\Services::validation()
        ]);
    }

    /**
     * Store uploaded files
     */
    public function store()
    {
        $input = $this->request->getPost();

        $rules = [
            'client_id' => 'required|integer',
            'category' => 'permit_empty|in_list[document,image,spreadsheet,presentation,other]',
            'description' => 'permit_empty|max_length[500]',
            'files' => 'uploaded[files]|max_size[files,10240]' // 10MB max per file
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('validation', $this->validator)
                           ->with('error', 'Validation failed.');
        }

        $clientId = $input['client_id'];
        $category = $input['category'] ?? 'other';
        $description = $input['description'] ?? null;

        $uploadPath = FCPATH . 'uploads/admin_client_files/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $files = $this->request->getFiles();
        $uploadedCount = 0;
        $errors = [];

        if (isset($files['files'])) {
            foreach ($files['files'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    try {
                        $originalName = $file->getName();
                        $extension = $file->getExtension();
                        $fileName = time() . '_' . uniqid() . '.' . $extension;
                        
                        $file->move($uploadPath, $fileName);

                        $fileData = [
                            'client_id' => $clientId,
                            'file_name' => $fileName,
                            'original_name' => $originalName,
                            'file_size' => $file->getSize(),
                            'file_type' => $file->getMimeType(),
                            'file_extension' => $extension,
                            'category' => $category,
                            'description' => $description,
                            'uploaded_by' => session()->get('user_id')
                        ];

                        if ($this->uploadModel->insert($fileData)) {
                            $uploadedCount++;
                        }
                    } catch (\Exception $e) {
                        $errors[] = $originalName . ': ' . $e->getMessage();
                        log_message('error', 'File upload error: ' . $e->getMessage());
                    }
                }
            }
        }

        if ($uploadedCount > 0) {
            $message = $uploadedCount . ' file(s) uploaded successfully!';
            if (!empty($errors)) {
                $message .= ' Errors: ' . implode(', ', $errors);
            }
            return redirect()->to(base_url('admin-client-uploads'))
                           ->with('message', $message);
        } else {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to upload files. ' . implode(', ', $errors));
        }
    }

    /**
     * View files for specific client
     */
    public function clientFiles($clientId)
    {
        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return redirect()->to(base_url('admin-client-uploads'))
                           ->with('error', 'Client not found.');
        }

        $files = $this->uploadModel->getClientFilesWithUploader($clientId);
        $totalStorage = $this->uploadModel->getTotalStorageByClient($clientId);

        return view('admin_client_uploads/client_files', [
            'title' => 'Files for ' . $client['name'],
            'client' => $client,
            'files' => $files,
            'totalStorage' => $totalStorage
        ]);
    }

    /**
     * Download file
     */
    public function download($fileId)
    {
        $file = $this->uploadModel->find($fileId);
        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filePath = FCPATH . 'uploads/admin_client_files/' . $file['file_name'];
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        $binary = file_get_contents($filePath);
        
        return $this->response
                    ->setHeader('Content-Type', 'application/octet-stream')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $file['original_name'] . '"')
                    ->setBody($binary);
    }

    /**
     * Delete file
     */
    public function delete($fileId)
    {
        $file = $this->uploadModel->find($fileId);
        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filePath = FCPATH . 'uploads/admin_client_files/' . $file['file_name'];
        
        // Delete physical file
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete database record``
        if ($this->uploadModel->delete($fileId)) {
            return redirect()->back()->with('message', 'File deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to delete file.');
        }
    }
}

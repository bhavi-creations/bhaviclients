<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\EmployeeClientAssets.php

namespace App\Controllers;

use App\Controllers\BaseController;

class EmployeeClientAssets extends BaseController
{
    protected $db;
    protected $session;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();
        helper(['url', 'form']);

        // Access control - Only Employees (role_id = 2)
        if (session()->get('role_id') != 2) {
            header('Location: ' . base_url('dashboard'));
            exit;
        }
    }

    /**
     * List all client assets for employees (view-only)
     */
    public function index()
    {
        $assets = $this->db->table('client_assets')
            ->select('client_assets.*, 
                     clients.name as client_name,
                     clients.email as client_email,
                     users.first_name as uploaded_by_name,
                     users.last_name as uploaded_by_lastname')
            ->join('clients', 'clients.id = client_assets.client_id', 'left')
            ->join('users', 'users.id = client_assets.uploaded_by', 'left')
            ->orderBy('client_assets.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Client Assets',
            'assets' => $assets
        ];

        return view('employee/client_assets/index', $data);
    }

    /**
     * View client asset details
     */
    public function view($id)
    {
        $asset = $this->db->table('client_assets')
            ->select('client_assets.*, 
                     clients.name as client_name,
                     clients.email as client_email,
                     users.first_name as uploaded_by_name,
                     users.last_name as uploaded_by_lastname')
            ->join('clients', 'clients.id = client_assets.client_id', 'left')
            ->join('users', 'users.id = client_assets.uploaded_by', 'left')
            ->where('client_assets.id', $id)
            ->get()
            ->getRowArray();

        if (!$asset) {
            session()->setFlashdata('error', 'Asset not found.');
            return redirect()->to(base_url('employee-client-assets'));
        }

        // Decode JSON fields
        $asset['template_files_array'] = !empty($asset['template_files']) ? json_decode($asset['template_files'], true) : [];
        $asset['social_media_array'] = !empty($asset['social_media']) ? json_decode($asset['social_media'], true) : [];

        $data = [
            'title' => 'View Client Assets',
            'asset' => $asset
        ];

        return view('employee/client_assets/view', $data);
    }

    /**
     * Download file
     */
    public function downloadFile($type, $filename)
    {
        $basePath = FCPATH . 'uploads/client_assets/';

        if ($type == 'logo') {
            $filePath = $basePath . 'logos/' . $filename;
        } elseif ($type == 'template') {
            $filePath = $basePath . 'templates/' . $filename;
        } else {
            session()->setFlashdata('error', 'Invalid file type.');
            return redirect()->back();
        }

        if (!file_exists($filePath)) {
            session()->setFlashdata('error', 'File not found.');
            return redirect()->back();
        }

        // Fixed download method
        $binary = file_get_contents($filePath);

        return $this->response
            ->setHeader('Content-Type', 'application/octet-stream')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($binary);
    }
}

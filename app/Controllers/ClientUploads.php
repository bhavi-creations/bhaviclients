<?php

namespace App\Controllers;

use App\Models\ClientFileModel;
use App\Models\ClientModel;

class ClientUploads extends BaseController
{
    protected $clientFileModel;
    protected $clientModel;

    public function __construct()
    {
        $this->clientFileModel = new ClientFileModel();
        $this->clientModel = new ClientModel();
        helper(['form', 'url']);
    }

    /**
     * List clients who have uploaded files (for admin/manager)
     * Only counts files uploaded by clients (not admin-uploaded excels)
     */
    public function index()
    {
        // Get clients with client-uploaded files only
        $clientsWithFiles = $this->clientFileModel
            ->select(
                'clients.id, clients.name, clients.owner_first_name, clients.owner_last_name, clients.email, clients.phone,
                COUNT(client_files.id) as file_count,
                MAX(client_files.uploaded_at) as last_upload'
            )
            ->join('clients', 'clients.id = client_files.client_id', 'left')
            ->groupStart()
            ->where('client_files.uploaded_by', 'client')
            ->orWhere('client_files.uploaded_by = clients.id')
            ->groupEnd()
            ->groupBy('clients.id')
            ->orderBy('last_upload', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Client Uploaded Files',
            'clients' => $clientsWithFiles
        ];
        return view('client_uploads/index', $data);
    }

    /**
     * Show all files uploaded by a specific client (filters by client only, not by admin)
     */
    public function byClient($clientId)
    {
        $client = $this->clientModel->find($clientId);

        if (!$client) {
            session()->setFlashdata('error', 'Client not found.');
            return redirect()->to(base_url('client-uploads'));
        }

        // Get filter params
        $fromDate = $this->request->getGet('from_date');
        $toDate = $this->request->getGet('to_date');

        $builder = $this->clientFileModel
            ->where('client_id', $clientId)
            ->groupStart()
            ->where('uploaded_by', 'client')
            ->orWhere('uploaded_by', $clientId)
            ->groupEnd();


        if ($fromDate) {
            $builder->where('uploaded_at >=', $fromDate . ' 00:00:00');
        }
        if ($toDate) {
            $builder->where('uploaded_at <=', $toDate . ' 23:59:59');
        }

        $files = $builder->orderBy('uploaded_at', 'DESC')->findAll();

        $data = [
            'title' => 'Files from ' . $client['name'],
            'client' => $client,
            'files' => $files,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ];
        return view('client_uploads/by_client', $data);
    }

    /**
     * Download a single client-uploaded file
     */
    public function download($fileId)
    {
        $file = $this->clientFileModel->find($fileId);

        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filePath = FCPATH . 'uploads/client_uploads/' . $file['file_name'];

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        // Return the file as download response
        $binary = readfile($filePath);

        return $this->response
            ->setHeader('Content-Type', 'application/octet-stream')
            ->setHeader('Content-disposition', 'attachment; filename="' . $file['original_name'] . '"')
            ->setHeader('Content-Length', filesize($filePath))
            ->setBody($binary);
    }




    /**
     * Delete a client-uploaded file (allowed for admin, admin manager - role checks should be at controller/route)
     */
    public function delete($fileId)
    {
        $file = $this->clientFileModel->find($fileId);

        if (!$file || $file['uploaded_by'] !== 'client') {
            session()->setFlashdata('error', 'File not found or access denied.');
            return redirect()->back();
        }

        $filePath = FCPATH . 'uploads/client_uploads/' . $file['file_name'];

        // Delete physical file if exists
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $this->clientFileModel->delete($fileId);

        session()->setFlashdata('success', 'File deleted successfully.');
        return redirect()->back();
    }
}

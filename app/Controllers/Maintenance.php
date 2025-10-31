<?php

namespace App\Controllers;

use App\Models\MaintenanceModel;
use App\Models\ClientModel;

class Maintenance extends BaseController
{
    protected $maintenanceModel;
    protected $clientModel;

    public function __construct()
    {
        $this->maintenanceModel = new MaintenanceModel();
        $this->clientModel = new ClientModel();
        helper(['form', 'url', 'filesystem']);
    }

    // Admin: List all, Client: List their own
    public function index()
    {
        $roleId = session()->get('role_id');
        if ($roleId != 1) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Unauthorized.');
        }

        // Get only clients who have at least one maintenance record
        $builder = $this->maintenanceModel
            ->select('clients.id, clients.name, clients.owner_first_name, clients.owner_last_name, clients.email, clients.phone, COUNT(maintenance.id) as record_count')
            ->join('clients', 'clients.id = maintenance.client_id')
            ->groupBy('clients.id')
            ->orderBy('clients.name', 'ASC');

        $clients = $builder->findAll();

        return view('maintenance/index', [
            'title' => 'Project Details',
            'clients' => $clients
        ]);
    }

    public function create()
    {
        $this->restrictAdmin();
        $clients = $this->clientModel->findAll();
        return view('maintenance/create', [
            'title' => 'Create Project Details',
            'clients' => $clients
        ]);
    }

    public function store()
    {
        $this->restrictAdmin();
        
        if (!$this->validate([
            'client_id' => 'required|integer',
            'title' => 'required|min_length[2]|max_length[255]',
            'description' => 'permit_empty',
            'remarks' => 'permit_empty',
            'files' => [
                'rules' => 'permit_empty',
                'errors' => [
                    'mime_in' => 'Invalid file type. Only images, PDFs, and documents allowed.',
                    'max_size' => 'File size must not exceed 10MB.'
                ]
            ]
        ])) {
            return redirect()->back()->withInput()->with('error', implode(', ', $this->validator->getErrors()));
        }

        $data = $this->request->getPost();
        $uploadedFiles = [];

        // Handle multiple file uploads
        $files = $this->request->getFiles();
        if (isset($files['files'])) {
            foreach ($files['files'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(FCPATH . 'uploads/maintenance/', $newName);
                    $uploadedFiles[] = $newName;
                }
            }
        }

        $this->maintenanceModel->save([
            'client_id' => $data['client_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'remarks' => $data['remarks'] ?? null,
            'file_uploads' => !empty($uploadedFiles) ? json_encode($uploadedFiles) : null,
        ]);

        return redirect()->to(base_url('maintenance'))->with('success', 'Project details added successfully!');
    }

    public function edit($id)
    {
        $this->restrictAdmin();
        $record = $this->maintenanceModel->find($id);
        if (!$record) return redirect()->to(base_url('maintenance'))->with('error', 'Record not found');
        $clients = $this->clientModel->findAll();
        return view('maintenance/edit', [
            'title' => 'Edit Project Details',
            'record' => $record,
            'clients' => $clients
        ]);
    }

    public function update($id)
    {
        $this->restrictAdmin();
        
        $record = $this->maintenanceModel->find($id);
        if (!$record) {
            return redirect()->to(base_url('maintenance'))->with('error', 'Record not found');
        }

        if (!$this->validate([
            'client_id' => 'required|integer',
            'title' => 'required|min_length[2]|max_length[255]',
            'description' => 'permit_empty',
            'remarks' => 'permit_empty',
            'files' => [
                'rules' => 'permit_empty',
                'errors' => [
                    'mime_in' => 'Invalid file type. Only images, PDFs, and documents allowed.',
                    'max_size' => 'File size must not exceed 10MB.'
                ]
            ]
        ])) {
            return redirect()->back()->withInput()->with('error', implode(', ', $this->validator->getErrors()));
        }

        $data = $this->request->getPost();
        
        // Get existing files
        $existingFiles = !empty($record['file_uploads']) ? json_decode($record['file_uploads'], true) : [];
        $uploadedFiles = $existingFiles;

        // Handle multiple file uploads
        $files = $this->request->getFiles();
        if (isset($files['files'])) {
            foreach ($files['files'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(FCPATH . 'uploads/maintenance/', $newName);
                    $uploadedFiles[] = $newName;
                }
            }
        }

        $this->maintenanceModel->update($id, [
            'client_id' => $data['client_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'remarks' => $data['remarks'] ?? null,
            'file_uploads' => !empty($uploadedFiles) ? json_encode($uploadedFiles) : null,
        ]);

        return redirect()->to(base_url('maintenance/client/' . $data['client_id']))->with('success', 'Project details updated successfully!');
    }

    public function delete($id)
    {
        $this->restrictAdmin();
        
        $record = $this->maintenanceModel->find($id);
        if ($record) {
            // Delete associated files
            if (!empty($record['file_uploads'])) {
                $files = json_decode($record['file_uploads'], true);
                if (is_array($files)) {
                    foreach ($files as $file) {
                        $filePath = FCPATH . 'uploads/maintenance/' . $file;
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
            }
            $this->maintenanceModel->delete($id);
        }
        
        return redirect()->back()->with('success', 'Project details deleted successfully!');
    }

    public function deleteFile($id, $filename)
    {
        $this->restrictAdmin();
        
        $record = $this->maintenanceModel->find($id);
        if (!$record) {
            return redirect()->back()->with('error', 'Record not found');
        }

        $files = !empty($record['file_uploads']) ? json_decode($record['file_uploads'], true) : [];
        
        // Remove file from array
        $files = array_filter($files, function($file) use ($filename) {
            return $file !== $filename;
        });

        // Delete physical file
        $filePath = FCPATH . 'uploads/maintenance/' . $filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Update database
        $this->maintenanceModel->update($id, [
            'file_uploads' => !empty($files) ? json_encode(array_values($files)) : null
        ]);

        return redirect()->back()->with('success', 'File deleted successfully!');
    }

    public function downloadFile($id, $filename)
    {
        $record = $this->maintenanceModel->find($id);
        if (!$record) {
            return redirect()->back()->with('error', 'Record not found');
        }

        $filePath = FCPATH . 'uploads/maintenance/' . $filename;
        if (file_exists($filePath)) {
            return $this->response->download($filePath, null);
        }

        return redirect()->back()->with('error', 'File not found');
    }

    private function restrictAdmin()
    {
        if (session()->get('role_id') != 1) {
            exit('Unauthorized.');
        }
    }

    public function view($id)
    {
        $roleId = session()->get('role_id');
        $user = session()->get();
        $record = $this->maintenanceModel->find($id);
        
        if (!$record) return redirect()->to(base_url('maintenance'))->with('error', 'Not found');
        
        // Get client info
        $client = $this->clientModel->find($record['client_id']);
        
        if (
            $roleId == 1 ||
            ($roleId == 3 && $user['client_id'] == $record['client_id']) ||
            ($roleId == 4 && $user['client_id'] == $record['client_id'])
        ) {
            return view('maintenance/view', [
                'title' => 'Project Details',
                'record' => $record,
                'client' => $client
            ]);
        } else {
            return redirect()->to(base_url('dashboard'))->with('error', 'Unauthorized.');
        }
    }

    public function client($clientId)
    {
        $roleId = session()->get('role_id');
        if ($roleId != 1) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Unauthorized.');
        }

        $client = $this->clientModel->find($clientId);
        if (!$client) return redirect()->to(base_url('maintenance'))->with('error', 'Client not found');

        $records = $this->maintenanceModel
            ->where('client_id', $clientId)
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('maintenance/client_detail', [
            'title' => 'Project Details for ' . $client['name'],
            'client' => $client,
            'records' => $records
        ]);
    }

    public function clientView()
    {
        $roleId = session()->get('role_id');
        $user = session()->get();

        // Get the correct client id
        if ($roleId == 3 || $roleId == 4) {
            $clientId = $user['client_id'];
            $client = $this->clientModel->find($clientId);
            if (!$client) {
                return redirect()->to(base_url('dashboard'))->with('error', 'Client not found!');
            }
            $records = $this->maintenanceModel
                ->where('client_id', $clientId)
                ->orderBy('id', 'DESC')
                ->findAll();

            return view('maintenance/client_panel', [
                'title' => 'My Project Details',
                'client' => $client,
                'records' => $records
            ]);
        }
        return redirect()->to(base_url('dashboard'))->with('error', 'Unauthorized!');
    }
}

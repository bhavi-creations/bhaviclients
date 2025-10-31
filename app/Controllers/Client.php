<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\Client.php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ClientModel;
use App\Models\ClientFileModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class Client extends Controller
{
    use ResponseTrait;

    protected $clientModel;
    protected $clientFileModel;
    protected $userModel;
    protected $validation;
    protected $db;
    protected $clientRoleId = 3; // Client role ID

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->clientFileModel = new ClientFileModel();
        $this->userModel = new UserModel();
        $this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);

        // Access control: Only allow admins and admin managers
        if (!in_array(session()->get('role_id'), [1, 5])) {
            if (!function_exists('redirect')) {
                header('Location: ' . base_url('dashboard'));
                exit;
            } else {
                redirect()->to(base_url('dashboard'))->send();
                exit;
            }
        }
    }

    public function index()
    {
        $data['clients'] = $this->clientModel->findAll();
        $data['title'] = 'Client List';
        return view('client/index', $data);
    }

    public function create()
    {
        $data['title'] = 'Add New Client';
        $data['validation'] = $this->validation;
        return view('client/create', $data);
    }

    public function store()
    {
        $input = $this->request->getPost();

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'owner_first_name' => 'required|min_length[2]|max_length[100]',
            'owner_last_name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|max_length[255]',
            'phone' => 'required|min_length[10]|max_length[20]',
            'manager_name' => 'permit_empty|max_length[100]',
            'manager_phone' => 'permit_empty|max_length[20]',
            'reference' => 'permit_empty|max_length[255]',
            'started_date' => 'permit_empty',
            'remarks' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->listErrors());
        }

        $this->db->transStart();

        try {
            // ==========================================
            // 1. CREATE CLIENT USER (Role 3)
            // ==========================================
            $clientUserData = [
                'role_id' => $this->clientRoleId, // Role 3 (Client)
                'first_name' => $input['owner_first_name'],
                'last_name' => $input['owner_last_name'],
                'email' => $input['email'],
                'username' => $input['email'], // Email as username
                'phone' => $input['phone'],
                'password' => $input['phone'], // Plain password - UserModel will hash it
            ];

            $clientUserId = $this->userModel->insert($clientUserData);
            if (!$clientUserId) {
                throw new \Exception('Client user insert failed: ' . json_encode($this->userModel->errors()));
            }
            $clientUserId = (int)$clientUserId;

            // ==========================================
            // 2. CREATE CLIENT RECORD
            // ==========================================
            $clientData = [
                'user_id' => $clientUserId,
                'name' => $input['name'],
                'owner_first_name' => $input['owner_first_name'],
                'owner_last_name' => $input['owner_last_name'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'manager_name' => $input['manager_name'] ?? null,
                'manager_phone' => $input['manager_phone'] ?? null,
                'reference' => $input['reference'] ?? null,
                'started_date' => !empty($input['started_date']) ? $input['started_date'] : null,
                'remarks' => $input['remarks'] ?? null,
                'role_id' => $this->clientRoleId,
            ];

            if (!$this->clientModel->insert($clientData)) {
                throw new \Exception('Client insert failed: ' . json_encode($this->clientModel->errors()));
            }

            $clientId = $this->clientModel->getInsertID();

            // Update client user with client_id and company_name
            $updateClientUserData = [
                'client_id' => $clientId,
                'company_name' => $input['name'],
            ];

            if ($this->userModel->update($clientUserId, $updateClientUserData) === false) {
                throw new \Exception('Client user update failed: ' . json_encode($this->userModel->errors()));
            }

            // ==========================================
            // 3. CREATE CLIENT MANAGER USER (Role 4) - IF MANAGER DETAILS PROVIDED
            // ==========================================
            $clientManagerUserId = null;

            if (!empty($input['manager_name']) && !empty($input['manager_phone'])) {
                // Split manager name into first and last name
                $managerName = trim($input['manager_name']);
                $nameParts = explode(' ', $managerName, 2);
                $managerFirstName = $nameParts[0] ?? 'Manager';
                $managerLastName = $nameParts[1] ?? '';

                // Create unique email for manager by prefixing with "manager."
                $managerEmail = 'manager.' . $input['email'];
                $managerUsername = 'manager_' . $input['email'];

                $clientManagerUserData = [
                    'role_id' => 4, // Role 4 (Client Manager)
                    'first_name' => $managerFirstName,
                    'last_name' => $managerLastName,
                    'email' => $managerEmail, // Unique email
                    'username' => $managerUsername, // Unique username
                    'phone' => $input['manager_phone'],
                    'password' => $input['manager_phone'], // Plain password - UserModel will hash it
                    'client_id' => $clientId,
                    'company_name' => $input['name'],
                ];

                $clientManagerUserId = $this->userModel->insert($clientManagerUserData);
                if (!$clientManagerUserId) {
                    throw new \Exception('Client Manager user insert failed: ' . json_encode($this->userModel->errors()));
                }
                $clientManagerUserId = (int)$clientManagerUserId;

                // Update client table with client_manager_id
                $this->clientModel->update($clientId, ['client_manager_id' => $clientManagerUserId]);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction commit failed.');
            }

            // Build success message with credentials
            $message = '<div class="alert alert-success">';
            $message .= '<h5><i class="fas fa-check-circle"></i> Client Created Successfully!</h5><hr>';
            $message .= '<strong>📋 Login Credentials:</strong><br><br>';

            $message .= '<strong>Client Login:</strong><br>';
            $message .= '<table class="table table-sm table-bordered" style="max-width: 500px;">';
            $message .= '<tr><td><strong>Username:</strong></td><td><code>' . $input['email'] . '</code></td></tr>';
            $message .= '<tr><td><strong>Password:</strong></td><td><code>' . $input['phone'] . '</code></td></tr>';
            $message .= '</table>';

            if ($clientManagerUserId) {
                $message .= '<strong>Manager Login:</strong><br>';
                $message .= '<table class="table table-sm table-bordered" style="max-width: 500px;">';
                $message .= '<tr><td><strong>Username:</strong></td><td><code>manager_' . $input['email'] . '</code></td></tr>';
                $message .= '<tr><td><strong>Password:</strong></td><td><code>' . $input['manager_phone'] . '</code></td></tr>';
                $message .= '</table>';
            }

            $message .= '<small class="text-muted"><i class="fas fa-info-circle"></i> Users can change their passwords after first login.</small>';
            $message .= '</div>';

            session()->setFlashdata('message', $message);
            return redirect()->to(base_url('client'));
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Client creation error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Client creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }





    public function edit($id = null)
    {
        $client = $this->clientModel->find($id);

        if (!$client) {
            session()->setFlashdata('error', 'Client not found for editing.');
            return redirect()->to(base_url('client'));
        }

        $data['title'] = 'Edit Client';
        $data['client'] = $client;
        $data['validation'] = $this->validation;

        return view('client/edit', $data);
    }

    public function update($id = null)
    {
        $client = $this->clientModel->find($id);
        if (!$client) {
            session()->setFlashdata('error', 'Client not found.');
            return redirect()->to(base_url('client'));
        }

        $input = $this->request->getPost();

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'owner_first_name' => 'required|min_length[2]|max_length[100]',
            'owner_last_name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|max_length[255]',
            'phone' => 'required|min_length[10]|max_length[20]',
            'manager_name' => 'permit_empty|max_length[100]',
            'manager_phone' => 'permit_empty|max_length[20]',
            'reference' => 'permit_empty|max_length[255]',
            'started_date' => 'permit_empty',
            'remarks' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return view('client/edit', [
                'title'      => 'Edit Client',
                'client'     => $client,
                'validation' => $this->validator,
            ]);
        }

        $this->db->transStart();

        try {
            $clientData = [
                'name' => $input['name'],
                'owner_first_name' => $input['owner_first_name'],
                'owner_last_name' => $input['owner_last_name'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'manager_name' => $input['manager_name'] ?? null,
                'manager_phone' => $input['manager_phone'] ?? null,
                'reference' => $input['reference'] ?? null,
                'started_date' => !empty($input['started_date']) ? $input['started_date'] : null,
                'remarks' => $input['remarks'] ?? null,
            ];

            $userData = [
                'first_name' => $input['owner_first_name'],
                'last_name' => $input['owner_last_name'],
                'email' => $input['email'],
                'username' => $input['email'],
                'phone' => $input['phone'],
                'password' => $input['phone'],
                'client_id' => $id,
                'company_name' => $input['name'],
            ];

            $userUpdate = $this->userModel->update($client['user_id'], $userData);
            $clientUpdate = $this->clientModel->update($id, $clientData);

            if (!$userUpdate || !$clientUpdate) {
                throw new \Exception('Update failed: ' . json_encode(array_merge(
                    $this->userModel->errors(),
                    $this->clientModel->errors()
                )));
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction commit failed.');
            }

            session()->setFlashdata('message', 'Client updated successfully!');
            return redirect()->to(base_url('client'));
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Client update failed: ' . $e->getMessage());
            session()->setFlashdata('error', 'Client update failed: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }


    public function view($id = null)
    {
        $client = $this->clientModel->find($id);

        if (!$client) {
            session()->setFlashdata('error', 'Client not found.');
            return redirect()->to(base_url('client'));
        }

        $data['title'] = 'Client Details';
        $data['client'] = $client;

        return view('client/view', $data);
    }

    public function delete($id)
    {
        $client = $this->clientModel->find($id);
        if (!$client) {
            return redirect()->to(base_url('client'))->with('error', 'Client not found.');
        }

        // Delete related files, users, or other cleanup as needed here

        $this->clientModel->delete($id);

        return redirect()->to(base_url('client'))->with('message', 'Client deleted successfully.');
    }

    // Custom validation callbacks

    public function validateUniqueEmail(string $email, ?string $fields, array $data): bool
    {
        $clientId = $data['client_id'] ?? null;
        $userId = $data['user_id'] ?? null;

        $clientExists = $this->clientModel->where('email', $email)
            ->where('id !=', $clientId)
            ->countAllResults() > 0;

        $userExists = $this->userModel->where('email', $email)
            ->where('id !=', $userId)
            ->countAllResults() > 0;

        return !($clientExists || $userExists);
    }

    public function validateUniquePhone(string $phone, ?string $fields, array $data): bool
    {
        $userId = $data['user_id'] ?? null;

        $phoneExists = $this->userModel->where('phone', $phone)
            ->where('id !=', $userId)
            ->countAllResults() > 0;

        return !$phoneExists;
    }

    public function files($clientId)
    {
        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return redirect()->to(base_url('client'))->with('error', 'Client not found.');
        }

        // Only show ADMIN-uploaded files (exclude client self-uploads)
        $clientFiles = $this->clientFileModel
            ->where('client_id', $clientId)
            ->where('uploaded_by !=', 'client')
            ->where('uploaded_by !=', $clientId)
            ->orderBy('uploaded_at', 'DESC')
            ->findAll();

        return view('client/files', [
            'title' => 'Client Files',
            'client' => $client,
            'clientFiles' => $clientFiles,
        ]);
    }


    public function upload($clientId)
    {
        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return redirect()->to(base_url('client'))->with('error', 'Client not found.');
        }

        $files = $this->request->getFiles('client_files');

        if (empty($files)) {
            return redirect()->back()->with('error', 'No files selected.');
        }

        $uploadPath = FCPATH . 'uploads/clients/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        foreach ($files as $file) {
            if (is_array($file)) {
                foreach ($file as $singleFile) {
                    $this->processFileUpload($singleFile, $uploadPath, $clientId);
                }
            } else {
                $this->processFileUpload($file, $uploadPath, $clientId);
            }
        }

        return redirect()->to(base_url('client/files/' . $clientId))
            ->with('message', 'Files uploaded successfully.');
    }

    private function processFileUpload($file, $uploadPath, $clientId)
    {
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);

            $fileData = [
                'client_id' => $clientId,
                'file_name' => $newName,
                'original_name' => $file->getClientName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => 'admin', // Mark as admin upload
                'uploaded_at' => date('Y-m-d H:i:s'),
            ];

            $this->clientFileModel->insert($fileData);
        }
    }


    // Download file by ID
    public function downloadFile($fileId)
    {
        $file = $this->clientFileModel->find($fileId);
        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filePath = FCPATH . 'uploads/clients/' . $file['file_name'];

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        return $this->response->download($filePath, null)->setFileName($file['original_name']);
    }

    public function deleteFile($fileId)
    {
        $file = $this->clientFileModel->find($fileId);
        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filePath = FCPATH . 'uploads/clients/' . $file['file_name'];

        // Delete the physical file if exists
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete database record
        $this->clientFileModel->delete($fileId);

        return redirect()->back()->with('message', 'File deleted successfully.');
    }
}

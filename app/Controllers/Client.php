<?php

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
            'email' => 'required|valid_email|max_length[255]|validateUniqueEmail',
            'phone' => 'required|min_length[10]|max_length[20]|validateUniquePhone',
        ];

        // Register custom validation callbacks (label is null, third param is callable)
        $this->validation->setRule('validateUniqueEmail', null, [$this, 'validateUniqueEmail']);
        $this->validation->setRule('validateUniquePhone', null, [$this, 'validateUniquePhone']);

        $this->validation->setRules($rules);

        // For creation, no IDs needed
        $validationData = array_merge($input, [
            'client_id' => null,
            'user_id' => null,
        ]);

        if (!$this->validation->run($validationData)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validation);
        }

        $this->db->transStart();

        try {
            $userData = [
                'role_id' => $this->clientRoleId,
                'first_name' => $input['owner_first_name'],
                'last_name' => $input['owner_last_name'],
                'email' => $input['email'],
                'username' => $input['email'],
                'phone' => $input['phone'],
                'password' => $input['phone'],  // Use hashing in production
            ];

            $userId = $this->userModel->insert($userData);
            if (!$userId) {
                throw new \Exception('User insert failed: ' . json_encode($this->userModel->errors()));
            }
            $userId = (int)$userId;

            $clientData = [
                'user_id' => $userId,
                'name' => $input['name'],
                'owner_first_name' => $input['owner_first_name'],
                'owner_last_name' => $input['owner_last_name'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'role_id' => $this->clientRoleId,
            ];

            if (!$this->clientModel->insert($clientData)) {
                throw new \Exception('Client insert failed: ' . json_encode($this->clientModel->errors()));
            }

            $clientId = $this->clientModel->getInsertID();

            $updateUserData = [
                'client_id' => $clientId,
                'company_name' => $input['name'],
            ];

            $updateResult = $this->userModel->update($userId, $updateUserData);
            if ($updateResult === false) {
                throw new \Exception('User update with client data failed: ' . json_encode($this->userModel->errors()));
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction commit failed.');
            }

            session()->setFlashdata('message', 'Client and user created successfully!');
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

        // Validation rules with proper IDs in params
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'owner_first_name' => 'required|min_length[2]|max_length[100]',
            'owner_last_name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|max_length[255]|validateUniqueEmail[' . $id . ',' . $client['user_id'] . ']',
            'phone' => 'required|min_length[10]|max_length[20]|validateUniquePhone[' . $client['user_id'] . ']',
        ];

        $validationData = array_merge($input, [
            'client_id' => $id,
            'user_id'   => $client['user_id'],
        ]);

        if (!$this->validate($rules, $validationData)) {
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
            ];

            $userData = [
                'first_name' => $input['owner_first_name'],
                'last_name' => $input['owner_last_name'],
                'email' => $input['email'],
                'username' => $input['email'],
                'phone' => $input['phone'],
                'password' => $input['phone'], // hash in production
                'client_id' => $id,
                'company_name' => $input['name'],
            ];

            $userUpdate = $this->userModel->update($client['user_id'], $userData);
            $clientUpdate = $this->clientModel->update($id, $clientData);

            log_message('debug', 'User update result: ' . var_export($userUpdate, true));
            log_message('debug', 'Client update result: ' . var_export($clientUpdate, true));

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

        $clientFiles = $this->clientFileModel->where('client_id', $clientId)->findAll();

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

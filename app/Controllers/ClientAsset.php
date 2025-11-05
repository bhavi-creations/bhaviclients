<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\ClientAsset.php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClientAssetModel;
use App\Models\ClientModel;

class ClientAsset extends BaseController
{
    protected $assetModel;
    protected $clientModel;
    protected $db;
    protected $validation;

    public function __construct()
    {
        $this->assetModel = new ClientAssetModel();
        $this->clientModel = new ClientModel();
        $this->db = \Config\Database::connect();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url']);

        // Access control - Only Admin Manager (role_id = 5)
        if (session()->get('role_id') != 5) {
            header('Location: ' . base_url('dashboard'));
            exit;
        }
    }

    /**
     * List all client assets
     */
    public function index()
    {
        $assets = $this->db->table('client_assets')
            ->select('client_assets.*, 
                     clients.name as client_name,
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

        return view('client_assets/index', $data);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $clients = $this->clientModel->findAll();

        $data = [
            'title' => 'Upload Client Assets',
            'clients' => $clients,
            'validation' => $this->validation
        ];

        return view('client_assets/create', $data);
    }

    /**
     * Store client assets
     */
    /**
     * Store client assets
     */
    public function store()
    {
        $rules = [
            'client_id' => 'required|integer',
            'logo_png' => 'permit_empty|uploaded[logo_png]|max_size[logo_png,5120]|ext_in[logo_png,png]',
            'logo_jpg' => 'permit_empty|uploaded[logo_jpg]|max_size[logo_jpg,5120]|ext_in[logo_jpg,jpg,jpeg]',
            'logo_psd' => 'permit_empty|uploaded[logo_psd]|max_size[logo_psd,51200]|ext_in[logo_psd,psd]',
            'logo_pdf' => 'permit_empty|uploaded[logo_pdf]|max_size[logo_pdf,5120]|ext_in[logo_pdf,pdf]',
            'remarks' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validation)
                ->with('error', 'Validation failed.');
        }

        $input = $this->request->getPost();

        try {
            $uploadPath = FCPATH . 'uploads/client_assets/logos/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Handle PNG logo upload
            $logoPng = null;
            $pngFile = $this->request->getFile('logo_png');
            if ($pngFile && $pngFile->isValid() && !$pngFile->hasMoved()) {
                $pngName = time() . '_png_' . $pngFile->getRandomName();
                $pngFile->move($uploadPath, $pngName);
                $logoPng = $pngName;
            }

            // Handle JPG logo upload
            $logoJpg = null;
            $jpgFile = $this->request->getFile('logo_jpg');
            if ($jpgFile && $jpgFile->isValid() && !$jpgFile->hasMoved()) {
                $jpgName = time() . '_jpg_' . $jpgFile->getRandomName();
                $jpgFile->move($uploadPath, $jpgName);
                $logoJpg = $jpgName;
            }

            // Handle PSD logo upload
            $logoPsd = null;
            $psdFile = $this->request->getFile('logo_psd');
            if ($psdFile && $psdFile->isValid() && !$psdFile->hasMoved()) {
                $psdName = time() . '_psd_' . $psdFile->getRandomName();
                $psdFile->move($uploadPath, $psdName);
                $logoPsd = $psdName;
            }

            // Handle PDF logo upload
            $logoPdf = null;
            $pdfFile = $this->request->getFile('logo_pdf');
            if ($pdfFile && $pdfFile->isValid() && !$pdfFile->hasMoved()) {
                $pdfName = time() . '_pdf_' . $pdfFile->getRandomName();
                $pdfFile->move($uploadPath, $pdfName);
                $logoPdf = $pdfName;
            }

            // Handle template files upload (multiple)
            $templateFiles = [];
            $templates = $this->request->getFiles('template_files');
            if (!empty($templates)) {
                $templatePath = FCPATH . 'uploads/client_assets/templates/';
                if (!is_dir($templatePath)) {
                    mkdir($templatePath, 0755, true);
                }

                foreach ($templates as $template) {
                    if (is_array($template)) {
                        foreach ($template as $file) {
                            if ($file->isValid() && !$file->hasMoved()) {
                                $fileName = time() . '_' . $file->getRandomName();
                                $file->move($templatePath, $fileName);
                                $templateFiles[] = $fileName;
                            }
                        }
                    } else {
                        if ($template->isValid() && !$template->hasMoved()) {
                            $fileName = time() . '_' . $template->getRandomName();
                            $template->move($templatePath, $fileName);
                            $templateFiles[] = $fileName;
                        }
                    }
                }
            }

            // Handle social media - Fixed platforms with link, username, password
            $socialMedia = [];
            $platforms = ['facebook', 'instagram', 'youtube', 'twitter', 'quora', 'website', 'linkedin', 'pinterest', 'gmb'];

            foreach ($platforms as $platform) {
                $link = $this->request->getPost($platform . '_link');
                $username = $this->request->getPost($platform . '_username');
                $password = $this->request->getPost($platform . '_password');

                if (!empty($link) || !empty($username) || !empty($password)) {
                    $socialMedia[$platform] = [
                        'link' => trim($link ?? ''),
                        'username' => trim($username ?? ''),
                        'password' => trim($password ?? '')  // Consider encrypting this in production
                    ];
                }
            }

            // Insert asset record
            $assetData = [
                'client_id' => $input['client_id'],
                'logo_png' => $logoPng,
                'logo_jpg' => $logoJpg,
                'logo_psd' => $logoPsd,
                'logo_pdf' => $logoPdf,
                'template_files' => !empty($templateFiles) ? json_encode($templateFiles) : null,
                'social_media' => !empty($socialMedia) ? json_encode($socialMedia) : null,
                'remarks' => !empty($input['remarks']) ? trim($input['remarks']) : null,
                'uploaded_by' => session()->get('user_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->assetModel->insert($assetData);

            session()->setFlashdata('success', 'Client assets uploaded successfully!');
            return redirect()->to(base_url('client-assets'));
        } catch (\Exception $e) {
            log_message('error', 'Client Asset Upload Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to upload assets: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }


    /**
     * View client assets
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
            return redirect()->to(base_url('client-assets'));
        }

        // Decode JSON fields
        $asset['template_files_array'] = !empty($asset['template_files']) ? json_decode($asset['template_files'], true) : [];
        $asset['social_media_array'] = !empty($asset['social_media']) ? json_decode($asset['social_media'], true) : [];

        $data = [
            'title' => 'View Client Assets',
            'asset' => $asset
        ];

        return view('client_assets/view', $data);
    }

    /**
     * Edit client assets
     */
    public function edit($id)
    {
        $asset = $this->assetModel->find($id);

        if (!$asset) {
            session()->setFlashdata('error', 'Asset not found.');
            return redirect()->to(base_url('client-assets'));
        }

        $clients = $this->clientModel->findAll();

        // Decode JSON fields
        $asset['template_files_array'] = !empty($asset['template_files']) ? json_decode($asset['template_files'], true) : [];
        $asset['social_media_array'] = !empty($asset['social_media']) ? json_decode($asset['social_media'], true) : [];

        $data = [
            'title' => 'Edit Client Assets',
            'asset' => $asset,
            'clients' => $clients,
            'validation' => $this->validation
        ];

        return view('client_assets/edit', $data);
    }

    /**
     * Update client assets
     */
    public function update($id)
    {
        $asset = $this->assetModel->find($id);

        if (!$asset) {
            session()->setFlashdata('error', 'Asset not found.');
            return redirect()->to(base_url('client-assets'));
        }

        $rules = [
            'client_id' => 'required|integer',
            'logo_png' => 'permit_empty|uploaded[logo_png]|max_size[logo_png,5120]|ext_in[logo_png,png]',
            'logo_jpg' => 'permit_empty|uploaded[logo_jpg]|max_size[logo_jpg,5120]|ext_in[logo_jpg,jpg,jpeg]',
            'logo_psd' => 'permit_empty|uploaded[logo_psd]|max_size[logo_psd,51200]|ext_in[logo_psd,psd]',
            'logo_pdf' => 'permit_empty|uploaded[logo_pdf]|max_size[logo_pdf,5120]|ext_in[logo_pdf,pdf]',
            'remarks' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validation)
                ->with('error', 'Validation failed.');
        }

        $input = $this->request->getPost();

        try {
            $uploadPath = FCPATH . 'uploads/client_assets/logos/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Handle PNG Logo
            $logoPng = $asset['logo_png'];
            if ($this->request->getPost('delete_logo_png')) {
                // Delete existing PNG
                if (!empty($logoPng)) {
                    $oldPath = $uploadPath . $logoPng;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $logoPng = null;
            }
            $pngFile = $this->request->getFile('logo_png');
            if ($pngFile && $pngFile->isValid() && !$pngFile->hasMoved()) {
                // Delete old PNG
                if (!empty($logoPng)) {
                    $oldPath = $uploadPath . $logoPng;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $pngName = time() . '_png_' . $pngFile->getRandomName();
                $pngFile->move($uploadPath, $pngName);
                $logoPng = $pngName;
            }

            // Handle JPG Logo
            $logoJpg = $asset['logo_jpg'];
            if ($this->request->getPost('delete_logo_jpg')) {
                if (!empty($logoJpg)) {
                    $oldPath = $uploadPath . $logoJpg;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $logoJpg = null;
            }
            $jpgFile = $this->request->getFile('logo_jpg');
            if ($jpgFile && $jpgFile->isValid() && !$jpgFile->hasMoved()) {
                if (!empty($logoJpg)) {
                    $oldPath = $uploadPath . $logoJpg;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $jpgName = time() . '_jpg_' . $jpgFile->getRandomName();
                $jpgFile->move($uploadPath, $jpgName);
                $logoJpg = $jpgName;
            }

            // Handle PSD Logo
            $logoPsd = $asset['logo_psd'];
            if ($this->request->getPost('delete_logo_psd')) {
                if (!empty($logoPsd)) {
                    $oldPath = $uploadPath . $logoPsd;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $logoPsd = null;
            }
            $psdFile = $this->request->getFile('logo_psd');
            if ($psdFile && $psdFile->isValid() && !$psdFile->hasMoved()) {
                if (!empty($logoPsd)) {
                    $oldPath = $uploadPath . $logoPsd;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $psdName = time() . '_psd_' . $psdFile->getRandomName();
                $psdFile->move($uploadPath, $psdName);
                $logoPsd = $psdName;
            }

            // Handle PDF Logo
            $logoPdf = $asset['logo_pdf'];
            if ($this->request->getPost('delete_logo_pdf')) {
                if (!empty($logoPdf)) {
                    $oldPath = $uploadPath . $logoPdf;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $logoPdf = null;
            }
            $pdfFile = $this->request->getFile('logo_pdf');
            if ($pdfFile && $pdfFile->isValid() && !$pdfFile->hasMoved()) {
                if (!empty($logoPdf)) {
                    $oldPath = $uploadPath . $logoPdf;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $pdfName = time() . '_pdf_' . $pdfFile->getRandomName();
                $pdfFile->move($uploadPath, $pdfName);
                $logoPdf = $pdfName;
            }

            // Handle template files
            $existingTemplates = !empty($asset['template_files']) ? json_decode($asset['template_files'], true) : [];
            $keepTemplates = $this->request->getPost('keep_templates') ?? [];

            $keptTemplates = [];
            foreach ($existingTemplates as $index => $file) {
                if (in_array($index, $keepTemplates)) {
                    $keptTemplates[] = $file;
                } else {
                    $filePath = FCPATH . 'uploads/client_assets/templates/' . $file;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            // Upload new templates
            $templates = $this->request->getFiles('template_files');
            if (!empty($templates)) {
                $templatePath = FCPATH . 'uploads/client_assets/templates/';
                if (!is_dir($templatePath)) {
                    mkdir($templatePath, 0755, true);
                }

                foreach ($templates as $template) {
                    if (is_array($template)) {
                        foreach ($template as $file) {
                            if ($file->isValid() && !$file->hasMoved()) {
                                $fileName = time() . '_' . $file->getRandomName();
                                $file->move($templatePath, $fileName);
                                $keptTemplates[] = $fileName;
                            }
                        }
                    } else {
                        if ($template->isValid() && !$template->hasMoved()) {
                            $fileName = time() . '_' . $template->getRandomName();
                            $template->move($templatePath, $fileName);
                            $keptTemplates[] = $fileName;
                        }
                    }
                }
            }

            // Handle social media - Fixed platforms with link, username, password
            $socialMedia = [];
            $platforms = ['facebook', 'instagram', 'youtube', 'twitter', 'quora', 'website', 'linkedin', 'pinterest', 'gmb'];

            foreach ($platforms as $platform) {
                $link = $this->request->getPost($platform . '_link');
                $username = $this->request->getPost($platform . '_username');
                $password = $this->request->getPost($platform . '_password');

                if (!empty($link) || !empty($username) || !empty($password)) {
                    $socialMedia[$platform] = [
                        'link' => trim($link ?? ''),
                        'username' => trim($username ?? ''),
                        'password' => trim($password ?? '')  // Consider encrypting in production
                    ];
                }
            }

            // Update asset record
            $updateData = [
                'client_id' => $input['client_id'],
                'logo_png' => $logoPng,
                'logo_jpg' => $logoJpg,
                'logo_psd' => $logoPsd,
                'logo_pdf' => $logoPdf,
                'template_files' => !empty($keptTemplates) ? json_encode($keptTemplates) : null,
                'social_media' => !empty($socialMedia) ? json_encode($socialMedia) : null,
                'remarks' => !empty($input['remarks']) ? trim($input['remarks']) : null,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->assetModel->update($id, $updateData);

            session()->setFlashdata('success', 'Client assets updated successfully!');
            return redirect()->to(base_url('client-assets'));
        } catch (\Exception $e) {
            log_message('error', 'Client Asset Update Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to update assets: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }


    /**
     * Delete client assets
     */
    public function delete($id)
    {
        $asset = $this->assetModel->find($id);

        if (!$asset) {
            session()->setFlashdata('error', 'Asset not found.');
            return redirect()->to(base_url('client-assets'));
        }

        try {
            // Delete logo file
            if (!empty($asset['logo_file'])) {
                $logoPath = FCPATH . 'uploads/client_assets/logos/' . $asset['logo_file'];
                if (file_exists($logoPath)) {
                    unlink($logoPath);
                }
            }

            // Delete template files
            if (!empty($asset['template_files'])) {
                $templates = json_decode($asset['template_files'], true);
                foreach ($templates as $file) {
                    $filePath = FCPATH . 'uploads/client_assets/templates/' . $file;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            $this->assetModel->delete($id);

            session()->setFlashdata('success', 'Client assets deleted successfully!');
        } catch (\Exception $e) {
            log_message('error', 'Client Asset Deletion Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to delete assets.');
        }

        return redirect()->to(base_url('client-assets'));
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

        return $this->response->download($filePath, null)->setFileName($filename);
    }
}

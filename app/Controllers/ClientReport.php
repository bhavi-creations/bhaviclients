<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\ClientReport.php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClientReportModel;
use App\Models\ClientModel;

class ClientReport extends BaseController
{
    protected $reportModel;
    protected $clientModel;
    protected $validation;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->reportModel = new ClientReportModel();
        $this->clientModel = new ClientModel();
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);

        // Access control - Only Admin and Admin Manager
        if (!in_array(session()->get('role_id'), [1, 5])) {
            header('Location: ' . base_url('dashboard'));
            exit;
        }
    }

    /**
     * Display all reports
     */
    public function index()
    {
        $reports = $this->reportModel->getAllReportsWithClient();

        $data = [
            'title' => 'Client Reports',
            'reports' => $reports
        ];

        return view('client_report/index', $data);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $clients = $this->clientModel->findAll();

        $data = [
            'title' => 'Upload Client Report',
            'clients' => $clients,
            'validation' => $this->validation
        ];

        return view('client_report/create', $data);
    }

    /**
     * Store new report
     */
    public function store()
    {
        $rules = [
            'client_id' => 'required|integer',
            'title' => 'required|min_length[3]|max_length[255]',
            'report_date' => 'required',
            'remarks' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validation)
                ->with('error', 'Validation failed. Please check the form.');
        }

        $input = $this->request->getPost();
        $this->db->transStart();

        try {
            // Create report
            $reportData = [
                'client_id' => $input['client_id'],
                'title' => trim($input['title']),
                'report_date' => $input['report_date'],
                'remarks' => !empty($input['remarks']) ? trim($input['remarks']) : null,
                'uploaded_by' => session()->get('id'),
                'file_uploads' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->reportModel->insert($reportData);
            $reportId = $this->reportModel->getInsertID();

            if (!$reportId) {
                throw new \Exception('Failed to create report.');
            }

            // Handle file uploads
            $files = $this->request->getFiles('report_files');
            if (!empty($files)) {
                $uploadPath = FCPATH . 'uploads/client_reports/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $uploadedFiles = [];
                foreach ($files as $file) {
                    if (is_array($file)) {
                        foreach ($file as $singleFile) {
                            if ($singleFile->isValid() && !$singleFile->hasMoved()) {
                                $newName = $singleFile->getRandomName();
                                $singleFile->move($uploadPath, $newName);
                                $uploadedFiles[] = $newName;
                            }
                        }
                    } else {
                        if ($file->isValid() && !$file->hasMoved()) {
                            $newName = $file->getRandomName();
                            $file->move($uploadPath, $newName);
                            $uploadedFiles[] = $newName;
                        }
                    }
                }

                if (!empty($uploadedFiles)) {
                    $this->reportModel->update($reportId, [
                        'file_uploads' => json_encode($uploadedFiles)
                    ]);
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed.');
            }

            session()->setFlashdata('success', 'Client report uploaded successfully!');
            return redirect()->to(base_url('client-report'));
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Report Creation Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to create report: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * View report details
     */
    public function view($id)
    {
        $report = $this->reportModel->find($id);

        if (!$report) {
            session()->setFlashdata('error', 'Report not found.');
            return redirect()->to(base_url('client-report'));
        }

        $client = $this->clientModel->find($report['client_id']);
        $files = $this->reportModel->getReportFiles($id);

        $data = [
            'title' => 'Report Details',
            'report' => $report,
            'client' => $client,
            'files' => $files
        ];

        return view('client_report/view', $data);
    }

    /**
     * Download report file
     */
    public function downloadFile($reportId, $fileName)
    {
        $report = $this->reportModel->find($reportId);
        if (!$report) {
            return redirect()->back()->with('error', 'Report not found.');
        }

        $files = $this->reportModel->getReportFiles($reportId);
        if (!in_array($fileName, $files)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filePath = FCPATH . 'uploads/client_reports/' . $fileName;
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        return $this->response->download($filePath, null)->setFileName($fileName);
    }

    /**
     * Delete report file
     */
    public function deleteFile($reportId, $fileName)
    {
        $report = $this->reportModel->find($reportId);
        if (!$report) {
            return redirect()->back()->with('error', 'Report not found.');
        }

        $files = $this->reportModel->getReportFiles($reportId);
        if (!in_array($fileName, $files)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filePath = FCPATH . 'uploads/client_reports/' . $fileName;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $this->reportModel->removeFile($reportId, $fileName);

        session()->setFlashdata('success', 'File deleted successfully.');
        return redirect()->back();
    }

    /**
     * Delete report
     */
    public function delete($id)
    {
        $report = $this->reportModel->find($id);

        if (!$report) {
            session()->setFlashdata('error', 'Report not found.');
            return redirect()->to(base_url('client-report'));
        }

        // Delete files
        $files = $this->reportModel->getReportFiles($id);
        foreach ($files as $file) {
            $filePath = FCPATH . 'uploads/client_reports/' . $file;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Delete report
        $this->reportModel->delete($id);

        session()->setFlashdata('success', 'Report deleted successfully.');
        return redirect()->to(base_url('client-report'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $report = $this->reportModel->find($id);

        if (!$report) {
            session()->setFlashdata('error', 'Report not found.');
            return redirect()->to(base_url('client-report'));
        }

        $clients = $this->clientModel->findAll();
        $files = $this->reportModel->getReportFiles($id);

        $data = [
            'title' => 'Edit Report',
            'report' => $report,
            'clients' => $clients,
            'files' => $files,
            'validation' => $this->validation
        ];

        return view('client_report/edit', $data);
    }

    /**
     * Update report
     */
    public function update($id)
    {
        $report = $this->reportModel->find($id);

        if (!$report) {
            session()->setFlashdata('error', 'Report not found.');
            return redirect()->to(base_url('client-report'));
        }

        $rules = [
            'client_id' => 'required|integer',
            'title' => 'required|min_length[3]|max_length[255]',
            'report_date' => 'required',
            'remarks' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validation)
                ->with('error', 'Validation failed. Please check the form.');
        }

        $input = $this->request->getPost();
        $this->db->transStart();

        try {
            // Update report details
            $reportData = [
                'client_id' => $input['client_id'],
                'title' => trim($input['title']),
                'report_date' => $input['report_date'],
                'remarks' => !empty($input['remarks']) ? trim($input['remarks']) : null,
            ];

            $this->reportModel->update($id, $reportData);

            // Handle additional file uploads
            $files = $this->request->getFiles('report_files');
            if (!empty($files)) {
                $uploadPath = FCPATH . 'uploads/client_reports/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Get existing files
                $existingFiles = $this->reportModel->getReportFiles($id);

                $uploadedFiles = [];
                foreach ($files as $file) {
                    if (is_array($file)) {
                        foreach ($file as $singleFile) {
                            if ($singleFile->isValid() && !$singleFile->hasMoved()) {
                                $newName = $singleFile->getRandomName();
                                $singleFile->move($uploadPath, $newName);
                                $uploadedFiles[] = $newName;
                            }
                        }
                    } else {
                        if ($file->isValid() && !$file->hasMoved()) {
                            $newName = $file->getRandomName();
                            $file->move($uploadPath, $newName);
                            $uploadedFiles[] = $newName;
                        }
                    }
                }

                // Merge with existing files
                if (!empty($uploadedFiles)) {
                    $allFiles = array_merge($existingFiles, $uploadedFiles);
                    $this->reportModel->update($id, [
                        'file_uploads' => json_encode($allFiles)
                    ]);
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed.');
            }

            session()->setFlashdata('success', 'Report updated successfully!');
            return redirect()->to(base_url('client-report'));
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Report Update Error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to update report: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}

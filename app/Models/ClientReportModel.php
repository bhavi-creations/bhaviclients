<?php
// C:\xampp\htdocs\bhaviclients\app\Models\ClientReportModel.php

namespace App\Models;

use CodeIgniter\Model;

class ClientReportModel extends Model
{
    protected $table = 'client_reports';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'client_id',
        'title',
        'report_date',
        'file_uploads',
        'remarks',
        'uploaded_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Get all reports for a client
     */
    public function getClientReports($clientId)
    {
        return $this->select('client_reports.*, users.first_name, users.last_name')
                    ->join('users', 'users.id = client_reports.uploaded_by', 'left')
                    ->where('client_reports.client_id', $clientId)
                    ->orderBy('client_reports.report_date', 'DESC')
                    ->orderBy('client_reports.id', 'DESC')
                    ->findAll();
    }

    /**
     * Get all reports with client info
     */
    public function getAllReportsWithClient()
    {
        return $this->select('client_reports.*, clients.name as client_name, users.first_name, users.last_name')
                    ->join('clients', 'clients.id = client_reports.client_id', 'left')
                    ->join('users', 'users.id = client_reports.uploaded_by', 'left')
                    ->orderBy('client_reports.report_date', 'DESC')
                    ->orderBy('client_reports.id', 'DESC')
                    ->findAll();
    }

    /**
     * Get report files as array
     */
    public function getReportFiles($id)
    {
        $report = $this->find($id);
        if ($report && !empty($report['file_uploads'])) {
            return json_decode($report['file_uploads'], true);
        }
        return [];
    }

    /**
     * Add file to report
     */
    public function addFile($id, $fileName)
    {
        $files = $this->getReportFiles($id);
        $files[] = $fileName;
        
        return $this->update($id, [
            'file_uploads' => json_encode($files)
        ]);
    }

    /**
     * Remove file from report
     */
    public function removeFile($id, $fileName)
    {
        $files = $this->getReportFiles($id);
        $files = array_values(array_filter($files, function($file) use ($fileName) {
            return $file !== $fileName;
        }));
        
        return $this->update($id, [
            'file_uploads' => json_encode($files)
        ]);
    }

    /**
     * Get reports by month/year
     */
    public function getReportsByMonth($year, $month, $clientId = null)
    {
        $builder = $this->select('client_reports.*, clients.name as client_name')
                        ->join('clients', 'clients.id = client_reports.client_id', 'left')
                        ->where('YEAR(client_reports.report_date)', $year)
                        ->where('MONTH(client_reports.report_date)', $month);
        
        if ($clientId) {
            $builder->where('client_reports.client_id', $clientId);
        }

        return $builder->orderBy('client_reports.report_date', 'DESC')->findAll();
    }
}

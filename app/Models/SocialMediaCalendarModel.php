<?php
// C:\xampp\htdocs\bhaviclients\app\Models\SocialMediaCalendarModel.php

namespace App\Models;

use CodeIgniter\Model;

class SocialMediaCalendarModel extends Model
{
    protected $table = 'social_media_calendars';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'client_id',
        'calendar_month',
        'calendar_year',
        'file_name',
        'original_name',
        'file_size',
        'file_type',
        'file_extension',
        'remarks',
        'uploaded_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'uploaded_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'client_id' => 'required|integer',
        'calendar_month' => 'required|integer|greater_than[0]|less_than[13]',
        'calendar_year' => 'required|integer',
        'file_name' => 'required|max_length[255]',
        'original_name' => 'required|max_length[255]',
        'file_size' => 'required|integer',
        'file_type' => 'required|max_length[100]',
        'file_extension' => 'required|max_length[10]',
        'remarks' => 'permit_empty',
        'uploaded_by' => 'required|integer'
    ];

    /**
     * Get all calendars for a client with uploader info
     */
    public function getClientCalendarsWithUploader($clientId)
    {
        return $this->select('social_media_calendars.*, 
                            users.first_name as uploader_first_name, 
                            users.last_name as uploader_last_name')
            ->join('users', 'users.id = social_media_calendars.uploaded_by', 'left')
            ->where('social_media_calendars.client_id', $clientId)
            ->orderBy('social_media_calendars.calendar_year', 'DESC')
            ->orderBy('social_media_calendars.calendar_month', 'DESC')
            ->findAll();
    }

    /**
     * Get all calendars with client and uploader info
     */
    public function getAllCalendarsWithDetails()
    {
        return $this->select('social_media_calendars.*, 
                            clients.name as client_name,
                            users.first_name as uploader_first_name, 
                            users.last_name as uploader_last_name')
            ->join('clients', 'clients.id = social_media_calendars.client_id', 'left')
            ->join('users', 'users.id = social_media_calendars.uploaded_by', 'left')
            ->orderBy('social_media_calendars.calendar_year', 'DESC')
            ->orderBy('social_media_calendars.calendar_month', 'DESC')
            ->findAll();
    }

    /**
     * Helper: Get month name from number
     */
    public static function getMonthName($monthNumber)
    {
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
        return $months[$monthNumber] ?? 'Unknown';
    }

    /**
     * Get clients who have calendars with latest remarks
     */
    public function getClientsWithCalendars()
    {
        return $this->select('clients.id as client_id,
                         clients.name as client_name,
                         clients.email,
                         clients.phone,
                         COUNT(social_media_calendars.id) as calendar_count,
                         MAX(social_media_calendars.uploaded_at) as last_upload,
                         (SELECT remarks FROM social_media_calendars 
                          WHERE client_id = clients.id 
                          ORDER BY uploaded_at DESC 
                          LIMIT 1) as latest_remarks')
            ->join('clients', 'clients.id = social_media_calendars.client_id', 'left')
            ->groupBy('clients.id, clients.name, clients.email, clients.phone')
            ->orderBy('last_upload', 'DESC')
            ->findAll();
    }
}

<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\ClientSocialMediaCalendar.php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\SocialMediaCalendarModel;

class ClientSocialMediaCalendar extends Controller
{
    protected $calendarModel;
    protected $db;

    public function __construct()
    {
        $this->calendarModel = new SocialMediaCalendarModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);

        // Access control: Only clients and client managers (role_id = 3, 4)
        if (!in_array(session()->get('role_id'), [3, 4])) {
            header('Location: ' . base_url('dashboard'));
            exit;
        }
    }

    /**
     * View social media calendars for logged-in client
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $roleId = session()->get('role_id');

        // Get client_id based on role
        if ($roleId == 3) {
            $client = $this->db->table('clients')
                              ->where('user_id', $userId)
                              ->get()
                              ->getRowArray();
        } elseif ($roleId == 4) {
            $client = $this->db->table('clients')
                              ->where('client_manager_id', $userId)
                              ->get()
                              ->getRowArray();
        }

        if (!$client) {
            return redirect()->to(base_url('dashboard'))
                           ->with('error', 'Client profile not found.');
        }

        $clientId = $client['id'];

        // Get all calendars for this client
        $calendars = $this->calendarModel->getClientCalendarsWithUploader($clientId);

        return view('client/social_media_calendar/index', [
            'title' => 'My Social Media Calendars',
            'client' => $client,
            'calendars' => $calendars
        ]);
    }

    /**
     * Download calendar file
     */
    public function download($fileId)
    {
        $userId = session()->get('user_id');
        $roleId = session()->get('role_id');

        // Get client_id
        if ($roleId == 3) {
            $client = $this->db->table('clients')->where('user_id', $userId)->get()->getRowArray();
        } elseif ($roleId == 4) {
            $client = $this->db->table('clients')->where('client_manager_id', $userId)->get()->getRowArray();
        }

        if (!$client) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $file = $this->calendarModel->find($fileId);
        
        // Verify this file belongs to this client
        if (!$file || $file['client_id'] != $client['id']) {
            return redirect()->back()->with('error', 'File not found or access denied.');
        }

        $filePath = FCPATH . 'uploads/social_media_calendars/' . $file['file_name'];
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        return $this->response->download($filePath, null)->setFileName($file['original_name']);
    }
}

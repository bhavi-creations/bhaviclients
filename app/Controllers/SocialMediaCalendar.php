<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\SocialMediaCalendar.php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\SocialMediaCalendarModel;
use App\Models\ClientModel;

class SocialMediaCalendar extends Controller
{
    protected $calendarModel;
    protected $clientModel;

    public function __construct()
    {
        $this->calendarModel = new SocialMediaCalendarModel();
        $this->clientModel = new ClientModel();
        helper(['form', 'url', 'filesystem']);

        // Access control: Only allow admins (role_id = 1, 5)
        if (!in_array(session()->get('role_id'), [1, 5])) {
            header('Location: ' . base_url('dashboard'));
            exit;
        }
    }

    /**
     * List all social media calendars
     */
    /**
     * List clients who have social media calendars
     */
    public function index()
    {
        $clients = $this->calendarModel->getClientsWithCalendars();

        return view('social_media_calendar/index', [
            'title' => 'Social Media Calendars',
            'clients' => $clients
        ]);
    }


    /**
     * Upload form
     */
    public function upload($clientId = null)
    {
        $clients = $this->clientModel->findAll();

        $selectedClient = null;
        if ($clientId) {
            $selectedClient = $this->clientModel->find($clientId);
        }

        return view('social_media_calendar/upload', [
            'title' => 'Upload Social Media Calendar',
            'clients' => $clients,
            'selectedClient' => $selectedClient,
            'validation' => \Config\Services::validation()
        ]);
    }

    /**
     * Store uploaded calendar
     */
    public function store()
    {
        $input = $this->request->getPost();

        $rules = [
            'client_id' => 'required|integer',
            'calendar_month' => 'required|integer|greater_than[0]|less_than[13]',
            'calendar_year' => 'required|integer|greater_than[2020]|less_than[2100]',
            'remarks' => 'permit_empty|max_length[500]',
            'calendar_file' => 'uploaded[calendar_file]|max_size[calendar_file,10240]|ext_in[calendar_file,pdf,doc,docx,xls,xlsx,jpg,jpeg,png]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validator)
                ->with('error', 'Validation failed.');
        }

        $file = $this->request->getFile('calendar_file');

        if ($file->isValid() && !$file->hasMoved()) {
            try {
                $uploadPath = FCPATH . 'uploads/social_media_calendars/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Get file details BEFORE moving
                $originalName = $file->getName();
                $fileSize = $file->getSize();
                $fileExtension = $file->getExtension();

                // Determine MIME type safely
                $mimeType = 'application/octet-stream';
                try {
                    $mimeType = $file->getMimeType();
                } catch (\Exception $e) {
                    // Fallback to guessing MIME type from extension
                    $mimeTypes = [
                        'pdf' => 'application/pdf',
                        'doc' => 'application/msword',
                        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'xls' => 'application/vnd.ms-excel',
                        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'jpg' => 'image/jpeg',
                        'jpeg' => 'image/jpeg',
                        'png' => 'image/png'
                    ];
                    $mimeType = $mimeTypes[strtolower($fileExtension)] ?? 'application/octet-stream';
                }

                // Generate unique filename and move
                $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                $file->move($uploadPath, $fileName);

                // Insert into database
                $fileData = [
                    'client_id' => $input['client_id'],
                    'calendar_month' => $input['calendar_month'],
                    'calendar_year' => $input['calendar_year'],
                    'file_name' => $fileName,
                    'original_name' => $originalName,
                    'file_size' => $fileSize,
                    'file_type' => $mimeType,
                    'file_extension' => $fileExtension,
                    'remarks' => $input['remarks'] ?? null,
                    'uploaded_by' => session()->get('user_id')
                ];

                if ($this->calendarModel->insert($fileData)) {
                    return redirect()->to(base_url('social-media-calendar'))
                        ->with('message', 'Calendar uploaded successfully!');
                } else {
                    return redirect()->back()->withInput()->with('error', 'Failed to save calendar.');
                }
            } catch (\Exception $e) {
                log_message('error', 'Calendar upload error: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Upload failed: ' . $e->getMessage());
            }
        }

        return redirect()->back()->withInput()->with('error', 'Invalid file upload.');
    }


    /**
     * View calendars for specific client
     */
    public function clientCalendars($clientId)
    {
        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return redirect()->to(base_url('social-media-calendar'))
                ->with('error', 'Client not found.');
        }

        $calendars = $this->calendarModel->getClientCalendarsWithUploader($clientId);

        return view('social_media_calendar/client_calendars', [
            'title' => 'Calendars - ' . $client['name'],
            'client' => $client,
            'calendars' => $calendars
        ]);
    }


    /**
     * Update calendar details (month, year, remarks, and optionally file)
     */
    public function update()
    {
        $input = $this->request->getPost();

        $rules = [
            'calendar_id' => 'required|integer',
            'client_id' => 'required|integer',
            'calendar_month' => 'required|integer|greater_than[0]|less_than[13]',
            'calendar_year' => 'required|integer|greater_than[2020]|less_than[2100]',
            'remarks' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Validation failed.');
        }

        $calendarId = $input['calendar_id'];
        $clientId = $input['client_id'];

        // Get existing calendar
        $existingCalendar = $this->calendarModel->find($calendarId);
        if (!$existingCalendar) {
            return redirect()->back()->with('error', 'Calendar not found.');
        }

        // Prepare update data
        $updateData = [
            'calendar_month' => $input['calendar_month'],
            'calendar_year' => $input['calendar_year'],
            'remarks' => $input['remarks'] ?? null
        ];

        // Check if new file is uploaded
        $file = $this->request->getFile('calendar_file');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            try {
                // Validate file
                if (!$this->validate(['calendar_file' => 'max_size[calendar_file,10240]|ext_in[calendar_file,pdf,doc,docx,xls,xlsx,jpg,jpeg,png]'])) {
                    return redirect()->back()->with('error', 'Invalid file upload.');
                }

                $uploadPath = FCPATH . 'uploads/social_media_calendars/';

                // Get file details BEFORE moving
                $originalName = $file->getName();
                $fileSize = $file->getSize();
                $fileExtension = $file->getExtension();

                // Determine MIME type safely
                $mimeType = 'application/octet-stream';
                try {
                    $mimeType = $file->getMimeType();
                } catch (\Exception $e) {
                    $mimeTypes = [
                        'pdf' => 'application/pdf',
                        'doc' => 'application/msword',
                        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'xls' => 'application/vnd.ms-excel',
                        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'jpg' => 'image/jpeg',
                        'jpeg' => 'image/jpeg',
                        'png' => 'image/png'
                    ];
                    $mimeType = $mimeTypes[strtolower($fileExtension)] ?? 'application/octet-stream';
                }

                // Generate unique filename and move
                $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                $file->move($uploadPath, $fileName);

                // Delete old file
                $oldFilePath = $uploadPath . $existingCalendar['file_name'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }

                // Update file data
                $updateData['file_name'] = $fileName;
                $updateData['original_name'] = $originalName;
                $updateData['file_size'] = $fileSize;
                $updateData['file_type'] = $mimeType;
                $updateData['file_extension'] = $fileExtension;
            } catch (\Exception $e) {
                log_message('error', 'File update error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'File upload failed: ' . $e->getMessage());
            }
        }

        // Update calendar
        if ($this->calendarModel->update($calendarId, $updateData)) {
            return redirect()->to(base_url('social-media-calendar/client/' . $clientId))
                ->with('message', 'Calendar updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to update calendar.');
        }
    }


    /**
     * View file in browser
     */
    public function view($calendarId)
    {
        $calendar = $this->calendarModel->find($calendarId);

        if (!$calendar) {
            return redirect()->back()->with('error', 'Calendar not found.');
        }

        // Correct path: Root uploads folder
        $filePath = ROOTPATH . 'uploads/social_media_calendars/' . $calendar['file_name'];

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found: ' . $calendar['file_name']);
        }

        // Set appropriate content type based on extension
        $ext = strtolower($calendar['file_extension']);
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';

        // Output file to browser
        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . $calendar['original_name'] . '"')
            ->setBody(file_get_contents($filePath));
    }



    /**
     * Download calendar file
     */
    public function download($fileId)
    {
        $file = $this->calendarModel->find($fileId);
        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filePath = FCPATH . 'uploads/social_media_calendars/' . $file['file_name'];

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        return $this->response->download($filePath, null)->setFileName($file['original_name']);
    }

    /**
     * Delete calendar
     */
    public function delete($fileId)
    {
        $file = $this->calendarModel->find($fileId);
        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filePath = FCPATH . 'uploads/social_media_calendars/' . $file['file_name'];

        // Delete physical file
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete database record
        if ($this->calendarModel->delete($fileId)) {
            return redirect()->back()->with('message', 'Calendar deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to delete calendar.');
        }
    }
}

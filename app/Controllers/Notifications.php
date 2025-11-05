<?php
// C:\xampp\htdocs\bhaviclients\app\Controllers\Notifications.php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\NotificationModel;

class Notifications extends Controller
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        helper(['form', 'url','common']);  
    }

    /**
     * View all notifications for logged-in user
     */
    public function index()
    {
        $userId = session()->get('user_id');
        
        $notifications = $this->notificationModel->where('user_id', $userId)
                                                 ->orderBy('created_at', 'DESC')
                                                 ->findAll();

        return view('notifications/index', [
            'title' => 'My Notifications',
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId)
    {
        $userId = session()->get('user_id');
        $notification = $this->notificationModel->find($notificationId);

        if ($notification && $notification['user_id'] == $userId) {
            $this->notificationModel->markAsRead($notificationId);
            
            // Redirect to the notification link
            if (!empty($notification['link'])) {
                return redirect()->to(base_url($notification['link']));
            }
        }

        return redirect()->back();
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead()
    {
        $userId = session()->get('user_id');
        $this->notificationModel->markAllAsRead($userId);

        return redirect()->back()->with('message', 'All notifications marked as read!');
    }
}

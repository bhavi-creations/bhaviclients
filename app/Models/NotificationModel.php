<?php
// C:\xampp\htdocs\bhaviclients\app\Models\NotificationModel.php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'user_id',
        'type',
        'related_id',      // NEW
        'related_type',    // NEW
        'title',
        'message',
        'link',
        'is_read'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get unread notifications for a user
     */
    public function getUnreadNotifications($userId, $limit = 10)
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId)
    {
        return $this->update($notificationId, ['is_read' => 1]);
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId)
    {
        return $this->where('user_id', $userId)
            ->set('is_read', 1)
            ->update();
    }

    /**
     * Create leave request notification for admins
     */
    public function notifyLeaveRequest($leaveId, $employeeName)
    {
        $db = \Config\Database::connect();
        $admins = $db->table('users')->whereIn('role_id', [1, 5])->get()->getResultArray();

        foreach ($admins as $admin) {
            $this->insert([
                'user_id' => $admin['id'],
                'type' => 'leave_request',
                'title' => 'New Leave Request',
                'message' => $employeeName . ' has applied for leave',
                'link' => 'leave-management/view/' . $leaveId
            ]);
        }
    }

    /**
     * Create leave status notification for employee
     */
    public function notifyLeaveStatus($employeeId, $status, $adminName)
    {
        $title = $status == 'approved' ? 'Leave Approved' : 'Leave Rejected';
        $message = 'Your leave request has been ' . $status . ' by ' . $adminName;

        $this->insert([
            'user_id' => $employeeId,
            'type' => 'leave_' . $status,
            'title' => $title,
            'message' => $message,
            'link' => 'my-leaves'
        ]);
    }


    /**
     * Create holiday notification for all employees and clients
     */
    public function notifyHoliday($holidayId, $holidayName, $holidayDate)
    {
        $db = \Config\Database::connect();
        // Get all employees, clients, and client managers
        $users = $db->table('users')
            ->whereIn('role_id', [2, 3, 4])
            ->get()
            ->getResultArray();

        foreach ($users as $user) {
            $this->insert([
                'user_id' => $user['id'],
                'type' => 'holiday_added',
                'related_id' => $holidayId,
                'related_type' => 'holiday',
                'title' => 'New Holiday Announced',
                'message' => $holidayName . ' on ' . date('d M Y', strtotime($holidayDate)),
                'link' => 'holidays-list'
            ]);
        }
    }
}

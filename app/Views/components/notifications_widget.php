<?php
// C:\xampp\htdocs\bhaviclients\app\Views\components\notifications_widget.php
$notificationModel = new \App\Models\NotificationModel();
$userId = session()->get('user_id');
$notifications = $notificationModel->getUnreadNotifications($userId, 5);
$unreadCount = $notificationModel->getUnreadCount($userId);
?>

<?php if (!empty($notifications)): ?>
<div class="card card-warning">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-bell"></i> Notifications</h3>
        <div class="card-tools">
            <?php if ($unreadCount > 0): ?>
                <span class="badge badge-warning"><?= $unreadCount ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">
            <?php foreach ($notifications as $notif): ?>
                <li class="list-group-item">
                    <a href="<?= base_url($notif['link']) ?>" class="text-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>
                                    <?php if ($notif['type'] == 'leave_request'): ?>
                                        <i class="fas fa-calendar-plus text-warning"></i>
                                    <?php elseif ($notif['type'] == 'leave_approved'): ?>
                                        <i class="fas fa-check-circle text-success"></i>
                                    <?php elseif ($notif['type'] == 'leave_rejected'): ?>
                                        <i class="fas fa-times-circle text-danger"></i>
                                    <?php else: ?>
                                        <i class="fas fa-calendar-day text-info"></i>
                                    <?php endif; ?>
                                    <?= esc($notif['title']) ?>
                                </strong>
                                <br>
                                <small class="text-muted"><?= esc($notif['message']) ?></small>
                            </div>
                            <small class="text-muted"><?= timeAgo($notif['created_at']) ?></small>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="card-footer text-center">
            <a href="<?= base_url('notifications') ?>" class="btn btn-sm btn-primary">View All Notifications</a>
        </div>
    </div>
</div>
<?php endif; ?>

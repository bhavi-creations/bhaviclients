<?php
namespace App\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table = 'password_resets';
    protected $primaryKey = 'id';
    protected $allowedFields = ['email', 'token', 'created_at', 'expires_at'];
    protected $useTimestamps = false;

    /**
     * Create a password reset token
     */
    public function createToken($email)
    {
        // Delete old tokens for this email
        $this->where('email', $email)->delete();

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->insert([
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt
        ]);

        return $token;
    }

    /**
     * Verify token and get email
     */
    public function verifyToken($token)
    {
        $reset = $this->where('token', $token)
                      ->where('expires_at >', date('Y-m-d H:i:s'))
                      ->first();

        return $reset;
    }

    /**
     * Delete token after use
     */
    public function deleteToken($token)
    {
        return $this->where('token', $token)->delete();
    }
}

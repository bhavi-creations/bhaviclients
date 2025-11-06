<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\PasswordResetModel;

class ForgotPassword extends Controller
{
    protected $userModel;
    protected $resetModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->resetModel = new PasswordResetModel();
        helper(['form', 'url']);
    }

    /**
     * Show forgot password form
     */
    public function index()
    {
        return view('auth/forgot_password', [
            'title' => 'Forgot Password',
            'validation' => \Config\Services::validation()
        ]);
    }

    /**
     * Send reset link
     */
    public function sendResetLink()
    {
        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validator);
        }

        $email = $this->request->getPost('email');

        // Check if user exists
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()
                ->with('error', 'No account found with that email address.');
        }

        // Create reset token
        $token = $this->resetModel->createToken($email);

        // Create reset link
        $resetLink = base_url('reset-password/' . $token);

        // Send email
        $emailService = \Config\Services::email();

        $emailService->setFrom('bhavicreations2022@gmail.com', 'Bhavi Clients');
        $emailService->setTo($email);
        $emailService->setSubject('Password Reset Request - Bhavi Clients');

        // HTML Email Body
        $message = "
    <html>
    <body style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;'>
        <div style='max-width: 600px; margin: 0 auto; background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);'>
            <h2 style='color: #333; margin-bottom: 20px;'>Password Reset Request</h2>
            
            <p style='color: #555; line-height: 1.6;'>Hello,</p>
            
            <p style='color: #555; line-height: 1.6;'>
                You requested a password reset for your <strong>Bhavi Clients</strong> account.
            </p>
            
            <p style='color: #555; line-height: 1.6;'>
                Click the button below to reset your password:
            </p>
            
            <div style='text-align: center; margin: 30px 0;'>
                <a href='{$resetLink}' 
                   style='background-color: #007bff; 
                          color: white; 
                          padding: 14px 28px; 
                          text-decoration: none; 
                          border-radius: 5px; 
                          display: inline-block;
                          font-weight: bold;'>
                    Reset Password
                </a>
            </div>
            
            <p style='color: #555; line-height: 1.6; font-size: 14px;'>
                Or copy and paste this link into your browser:
            </p>
            
            <p style='background-color: #f8f9fa; 
                      padding: 10px; 
                      border-radius: 5px; 
                      word-break: break-all;
                      font-size: 12px;
                      color: #007bff;'>
                <a href='{$resetLink}' style='color: #007bff;'>{$resetLink}</a>
            </p>
            
            <div style='background-color: #fff3cd; 
                        border-left: 4px solid #ffc107; 
                        padding: 12px; 
                        margin: 20px 0;
                        border-radius: 4px;'>
                <strong style='color: #856404;'>⚠️ Important:</strong>
                <span style='color: #856404;'> This link will expire in 1 hour.</span>
            </div>
            
            <p style='color: #555; line-height: 1.6;'>
                If you didn't request this password reset, please ignore this email. 
                Your password will remain unchanged.
            </p>
            
            <hr style='margin: 30px 0; border: none; border-top: 1px solid #ddd;'>
            
            <p style='color: #999; font-size: 12px; line-height: 1.6;'>
                Thanks,<br>
                <strong>Bhavi Clients Team</strong>
            </p>
            
            <p style='color: #999; font-size: 11px; margin-top: 20px;'>
                This is an automated email. Please do not reply to this message.
            </p>
        </div>
    </body>
    </html>
    ";

        $emailService->setMessage($message);

        // Try sending email
            if ($emailService->send()) {
                return redirect()->to(base_url('login'))
                    ->with('message', 'Password reset link sent to your email! Please check your inbox (and spam folder).');
            } else {
                // Log the error for debugging
                log_message('error', 'Password reset email failed: ' . $emailService->printDebugger(['headers']));

                // In development, show the link
                if (ENVIRONMENT === 'development') {
                    return redirect()->back()
                        ->with('message', 'Email not configured. Reset link: ' . $resetLink);
                }

                // In production, show generic error
                return redirect()->back()
                    ->with('error', 'Failed to send reset email. Please contact support or try again later.');
            }
    }


    /**
     * Show reset password form
     */
    public function resetForm($token)
    {
        $reset = $this->resetModel->verifyToken($token);

        if (!$reset) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Invalid or expired reset link.');
        }

        return view('auth/reset_password', [
            'title' => 'Reset Password',
            'token' => $token,
            'validation' => \Config\Services::validation()
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword()
    {
        $rules = [
            'token' => 'required',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validator);
        }

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        // Verify token
        $reset = $this->resetModel->verifyToken($token);

        if (!$reset) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Invalid or expired reset link.');
        }

        // Update password (UserModel will hash it automatically via beforeUpdate callback)
        $user = $this->userModel->where('email', $reset['email'])->first();

        if ($user) {
            // Pass plain password - UserModel hashPassword callback will handle hashing
            $this->userModel->update($user['id'], [
                'password' => $password
            ]);

            // Delete token
            $this->resetModel->deleteToken($token);

            return redirect()->to(base_url('login'))
                ->with('message', 'Password reset successfully! Please login.');
        }

        return redirect()->to(base_url('login'))
            ->with('error', 'Failed to reset password.');
    }
}

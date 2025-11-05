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

        // Send email (you'll need to configure email in Config/Email.php)
        $emailService = \Config\Services::email();

        $emailService->setFrom('noreply@bhaviclients.com', 'Bhavi Clients');
        $emailService->setTo($email);
        $emailService->setSubject('Password Reset Request');

        $message = "Hello,\n\n";
        $message .= "You requested a password reset. Click the link below to reset your password:\n\n";
        $message .= $resetLink . "\n\n";
        $message .= "This link will expire in 1 hour.\n\n";
        $message .= "If you didn't request this, please ignore this email.\n\n";
        $message .= "Thanks,\nBhavi Clients Team";

        $emailService->setMessage($message);

        if ($emailService->send()) {
            return redirect()->to(base_url('login'))
                ->with('message', 'Password reset link sent to your email!');
        } else {
            // If email fails, show the link for development
            return redirect()->back()
                ->with('message', 'Reset link: ' . $resetLink);
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

<?php

namespace App\Validation;

use App\Models\ClientModel;
use App\Models\UserModel;

class CustomRules
{
    protected $clientModel;
    protected $userModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->userModel = new UserModel();
    }

    // Your existing after_or_equal_to method
    public function after_or_equal_to(string $str = null, string $field = null, array $data = []): bool
    {
        $compareToValue = $data[$field] ?? null;

        if (empty($str) || empty($compareToValue) || strtotime($str) === false || strtotime($compareToValue) === false) {
            return false;
        }
        return strtotime($str) >= strtotime($compareToValue);
    }

    // Fixed validateUniqueEmail with nullable $fields parameter
    public function validateUniqueEmail(string $email, ?string $fields = null, array $data = []): bool
    {
        // Add safe explode with default empty string to avoid errors
        [$clientId, $userId] = explode(',', $fields . ',');
        $clientId = $clientId ?: null;
        $userId = $userId ?: null;

        $clientExists = $this->clientModel->where('email', $email)
            ->where('id !=', $clientId)
            ->countAllResults() > 0;

        $userExists = $this->userModel->where('email', $email)
            ->where('id !=', $userId)
            ->countAllResults() > 0;

        return !($clientExists || $userExists);
    }

    // Fixed validateUniquePhone with nullable $fields parameter
    public function validateUniquePhone(string $phone, ?string $userId = null, array $data = []): bool
    {
        $userId = $userId ?: null;

        $phoneExists = $this->userModel->where('phone', $phone)
            ->where('id !=', $userId)
            ->countAllResults() > 0;

        return !$phoneExists;
    }
}

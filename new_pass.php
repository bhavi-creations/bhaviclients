<?php
$phone = 'password123'; // current phone number from patients table
$hash = password_hash(trim($phone), PASSWORD_DEFAULT);
echo "Generated hash: $hash\n";

if (password_verify(trim($phone), $hash)) {
    echo "Password is valid!\n";
} else {
    echo "Password is NOT valid!\n";
}

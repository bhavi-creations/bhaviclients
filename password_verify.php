<?php
$phone = '7777788888'; // confirm exact phone number you want to test
$hash = '$2y$10$3ivSayHUFMyEbbelpb6IYOmoN8OnuSkxpVuO6zeL/S2RFrXvm27Ky'; // full hash from users table

if (password_verify(trim($phone), $hash)) {
    echo "Password is valid for this phone number.";
} else {
    echo "Password does NOT match for this phone number.";
}

?>
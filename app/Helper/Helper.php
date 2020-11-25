<?php
use App\Models\User;

if (!function_exists('getLastUserId')) {
    function getLastUserId() {
        $user = User::latest('id')->first();
        if ($user) return $user->id;
        return 0;
    }
}
if (!function_exists('getCurrentTime'))
{
    function getCurrentTime() {
        $currentTime = date("Y-m-d H:i:s");
        return $currentTime;
    }
}
?>

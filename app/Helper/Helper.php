<?php
use App\Models\User;

if (!function_exists('getLastUserId')) {
    function getLastUserId() {
        $id = User::latest('id')->first();
        if ($id) return $id;
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

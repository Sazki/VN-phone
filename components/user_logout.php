<?php

include 'connect.php';

session_start();
session_unset();
session_destroy();

if (isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time() - 3600, '/'); // Xóa cookie nếu có
}

header('location:../home.php');
<?php
require_once __DIR__ . '/../includes/init.php';


if (!isset($_SESSION['admin'])) {
    header('Location: ' . INDEX_URL . 'admin/login.php');
    exit();
}


unset($_SESSION['admin']);
session_destroy();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}
header('Location: ' . INDEX_URL . 'admin/login.php');
exit();
<?php
require_once __DIR__ . '/includes/init.php';
if (!isset($_SESSION['sinhvien'])) {
    header('Location: ' . INDEX_URL . 'login.php');
    exit();
}
unset($_SESSION['sinhvien']);
session_destroy();

// Xóa cookie session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

header('Location: ' . INDEX_URL . 'login.php');
exit();

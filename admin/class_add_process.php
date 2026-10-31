<?php
require_once __DIR__ . '/../includes/init.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ' . INDEX_URL . 'admin/login.php');
    exit();
}

$malop = trim($_POST['malop'] ?? '');
$tenlop = trim($_POST['tenlop'] ?? '');
$makhoa = trim($_POST['makhoa'] ?? '');
if (empty($malop) || empty($tenlop) || empty($makhoa)) {
    $_SESSION['error'] = 'Vui lòng điền đầy đủ mã lớp, tên lớp và chọn khoa.';
    header('Location: ' . INDEX_URL . 'admin/class_add.php');
    exit();
}

if (strlen($malop) > 10) {
    $_SESSION['error'] = 'Mã lớp không được vượt quá 10 ký tự.';
    header('Location: ' . INDEX_URL . 'admin/class_add.php');
    exit();
}

if (strlen($tenlop) > 100) {
    $_SESSION['error'] = 'Tên lớp không được vượt quá 100 ký tự.';
    header('Location: ' . INDEX_URL . 'admin/class_add.php');
    exit();
}
$existing = DB::selectOne('SELECT malop FROM lop WHERE malop = :malop', ['malop' => $malop]);
if ($existing) {
    $_SESSION['error'] = 'Mã lớp đã tồn tại trong hệ thống.';
    header('Location: ' . INDEX_URL . 'admin/class_add.php');
    exit();
}


$khoa = DB::selectOne('SELECT makhoa FROM khoa WHERE makhoa = :makhoa', ['makhoa' => $makhoa]);
if (!$khoa) {
    $_SESSION['error'] = 'Khoa không tồn tại trong hệ thống.';
    header('Location: ' . INDEX_URL . 'admin/class_add.php');
    exit();
}


try {
    $sql = "INSERT INTO lop (malop, tenlop, makhoa) VALUES (:malop, :tenlop, :makhoa)";
    $stmt = DB::getPdo()->prepare($sql);
    $stmt->execute([
        'malop' => $malop,
        'tenlop' => $tenlop,
        'makhoa' => $makhoa
    ]);

    $_SESSION['success'] = "Đã thêm lớp '$tenlop' thành công!";
    header('Location: ' . INDEX_URL . 'admin/class_list.php');
    exit();
} catch (Exception $e) {
    $_SESSION['error'] = 'Có lỗi xảy ra khi thêm lớp. Vui lòng thử lại.';
    header('Location: ' . INDEX_URL . 'admin/class_add.php');
    exit();
}
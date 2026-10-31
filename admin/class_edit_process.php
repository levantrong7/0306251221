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
    header('Location: ' . INDEX_URL . 'admin/class_edit.php?malop=' . urlencode($malop));
    exit();
}

if (strlen($tenlop) > 100) {
    $_SESSION['error'] = 'Tên lớp không được vượt quá 100 ký tự.';
    header('Location: ' . INDEX_URL . 'admin/class_edit.php?malop=' . urlencode($malop));
    exit();
}

$existing = DB::selectOne('SELECT tenlop FROM lop WHERE malop = :malop', ['malop' => $malop]);
if (!$existing) {
    $_SESSION['error'] = 'Không tìm thấy lớp cần cập nhật.';
    header('Location: ' . INDEX_URL . 'admin/class_list.php');
    exit();
}

$khoa = DB::selectOne('SELECT makhoa FROM khoa WHERE makhoa = :makhoa', ['makhoa' => $makhoa]);
if (!$khoa) {
    $_SESSION['error'] = 'Khoa không tồn tại trong hệ thống.';
    header('Location: ' . INDEX_URL . 'admin/class_edit.php?malop=' . urlencode($malop));
    exit();
}
try {
    DB::update('lop', 
        ['tenlop' => $tenlop, 'makhoa' => $makhoa],
        'malop = :malop',
        ['malop' => $malop]
    );

    $_SESSION['success'] = "Đã cập nhật thông tin lớp '$tenlop' thành công!";
    header('Location: ' . INDEX_URL . 'admin/class_list.php');
    exit();
} catch (Exception $e) {
    $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật thông tin lớp. Vui lòng thử lại.';
    header('Location: ' . INDEX_URL . 'admin/class_edit.php?malop=' . urlencode($malop));
    exit();
}
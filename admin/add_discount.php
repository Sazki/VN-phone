<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['add_discount'])) {
    $code = $_POST['code'];
    $discount_percent = $_POST['discount_percent'];
    $valid_from = $_POST['valid_from'];
    $valid_until = $_POST['valid_until'];

    // Kiểm tra nếu mã giảm giá đã tồn tại
    $check_code = $conn->prepare("SELECT * FROM `discounts` WHERE `code` = ?");
    $check_code->execute([$code]);

    if ($check_code->rowCount() > 0) {
        $message[] = 'Mã giảm giá này đã tồn tại. Vui lòng chọn mã khác!';
    } else {
        // Thêm mã giảm giá vào cơ sở dữ liệu
        $insert_discount = $conn->prepare("INSERT INTO `discounts` (code, discount_percent, valid_from, valid_until, status) 
                                       VALUES (?, ?, ?, ?, ?)");
        $insert_discount->execute([$code, $discount_percent, $valid_from, $valid_until, 'còn hạn']);
        $message[] = 'Thêm mã giảm giá thành công!';

        // Quay lại trang quản lý mã giảm giá
        header('location: discounts.php');
    }
}
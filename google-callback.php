<?php
require_once 'vendor/autoload.php'; // Import thư viện Google API Client
require 'components/connect.php'; // Kết nối database
session_start();

if (isset($_POST['credential'])) {
    // Lấy thông tin người dùng từ Google
    $id_token = $_POST['credential'];

    // Tạo một client Google
    $client = new Google_Client(['client_id' => '792266070129-cucpol6ejm3c9se9lbs10fer54k429ce.apps.googleusercontent.com']);  // Thay bằng Client ID của bạn
    $payload = $client->verifyIdToken($id_token);

    if ($payload) {
        $email = $payload['email'];
        $name = $payload['name'];

        // Kiểm tra người dùng trong cơ sở dữ liệu
        $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $select_user->execute([$email]);
        $row = $select_user->fetch(PDO::FETCH_ASSOC);

        if ($select_user->rowCount() > 0) {
            // Người dùng tồn tại, đăng nhập
            $_SESSION['user_id'] = $row['userID'];
            header('location:home.php');
        } else {
            // Người dùng chưa tồn tại, thêm vào database
            $insert_user = $conn->prepare("INSERT INTO `users` (email, name, phoneNumber, password, address, role) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_user->execute([$email, $name,'', '', '', 'client']);
            $_SESSION['user_id'] = $conn->lastInsertId();
            header('location:home.php');
        }
    } else {
        error_log("Lỗi xác thực: " . json_encode($client->getLastError()));
    echo "Không thể xác thực Google.";
    }
} else {
    echo "Không tìm thấy thông tin đăng nhập.";
}

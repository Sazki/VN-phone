<?php
// Kết nối cơ sở dữ liệu
include '../components/connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer autoload
require '../vendor/autoload.php';

if (isset($message)) {
    foreach ($message as $message) {
        echo '
      <div class="message">
         <span>' . $message . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
    }
}

// Kiểm tra nếu có discountID và userID
if (isset($_GET['discountID']) && isset($_GET['userID'])) {
    $discountID = $_GET['discountID'];
    $userID = $_GET['userID'];

    // Lấy thông tin mã giảm giá
    $select_discount = $conn->prepare("SELECT * FROM `discounts` WHERE discountID = ?");
    $select_discount->execute([$discountID]);
    $discount = $select_discount->fetch(PDO::FETCH_ASSOC);

    // Lấy thông tin người dùng
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE userID = ?");
    $select_user->execute([$userID]);
    $user = $select_user->fetch(PDO::FETCH_ASSOC);

    if ($discount && $user) {
        // Thông tin mã giảm giá
        $discountCode = $discount['code'];
        $discountPercent = $discount['discount_percent'];
        $validFrom = $discount['valid_from'];
        $validUntil = $discount['valid_until'];

        // Thông tin người nhận
        $userEmail = $user['email'];
        $userName = $user['name'];

        // Tạo đối tượng PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Sử dụng Gmail SMTP
            // $mail->SMTPDebug = 1;
            $mail->SMTPAuth = true;
            $mail->Username = 'huuviet19905@gmail.com';  // Địa chỉ email của bạn
            $mail->Password = 'vhabuiyfyxenxqqx';  // Mật khẩu email của bạn
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Người gửi và người nhận
            $mail->setFrom('huuviet19905@gmail.com', 'VN Food');
            $mail->addAddress($userEmail, $userName);  // Địa chỉ email người nhận

            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = 'COUPON VN-Food';
            $mail->Body    = "
                <p>Xin chào {$userName},</p>
                <p>Cảm ơn bạn đã sử dụng dịch vụ của VN Food! Chúng tôi rất vui được gửi đến bạn một mã giảm giá đặc biệt:</p>
                <p><strong>Mã giảm giá: $discountCode</strong></p>
                <p>Phần trăm giảm: $discountPercent%</p>
                <p>Hiệu lực từ: $validFrom</p>
                <p>Hết hạn vào: $validUntil</p>
                <p>Chúc bạn có trải nghiệm tuyệt vời với VN Food!</p>
                <p>Trân trọng,</p>
                <p>VN Food Team</p>
            ";

            // Gửi email
            $mail->send();

            $message[] = 'Đã gửi email thành công .';
            // Chuyển hướng về trang discount sau khi gửi email thành công
            header('Location: discounts.php');
            exit;
        } catch (Exception $e) {
            echo "Lỗi khi gửi email: {$mail->ErrorInfo}";
        }
    } else {
        echo 'Mã giảm giá hoặc người dùng không hợp lệ!';
    }
}
<?php
// vnpay_return.php - Đã sửa lỗi xử lý kết quả
session_start();
include 'components/connect.php';
require_once 'vnpay_config.php';

// Set timezone để đảm bảo thời gian chính xác
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('location: home.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = [];
$payment_success = false;

// Xử lý kết quả trả về từ VNPay
if (!empty($_GET)) {
    $inputData = array();
    foreach ($_GET as $key => $value) {
        if (substr($key, 0, 4) == "vnp_") {
            $inputData[$key] = $value;
        }
    }

    $vnp_TxnRef = $inputData['vnp_TxnRef'] ?? '';
    $vnp_Amount = $inputData['vnp_Amount'] ?? 0;
    $vnp_OrderInfo = $inputData['vnp_OrderInfo'] ?? '';
    $vnp_ResponseCode = $inputData['vnp_ResponseCode'] ?? '';
    $vnp_TransactionNo = $inputData['vnp_TransactionNo'] ?? '';
    $vnp_BankCode = $inputData['vnp_BankCode'] ?? '';
    $vnp_PayDate = $inputData['vnp_PayDate'] ?? '';
    $vnp_TransactionStatus = $inputData['vnp_TransactionStatus'] ?? '';

    // Xác thực chữ ký
    $secureHash = VNPayConfig::validateSecureHash($inputData);

    if ($secureHash) {
        // Chữ ký hợp lệ

        // Kiểm tra session order info có tồn tại và chưa hết hạn
        if (!isset($_SESSION['order_info'])) {
            $message[] = 'Phiên làm việc đã hết hạn. Vui lòng đặt hàng lại.';
            $payment_success = false;
        } else {
            $order_info = $_SESSION['order_info'];

            // Kiểm tra timeout (30 phút)
            $session_timeout = 30 * 60; // 30 phút
            if (isset($order_info['created_at']) && (time() - $order_info['created_at']) > $session_timeout) {
                $message[] = 'Phiên giao dịch đã hết hạn. Vui lòng đặt hàng lại.';
                $payment_success = false;
                // Xóa session cũ
                unset($_SESSION['order_info']);
                unset($_SESSION['vnp_TxnRef']);
            } else {
                // Kiểm tra mã phản hồi và trạng thái giao dịch
                if ($vnp_ResponseCode == "00" && $vnp_TransactionStatus == "00") {
                    // Thanh toán thành công

                    // Kiểm tra lại giỏ hàng
                    $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE userID = ?");
                    $check_cart->execute([$user_id]);

                    if ($check_cart->rowCount() > 0) {
                        try {
                            // Bắt đầu transaction
                            $conn->beginTransaction();

                            // Kiểm tra xem đơn hàng đã được tạo chưa (tránh trùng lặp)
                            $check_existing_order = $conn->prepare("SELECT * FROM `orders` WHERE userID = ? AND total_price = ? AND placed_on > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
                            $check_existing_order->execute([$user_id, $order_info['total_price']]);

                            if ($check_existing_order->rowCount() == 0) {
                                // Chèn đơn hàng vào database với trạng thái đã thanh toán
                                $insert_order = $conn->prepare("INSERT INTO `orders`(userID, name, phoneNumber, email, method, address, total_products, total_price, payment_status, placed_on) VALUES(?,?,?,?,?,?,?,?,?,NOW())");

                                $insert_order->execute([
                                    $order_info['user_id'],
                                    $order_info['name'],
                                    $order_info['number'],
                                    $order_info['email'],
                                    'thẻ tín dụng (VNPay)',
                                    $order_info['address'],
                                    $order_info['total_products'],
                                    $order_info['total_price'],
                                    'đã thanh toán'
                                ]);

                                // Xóa giỏ hàng
                                $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE userID = ?");
                                $delete_cart->execute([$user_id]);

                                $message[] = 'Thanh toán thành công! Đơn hàng đã được đặt.';
                                $payment_success = true;
                            } else {
                                $message[] = 'Đơn hàng đã được tạo trước đó.';
                                $payment_success = true;
                            }

                            // Commit transaction
                            $conn->commit();
                        } catch (Exception $e) {
                            // Rollback nếu có lỗi
                            $conn->rollback();
                            $message[] = 'Có lỗi xảy ra khi xử lý đơn hàng: ' . $e->getMessage();
                            $payment_success = false;
                        }
                    } else {
                        $message[] = 'Giỏ hàng đã được xóa trước đó hoặc đơn hàng đã được xử lý.';
                        $payment_success = true; // Vẫn coi là thành công vì thanh toán đã OK
                    }

                    // Xóa thông tin đơn hàng khỏi session
                    unset($_SESSION['order_info']);
                    unset($_SESSION['vnp_TxnRef']);
                } else {
                    // Thanh toán thất bại hoặc bị hủy
                    $error_messages = [
                        '01' => 'Giao dịch không thành công do thẻ hết hạn',
                        '02' => 'Giao dịch không thành công do thẻ bị khóa',
                        '03' => 'Giao dịch không thành công do code không đúng',
                        '04' => 'Giao dịch không thành công do chưa đăng ký dịch vụ',
                        '05' => 'Giao dịch không thành công do nhập sai quá số lần quy định',
                        '06' => 'Giao dịch không thành công do khách hàng hủy giao dịch',
                        '07' => 'Giao dịch bị từ chối do trùng lặp',
                        '09' => 'Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ',
                        '10' => 'Khách hàng xác thực thông tin thẻ không đúng quá 3 lần',
                        '11' => 'Đã hết hạn chờ thanh toán. Vui lòng thực hiện lại giao dịch',
                        '12' => 'Thẻ/Tài khoản của khách hàng bị khóa',
                        '13' => 'Quý khách nhập sai mật khẩu xác thực giao dịch (OTP)',
                        '24' => 'Khách hàng hủy giao dịch',
                        '51' => 'Tài khoản của quý khách không đủ số dư để thực hiện giao dịch',
                        '65' => 'Tài khoản của quý khách đã vượt quá hạn mức giao dịch trong ngày',
                        '75' => 'Ngân hàng thanh toán đang bảo trì',
                        '79' => 'KH nhập sai mật khẩu thanh toán quá số lần quy định',
                        '99' => 'Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê)'
                    ];

                    $error_msg = $error_messages[$vnp_ResponseCode] ?? 'Giao dịch không thành công (Mã lỗi: ' . $vnp_ResponseCode . ')';
                    $message[] = 'Thanh toán thất bại: ' . $error_msg;
                    $payment_success = false;
                }
            }
        }
    } else {
        // Chữ ký không hợp lệ
        $message[] = 'Chữ ký không hợp lệ. Giao dịch có thể bị giả mạo.';
        $payment_success = false;
    }
} else {
    $message[] = 'Không có dữ liệu trả về từ VNPay';
    $payment_success = false;
}

// Lấy thông tin user
$select_profile = $conn->prepare("SELECT * FROM `users` WHERE userID = ?");
$select_profile->execute([$user_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả thanh toán</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        .payment-result {
            max-width: 600px;
            margin: 120px auto 50px;
            padding: 30px;
            text-align: center;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .payment-success {
            border-left: 5px solid #4caf50;
            background: #f1f8e9;
        }

        .payment-failed {
            border-left: 5px solid #f44336;
            background: #ffebee;
        }

        .payment-result h1 {
            margin-bottom: 20px;
            font-size: 28px;
        }

        .payment-success h1 {
            color: #4caf50;
        }

        .payment-failed h1 {
            color: #f44336;
        }

        .payment-details {
            background: #fafafa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }

        .payment-details p {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .payment-details p:last-child {
            border-bottom: none;
        }

        .payment-details strong {
            color: #333;
            display: inline-block;
            width: 150px;
        }

        .btn-group {
            margin-top: 30px;
        }

        .btn-group .btn {
            margin: 0 10px;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #2196f3;
            color: white;
        }

        .btn-primary:hover {
            background: #1976d2;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .payment-success .icon {
            color: #4caf50;
        }

        .payment-failed .icon {
            color: #f44336;
        }

        .debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
            font-family: monospace;
            font-size: 12px;
            display: none;
            /* Ẩn debug info - chỉ hiện khi cần */
        }
    </style>
</head>

<body>
    <!-- header section starts -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <section class="payment-result <?= $payment_success ? 'payment-success' : 'payment-failed' ?>">
        <div class="icon">
            <?php if ($payment_success): ?>
                <i class="fas fa-check-circle"></i>
            <?php else: ?>
                <i class="fas fa-times-circle"></i>
            <?php endif; ?>
        </div>

        <h1>
            <?php if ($payment_success): ?>
                Thanh toán thành công!
            <?php else: ?>
                Thanh toán thất bại!
            <?php endif; ?>
        </h1>

        <?php if (!empty($message)): ?>
            <?php foreach ($message as $msg): ?>
                <p style="margin: 15px 0; font-size: 16px; color: <?= $payment_success ? '#4caf50' : '#f44336' ?>;">
                    <?= htmlspecialchars($msg) ?>
                </p>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($_GET) && isset($vnp_TxnRef)): ?>
            <div class="payment-details">
                <h3>Chi tiết giao dịch:</h3>
                <p><strong>Mã đơn hàng:</strong> <?= htmlspecialchars($vnp_TxnRef) ?></p>
                <?php if (isset($vnp_Amount) && $vnp_Amount > 0): ?>
                    <p><strong>Số tiền:</strong> <?= number_format($vnp_Amount / 100) ?>₫</p>
                <?php endif; ?>
                <?php if (!empty($vnp_TransactionNo)): ?>
                    <p><strong>Mã giao dịch VNPay:</strong> <?= htmlspecialchars($vnp_TransactionNo) ?></p>
                <?php endif; ?>
                <?php if (!empty($vnp_BankCode)): ?>
                    <p><strong>Ngân hàng:</strong> <?= htmlspecialchars($vnp_BankCode) ?></p>
                <?php endif; ?>
                <?php if (!empty($vnp_PayDate) && strlen($vnp_PayDate) == 14): ?>
                    <p><strong>Thời gian thanh toán:</strong>
                        <?= date('d/m/Y H:i:s', strtotime(substr($vnp_PayDate, 0, 8) . ' ' . substr($vnp_PayDate, 8, 6))) ?>
                    </p>
                <?php endif; ?>
                <p><strong>Mã phản hồi:</strong> <?= htmlspecialchars($vnp_ResponseCode) ?></p>
            </div>
        <?php endif; ?>

        <div class="btn-group">
            <?php if ($payment_success): ?>
                <a href="orders.php" class="btn btn-primary">
                    <i class="fas fa-list"></i> Xem đơn hàng
                </a>
            <?php else: ?>
                <a href="checkout.php" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Thử lại
                </a>
            <?php endif; ?>
            <a href="home.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Về trang chủ
            </a>
        </div>
    </section>

    <!-- footer section starts -->
    <?php include 'components/footer.php'; ?>
    <!-- footer section ends -->

    <!-- custom js file link -->
    <script src="js/script.js"></script>
</body>

</html>
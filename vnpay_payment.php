<?php
// vnpay_payment.php - Đã sửa lỗi timeout
session_start();
include 'components/connect.php';
require_once 'vnpay_config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('location: home.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin từ form checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8');
    $number = htmlspecialchars($_POST['number'] ?? '', ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES, 'UTF-8');
    $total_products = htmlspecialchars($_POST['total_products'] ?? '', ENT_QUOTES, 'UTF-8');
    $discount_code = htmlspecialchars($_POST['discount_code'] ?? '', ENT_QUOTES, 'UTF-8');
    
    // Xử lý giá tiền - loại bỏ tất cả ký tự không phải số
    $total_price = isset($_POST['total_price']) ? (int)preg_replace('/\D/', '', $_POST['total_price']) : 0;
    $total_price_coupon = isset($_POST['total_price_coupon']) ? (int)preg_replace('/\D/', '', $_POST['total_price_coupon']) : 0;
    
    // Sử dụng giá sau giảm giá nếu có
    $final_price = (!empty($discount_code) && $total_price_coupon > 0) ? $total_price_coupon : $total_price;
    
    // Kiểm tra số tiền hợp lệ (tối thiểu 1000 VND)
    if ($final_price < 1000) {
        $_SESSION['error_message'] = 'Số tiền thanh toán không hợp lệ (tối thiểu 1,000 VND)';
        header('location: checkout.php');
        exit();
    }
    
    // Kiểm tra giỏ hàng
    $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE userID = ?");
    $check_cart->execute([$user_id]);
    
    if ($check_cart->rowCount() == 0) {
        $_SESSION['error_message'] = 'Giỏ hàng của bạn đang trống';
        header('location: checkout.php');
        exit();
    }
    
    if (empty($address)) {
        $_SESSION['error_message'] = 'Vui lòng thêm địa chỉ của bạn!';
        header('location: checkout.php');
        exit();
    }
    
    // Lưu thông tin đơn hàng vào session để xử lý sau khi thanh toán thành công
    $_SESSION['order_info'] = [
        'user_id' => $user_id,
        'name' => $name,
        'number' => $number,
        'email' => $email,
        'address' => $address,
        'total_products' => $total_products,
        'total_price' => $final_price,
        'discount_code' => $discount_code,
        'created_at' => time() // Thêm timestamp để kiểm tra timeout
    ];
    
    // Tạo mã đơn hàng duy nhất
    $vnp_TxnRef = VNPayConfig::generateTxnRef($user_id);
    $vnp_OrderInfo = 'Thanh toan don hang ' . $vnp_TxnRef;
    $vnp_OrderType = 'billpayment';
    $vnp_Amount = $final_price * 100; // VNPay yêu cầu số tiền * 100
    $vnp_Locale = 'vn';
    $vnp_BankCode = '';
    $vnp_IpAddr = VNPayConfig::getClientIP();
    
    // Thời gian tạo và hết hạn (format: YmdHis)
    $vnp_CreateDate = date('YmdHis');
    $vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes')); // Hết hạn sau 15 phút
    
    // Tạo dữ liệu để gửi đến VNPay
    $inputData = array(
        "vnp_Version" => "2.1.0",
        "vnp_TmnCode" => VNPayConfig::VNP_TMN_CODE,
        "vnp_Amount" => $vnp_Amount,
        "vnp_Command" => "pay",
        "vnp_CreateDate" => $vnp_CreateDate,
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => $vnp_IpAddr,
        "vnp_Locale" => $vnp_Locale,
        "vnp_OrderInfo" => $vnp_OrderInfo,
        "vnp_OrderType" => $vnp_OrderType,
        "vnp_ReturnUrl" => VNPayConfig::VNP_RETURN_URL,
        "vnp_TxnRef" => $vnp_TxnRef,
        "vnp_ExpireDate" => $vnp_ExpireDate
    );
    
    if (isset($vnp_BankCode) && $vnp_BankCode != "") {
        $inputData['vnp_BankCode'] = $vnp_BankCode;
    }
    
    // Tạo URL thanh toán
    $vnpay_url = VNPayConfig::createSecureHash($inputData);
    
    // Lưu transaction reference vào session
    $_SESSION['vnp_TxnRef'] = $vnp_TxnRef;
    
    // Kiểm tra URL được tạo
    if (empty($vnpay_url) || !filter_var($vnpay_url, FILTER_VALIDATE_URL)) {
        $_SESSION['error_message'] = 'Không thể tạo URL thanh toán. Vui lòng thử lại.';
        header('location: checkout.php');
        exit();
    }
    
    // Debug - Có thể bỏ comment để kiểm tra
    /*
    echo "Debug info:<br>";
    echo "User ID: " . $user_id . "<br>";
    echo "Final price: " . $final_price . "<br>";
    echo "VNPay Amount: " . $vnp_Amount . "<br>";
    echo "TxnRef: " . $vnp_TxnRef . "<br>";
    echo "CreateDate: " . $vnp_CreateDate . "<br>";
    echo "ExpireDate: " . $vnp_ExpireDate . "<br>";
    echo "IP Address: " . $vnp_IpAddr . "<br>";
    echo "Return URL: " . VNPayConfig::VNP_RETURN_URL . "<br>";
    echo "<a href='" . $vnpay_url . "'>Test URL</a><br>";
    exit();
    */
    
    // Chuyển hướng đến VNPay
    header('Location: ' . $vnpay_url);
    exit();
} else {
    // Nếu không có dữ liệu POST, quay về trang checkout
    $_SESSION['error_message'] = 'Dữ liệu thanh toán không hợp lệ';
    header('location: checkout.php');
    exit();
}
?>
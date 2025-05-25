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
            $mail->setFrom('sazki2k5@gmail.com', 'VN-PHONE');
            $mail->addAddress($userEmail, $userName);  // Địa chỉ email người nhận

            // Nội dung email đẹp và chuyên nghiệp
            $mail->isHTML(true);
            $mail->Subject = '🎁 MÃ GIẢM GIÁ ĐẶC BIỆT TỪ VN-PHONE - TIẾT KIỆM NGAY!';
            $mail->Body    = "
            <!DOCTYPE html>
            <html lang='vi'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>VN-PHONE Coupon</title>
            </head>
            <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 4px 15px rgba(0,0,0,0.1);'>
                    
                    <!-- Header -->
                    <div style='background: linear-gradient(135deg, #FF6B35, #FF8E35); padding: 30px 20px; text-align: center; color: white;'>
                        <h1 style='margin: 0; font-size: 28px; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);'>
                            📱 VN-PHONE
                        </h1>
                        <p style='margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;'>
                            Siêu thị điện thoại uy tín #1 Việt Nam
                        </p>
                    </div>

                    <!-- Main Content -->
                    <div style='padding: 40px 30px;'>
                        <h2 style='color: #2c3e50; margin-bottom: 20px; font-size: 24px;'>
                            🎉 Chào bạn <span style='color: #FF6B35;'>{$userName}</span>!
                        </h2>
                        
                        <p style='color: #555; font-size: 16px; line-height: 1.6; margin-bottom: 25px;'>
                            Cảm ơn bạn đã tin tưởng và lựa chọn <strong>VN-PHONE</strong> - nơi quy tụ những chiếc điện thoại hot nhất từ các thương hiệu hàng đầu như iPhone, Samsung, Xiaomi, Oppo và nhiều hãng khác!
                        </p>

                        <!-- Coupon Box -->
                        <div style='background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 15px; padding: 30px; text-align: center; margin: 30px 0; position: relative; overflow: hidden;'>
                            <div style='position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%;'></div>
                            <div style='position: absolute; bottom: -30px; left: -30px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%;'></div>
                            
                            <h3 style='color: white; margin: 0 0 15px 0; font-size: 22px;'>
                                🎫 MÃ GIẢM GIÁ ĐẶC BIỆT
                            </h3>
                            <div style='background: white; border-radius: 10px; padding: 20px; margin: 20px 0; box-shadow: 0 5px 15px rgba(0,0,0,0.2);'>
                                <div style='font-size: 32px; font-weight: bold; color: #FF6B35; letter-spacing: 3px; margin-bottom: 10px;'>
                                    {$discountCode}
                                </div>
                                <div style='font-size: 20px; color: #e74c3c; font-weight: bold;'>
                                    GIẢM {$discountPercent}%
                                </div>
                            </div>
                            <p style='color: rgba(255,255,255,0.9); margin: 0; font-size: 14px;'>
                                ✨ Sao chép mã và áp dụng ngay khi thanh toán
                            </p>
                        </div>

                        <!-- Product Categories -->
                        <div style='background: #f8f9fa; border-radius: 10px; padding: 25px; margin: 25px 0;'>
                            <h4 style='color: #2c3e50; margin: 0 0 15px 0; font-size: 18px; text-align: center;'>
                                🔥 ÁP DỤNG CHO TẤT CẢ SẢN PHẨM
                            </h4>
                            <div style='display: flex; justify-content: space-around; flex-wrap: wrap; text-align: center;'>
                                <div style='margin: 10px;'>
                                    <div style='font-size: 24px; margin-bottom: 5px;'>📱</div>
                                    <div style='font-size: 14px; color: #666; font-weight: bold;'>iPhone</div>
                                </div>
                                <div style='margin: 10px;'>
                                    <div style='font-size: 24px; margin-bottom: 5px;'>📱</div>
                                    <div style='font-size: 14px; color: #666; font-weight: bold;'>Samsung</div>
                                </div>
                                <div style='margin: 10px;'>
                                    <div style='font-size: 24px; margin-bottom: 5px;'>📱</div>
                                    <div style='font-size: 14px; color: #666; font-weight: bold;'>Xiaomi</div>
                                </div>
                                <div style='margin: 10px;'>
                                    <div style='font-size: 24px; margin-bottom: 5px;'>📱</div>
                                    <div style='font-size: 14px; color: #666; font-weight: bold;'>Oppo</div>
                                </div>
                            </div>
                        </div>

                        <!-- Validity Info -->
                        <div style='border-left: 4px solid #FF6B35; padding-left: 20px; margin: 25px 0;'>
                            <h4 style='color: #2c3e50; margin: 0 0 10px 0;'>⏰ Thời hạn áp dụng:</h4>
                            <p style='margin: 5px 0; color: #555;'>
                                <strong>Từ:</strong> <span style='color: #27ae60;'>" . date('d/m/Y', strtotime($validFrom)) . "</span>
                            </p>
                            <p style='margin: 5px 0; color: #555;'>
                                <strong>Đến:</strong> <span style='color: #e74c3c;'>" . date('d/m/Y', strtotime($validUntil)) . "</span>
                            </p>
                        </div>

                        <!-- Call to Action -->
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='#' style='display: inline-block; background: linear-gradient(135deg, #FF6B35, #FF8E35); color: white; padding: 15px 40px; text-decoration: none; border-radius: 25px; font-weight: bold; font-size: 16px; box-shadow: 0 5px 15px rgba(255,107,53,0.4); transition: all 0.3s ease;'>
                                🛒 MUA SẮM NGAY
                            </a>
                        </div>

                        <!-- Additional Info -->
                        <div style='background: #e8f5e8; border-radius: 8px; padding: 20px; margin-top: 25px;'>
                            <h4 style='color: #2d8f3f; margin: 0 0 10px 0; font-size: 16px;'>
                                🌟 Tại sao chọn VN-PHONE?
                            </h4>
                            <ul style='color: #2d8f3f; margin: 0; padding-left: 20px; font-size: 14px;'>
                                <li>✅ Sản phẩm chính hãng 100%</li>
                                <li>✅ Bảo hành toàn quốc</li>
                                <li>✅ Giao hàng nhanh toàn quốc</li>
                                <li>✅ Hỗ trợ trả góp 0% lãi suất</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div style='background: #2c3e50; color: white; padding: 25px 30px; text-align: center;'>
                        <p style='margin: 0 0 15px 0; font-size: 16px; font-weight: bold;'>
                            📞 LIÊN HỆ HỖ TRỢ
                        </p>
                        <p style='margin: 5px 0; font-size: 14px;'>
                            Hotline: <strong>1900-xxxx</strong> | Email: <strong>support@vnphone.vn</strong>
                        </p>
                        <p style='margin: 15px 0 5px 0; font-size: 12px; opacity: 0.8;'>
                            © 2024 VN-PHONE. All rights reserved.
                        </p>
                        <p style='margin: 0; font-size: 11px; opacity: 0.7;'>
                            Bạn nhận được email này vì đã đăng ký nhận thông tin từ VN-PHONE
                        </p>
                    </div>
                </div>
            </body>
            </html>
            ";

            // Gửi email
            $mail->send();

            $message[] = 'Đã gửi mã giảm giá qua email thành công!';
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
?>
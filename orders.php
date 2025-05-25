<?php

include 'components/connect.php';
require 'vendor/autoload.php'; // Thêm autoload cho PHPMailer và mPDF

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:home.php');
}

// Xử lý khi người dùng nhấn nút "Đã nhận hàng"
if (isset($_POST['confirm_received'])) {
   $order_id = $_POST['order_id'];

   // Lấy thông tin đơn hàng từ cơ sở dữ liệu
   $select_order = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
   $select_order->execute([$order_id]);

   if ($select_order->rowCount() > 0) {
      $order = $select_order->fetch(PDO::FETCH_ASSOC);

      // Cập nhật trạng thái đơn hàng thành "đã giao hàng"
      $update_status = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
      $update_status->execute(['đã giao hàng', $order_id]);

      // Tạo PDF hóa đơn
      $mpdf = new \Mpdf\Mpdf();

      // Nội dung hóa đơn
      $html = '
       <div style="font-family: Arial, sans-serif; padding: 20px; font-size: 12px; line-height: 1.4;">
           <!-- Header -->
           <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #FF6B35; padding-bottom: 15px;">
               <h1 style="color: #FF6B35; font-size: 28px; margin: 0;">📱 VN-PHONE</h1>
               <p style="color: #666; margin: 2px 0; font-size: 13px;">Chuyên cung cấp điện thoại chính hãng</p>
               <p style="color: #FF6B35; font-size: 16px; font-weight: bold; margin: 8px 0;">HÓA ĐƠN BÁN HÀNG</p>
           </div>

           <!-- Row 1: Thông tin hóa đơn và khách hàng -->
           <div style="display: flex; margin-bottom: 15px;">
               <div style="width: 48%; margin-right: 4%;">
                   <h4 style="color: #FF6B35; margin: 0 0 8px 0; font-size: 13px;">📋 THÔNG TIN HÓA ĐƠN</h4>
                   <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                       <tr><td style="padding: 3px 0; width: 45%;"><strong>Số HĐ:</strong></td><td style="color: #FF6B35; font-weight: bold;">VNP-' . str_pad($order_id, 6, '0', STR_PAD_LEFT) . '</td></tr>
                       <tr><td style="padding: 3px 0;"><strong>Ngày xuất:</strong></td><td>' . date('d/m/Y') . '</td></tr>
                       <tr><td style="padding: 3px 0;"><strong>Ngày đặt:</strong></td><td>' . date('d/m/Y', strtotime($order['placed_on'])) . '</td></tr>
                   </table>
               </div>
               <div style="width: 48%;">
                   <h4 style="color: #FF6B35; margin: 0 0 8px 0; font-size: 13px;">👤 THÔNG TIN KHÁCH HÀNG</h4>
                   <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                       <tr><td style="padding: 3px 0; width: 35%;"><strong>Mã KH:</strong></td><td>KH-' . str_pad($order['userID'], 4, '0', STR_PAD_LEFT) . '</td></tr>
                       <tr><td style="padding: 3px 0;"><strong>Tên:</strong></td><td style="font-weight: bold; color: #FF6B35;">' . $order['name'] . '</td></tr>
                       <tr><td style="padding: 3px 0;"><strong>SĐT:</strong></td><td style="font-weight: bold;">' . $order['phoneNumber'] . '</td></tr>
                   </table>
               </div>
           </div>

           <!-- Địa chỉ -->
           <div style="margin-bottom: 15px;">
               <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                   <tr><td style="padding: 3px 0; width: 12%;"><strong>Địa chỉ:</strong></td><td>' . $order['address'] . '</td></tr>
                   <tr><td style="padding: 3px 0;"><strong>Email:</strong></td><td>' . $order['email'] . '</td></tr>
               </table>
           </div>

           <!-- Chi tiết sản phẩm -->
           <div style="margin-bottom: 15px;">
               <h4 style="color: #FF6B35; margin: 0 0 8px 0; font-size: 13px;">📱 CHI TIẾT SẢN PHẨM</h4>
               <table style="width: 100%; border-collapse: collapse; border: 1px solid #dee2e6;">
                   <tr style="background: #FF6B35; color: white;">
                       <td style="padding: 8px; font-weight: bold; font-size: 11px; text-align: center;">STT</td>
                       <td style="padding: 8px; font-weight: bold; font-size: 11px;">Tên sản phẩm</td>
                       <td style="padding: 8px; font-weight: bold; font-size: 11px; text-align: center;">SL</td>
                       <td style="padding: 8px; font-weight: bold; font-size: 11px; text-align: right;">Thành tiền</td>
                   </tr>
                   <tr>
                       <td style="padding: 8px; border: 1px solid #dee2e6; text-align: center; font-size: 11px;">1</td>
                       <td style="padding: 8px; border: 1px solid #dee2e6; font-size: 11px;">' . $order['total_products'] . '</td>
                       <td style="padding: 8px; border: 1px solid #dee2e6; text-align: center; font-size: 11px;">1</td>
                       <td style="padding: 8px; border: 1px solid #dee2e6; text-align: right; font-size: 11px; font-weight: bold;">' . number_format($order['total_price'], 0, ',', '.') . ' VNĐ</td>
                   </tr>
               </table>
           </div>

           <!-- Tổng tiền thanh toán ở giữa -->
           <div style="text-align: center; margin-bottom: 15px;">
               <div style="background: linear-gradient(135deg, #FF6B35, #FF8A65); color: white; padding: 20px; border-radius: 10px; display: inline-block; min-width: 300px;">
                   <h3 style="margin: 0; font-size: 16px;">TỔNG TIỀN THANH TOÁN</h3>
                   <p style="margin: 8px 0 0 0; font-size: 28px; font-weight: bold;">' . number_format($order['total_price'], 0, ',', '.') . ' VNĐ</p>
               </div>
           </div>

           <!-- Thông tin thanh toán -->
           <div style="margin-bottom: 15px;">
               <h4 style="color: #FF6B35; margin: 0 0 8px 0; font-size: 13px; text-align: center;">💳 THÔNG TIN THANH TOÁN</h4>
               <div style="text-align: center;">
                   <table style="margin: 0 auto; border-collapse: collapse; font-size: 11px;">
                       <tr><td style="padding: 3px 15px; text-align: right;"><strong>Phương thức thanh toán:</strong></td><td style="padding: 3px 15px;">' . $order['method'] . '</td></tr>
                       <tr><td style="padding: 3px 15px; text-align: right;"><strong>Trạng thái thanh toán:</strong></td><td style="padding: 3px 15px;"><span style="background: #28a745; color: white; padding: 2px 8px; border-radius: 10px; font-size: 10px;">Đã giao hàng</span></td></tr>
                   </table>
               </div>
           </div>

           <!-- Footer -->
           <div style="text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #FF6B35;">
               <p style="color: #FF6B35; font-size: 14px; font-weight: bold; margin: 5px 0;">📱 VN-PHONE - UY TÍN - CHẤT LƯỢNG 📱</p>
               <p style="color: #666; font-size: 11px; margin: 3px 0;">Cảm ơn quý khách đã tin tưởng sản phẩm của chúng tôi!</p>
               <p style="color: #666; font-size: 10px; margin: 3px 0;">Hotline: 1900-xxxx | Email: support@vn-phone.com | Website: www.vn-phone.com</p>
           </div>
       </div>
       ';

      // Tạo PDF
      $mpdf->WriteHTML($html);
      $filename = 'VN-PHONE-HoaDon-' . str_pad($order_id, 6, '0', STR_PAD_LEFT) . '-' . date('Ymd') . '.pdf';
      $pdf_content = $mpdf->Output('', 'S'); // Lấy nội dung PDF dưới dạng string

      // Tạo đối tượng PHPMailer để gửi email
      $mail = new PHPMailer(true);

      try {
         // Cấu hình SMTP
         $mail->isSMTP();
         $mail->Host = 'smtp.gmail.com';
         $mail->SMTPAuth = true;
         $mail->Username = 'huuviet19905@gmail.com';  // Thay bằng email của bạn
         $mail->Password = 'vhabuiyfyxenxqqx';        // Thay bằng app password của bạn
         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
         $mail->Port = 587;

         $mail->CharSet = 'UTF-8';
         $mail->Encoding = 'base64';

         // Người gửi và người nhận
         $mail->setFrom('sazki2k5@gmail.com', 'VN-PHONE');
         $mail->addAddress($order['email'], $order['name']);

         // ===== TIÊU ĐỀ EMAIL VỚI ENCODING ĐÚNG =====
         $mail->isHTML(true);
         $mail->Subject = '🎉 CẢM ƠN QUÝ KHÁCH - ĐƠN HÀNG ĐÃ GIAO THÀNH CÔNG!';

         // Đính kèm file PDF
         $mail->addStringAttachment($pdf_content, $filename, 'base64', 'application/pdf');
         $mail->Body = "
           <!DOCTYPE html>
           <html lang='vi'>
           <head>
               <meta charset='UTF-8'>
               <meta name='viewport' content='width=device-width, initial-scale=1.0'>
               <title>VN-PHONE Thank You</title>
           </head>
           <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;'>
               <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 4px 15px rgba(0,0,0,0.1);'>
                   
                   <!-- Header -->
                   <div style='background: linear-gradient(135deg, #28a745, #20c997); padding: 30px 20px; text-align: center; color: white;'>
                       <h1 style='margin: 0; font-size: 28px; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);'>
                           🎉 GIAO HÀNG THÀNH CÔNG
                       </h1>
                       <p style='margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;'>
                           📱 VN-PHONE - Cảm ơn quý khách!
                       </p>
                   </div>

                   <!-- Main Content -->
                   <div style='padding: 40px 30px;'>
                       <h2 style='color: #28a745; margin-bottom: 20px; font-size: 24px; text-align: center;'>
                           Xin chào <span style='color: #FF6B35;'>" . $order['name'] . "</span>! 👋
                       </h2>
                       
                       <div style='background: #e8f5e8; border-radius: 10px; padding: 25px; margin: 25px 0; text-align: center;'>
                           <h3 style='color: #28a745; margin: 0 0 15px 0; font-size: 20px;'>
                               ✅ ĐƠN HÀNG ĐÃ ĐƯỢC GIAO THÀNH CÔNG!
                           </h3>
                           <p style='color: #155724; margin: 0; font-size: 16px;'>
                               Mã đơn hàng: <strong style='color: #FF6B35;'>VNP-" . str_pad($order_id, 6, '0', STR_PAD_LEFT) . "</strong>
                           </p>
                       </div>

                       <p style='color: #555; font-size: 16px; line-height: 1.8; margin-bottom: 25px; text-align: center;'>
                           Cảm ơn quý khách đã tin tưởng và lựa chọn <strong style='color: #FF6B35;'>VN-PHONE</strong>! 
                           Chúng tôi rất vui khi được phục vụ quý khách và hy vọng sản phẩm sẽ mang lại sự hài lòng tuyệt đối.
                       </p>

                       <!-- Order Summary -->
                       <div style='background: #f8f9fa; border-radius: 10px; padding: 25px; margin: 25px 0;'>
                           <h4 style='color: #2c3e50; margin: 0 0 15px 0; font-size: 18px; text-align: center;'>
                               📋 THÔNG TIN ĐƠN HÀNG
                           </h4>
                           <table style='width: 100%; border-collapse: collapse;'>
                               <tr>
                                   <td style='padding: 8px 0; color: #666; font-size: 14px;'><strong>Sản phẩm:</strong></td>
                                   <td style='padding: 8px 0; color: #333; font-size: 14px;'>" . $order['total_products'] . "</td>
                               </tr>
                               <tr>
                                   <td style='padding: 8px 0; color: #666; font-size: 14px;'><strong>Tổng tiền:</strong></td>
                                   <td style='padding: 8px 0; color: #FF6B35; font-size: 16px; font-weight: bold;'>" . number_format($order['total_price'], 0, ',', '.') . " VNĐ</td>
                               </tr>
                               <tr>
                                   <td style='padding: 8px 0; color: #666; font-size: 14px;'><strong>Ngày đặt:</strong></td>
                                   <td style='padding: 8px 0; color: #333; font-size: 14px;'>" . date('d/m/Y', strtotime($order['placed_on'])) . "</td>
                               </tr>
                               <tr>
                                   <td style='padding: 8px 0; color: #666; font-size: 14px;'><strong>Ngày giao:</strong></td>
                                   <td style='padding: 8px 0; color: #28a745; font-size: 14px; font-weight: bold;'>" . date('d/m/Y') . "</td>
                               </tr>
                           </table>
                       </div>

                       <!-- Attachment Notice -->
                       <div style='background: linear-gradient(135deg, #FF6B35, #FF8E35); border-radius: 10px; padding: 20px; margin: 25px 0; text-align: center; color: white;'>
                           <h4 style='margin: 0 0 10px 0; font-size: 16px;'>
                               📎 HÓA ĐƠN ĐÍNH KÈM
                           </h4>
                           <p style='margin: 0; font-size: 14px; opacity: 0.9;'>
                               Hóa đơn chi tiết đã được đính kèm trong email này để quý khách lưu trữ và bảo hành sản phẩm.
                           </p>
                       </div>

                       <!-- Customer Care -->
                       <div style='border: 2px dashed #FF6B35; border-radius: 10px; padding: 20px; margin: 25px 0;'>
                           <h4 style='color: #FF6B35; margin: 0 0 15px 0; font-size: 16px; text-align: center;'>
                               🌟 DỊCH VỤ CHĂM SÓC KHÁCH HÀNG
                           </h4>
                           <ul style='color: #555; margin: 0; padding-left: 20px; font-size: 14px; line-height: 1.6;'>
                               <li>✅ <strong>Bảo hành:</strong> Sản phẩm được bảo hành chính hãng theo quy định</li>
                               <li>✅ <strong>Hỗ trợ kỹ thuật:</strong> Đội ngũ kỹ thuật sẵn sàng hỗ trợ 24/7</li>
                               <li>✅ <strong>Đổi trả:</strong> Chính sách đổi trả linh hoạt trong 7 ngày</li>
                               <li>✅ <strong>Tư vấn:</strong> Hỗ trợ tư vấn sử dụng và cài đặt miễn phí</li>
                           </ul>
                       </div>

                       <!-- Rating Request -->
                       <div style='background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 10px; padding: 20px; margin: 25px 0; text-align: center;'>
                           <h4 style='color: #856404; margin: 0 0 10px 0; font-size: 16px;'>
                               ⭐ ĐÁNH GIÁ DỊCH VỤ
                           </h4>
                           <p style='color: #856404; margin: 0 0 15px 0; font-size: 14px;'>
                               Hãy chia sẻ trải nghiệm của quý khách để chúng tôi ngày càng hoàn thiện hơn!
                           </p>
                           <div style='margin-top: 15px;'>
                               <span style='font-size: 24px; margin: 0 5px; cursor: pointer;'>⭐</span>
                               <span style='font-size: 24px; margin: 0 5px; cursor: pointer;'>⭐</span>
                               <span style='font-size: 24px; margin: 0 5px; cursor: pointer;'>⭐</span>
                               <span style='font-size: 24px; margin: 0 5px; cursor: pointer;'>⭐</span>
                               <span style='font-size: 24px; margin: 0 5px; cursor: pointer;'>⭐</span>
                           </div>
                       </div>
                      
                   </div>

                   <!-- Footer -->
                   <div style='background: #2c3e50; color: white; padding: 25px 30px; text-align: center;'>
                       <h3 style='margin: 0 0 15px 0; font-size: 18px; color: #FF6B35;'>
                           📱 VN-PHONE
                       </h3>
                       <p style='margin: 5px 0; font-size: 14px;'>
                           <strong>Siêu thị điện thoại uy tín #1 Việt Nam</strong>
                       </p>
                       <p style='margin: 10px 0; font-size: 14px;'>
                           📞 Hotline: <strong>1900-xxxx</strong> | 
                           📧 Email: <strong>support@vnphone.vn</strong>
                       </p>
                       <p style='margin: 15px 0 5px 0; font-size: 12px; opacity: 0.8;'>
                           © 2024 VN-PHONE. All rights reserved.
                       </p>
                       <p style='margin: 0; font-size: 11px; opacity: 0.7;'>
                           Trân trọng cảm ơn quý khách đã tin tưởng VN-PHONE!
                       </p>
                   </div>
               </div>
           </body>
           </html>
           ";

         // Gửi email
         $mail->send();

         $message[] = 'Chúc mừng bạn đã nhận đơn hàng thành công. Email cảm ơn và hóa đơn đã được gửi!';
      } catch (Exception $e) {
         $message[] = 'Đã xác nhận nhận hàng thành công nhưng có lỗi khi gửi email: ' . $mail->ErrorInfo;
      }
   } else {
      $message[] = 'Không tìm thấy đơn hàng!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>orders</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

    <style>
    .btn {
        padding: 10px 15px;
        background-color: #28a745;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: 0.3s ease;
    }

    .btn:hover {
        background-color: #218838;
    }

    .box {
        border: 1px solid #ddd;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .message {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 1000;
        animation: slideIn 0.5s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
        }

        to {
            transform: translateX(0);
        }
    }
    </style>

</head>

<body>

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <!-- Display messages -->
    <?php
   if (isset($message)) {
      foreach ($message as $msg) {
         echo '<div class="message" onclick="this.remove();">' . $msg . '</div>';
      }
   }
   ?>

    <section class="orders">

        <h1 class="title" style="margin-top:100px">đơn hàng của bạn</h1>

        <div class="box-container">

            <?php
         if ($user_id == '') {
            echo '<p class="empty">vui lòng đăng nhập để xem đơn hàng của bạn</p>';
         } else {
            $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE userID = ?");
            $select_orders->execute([$user_id]);
            if ($select_orders->rowCount() > 0) {
               while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
         ?>
            <div class="box">
                <p>ngày đặt hàng : <span><?= $fetch_orders['placed_on']; ?></span></p>
                <p>Tên : <span><?= $fetch_orders['name']; ?></span></p>
                <p>Email : <span><?= $fetch_orders['email']; ?></span></p>
                <p>Số điện thoại : <span><?= $fetch_orders['phoneNumber']; ?></span></p>
                <p>Địa chỉ : <span><?= $fetch_orders['address']; ?></span></p>
                <p>phương thức thanh toán : <span><?= $fetch_orders['method']; ?></span></p>
                <p>Chi tiết đơn hàng: <span><?= $fetch_orders['total_products']; ?></span></p>
                <p>Tổng giá đơn : <span><?= number_format((float)$fetch_orders['total_price']); ?>₫</span></p>

                <p>Tình trạng đơn hàng :
                    <span style="color:<?php
                                             if ($fetch_orders['payment_status'] == 'chờ giao hàng') {
                                                echo 'orange';
                                             } elseif ($fetch_orders['payment_status'] == 'đang giao hàng') {
                                                echo 'blue';
                                             } else {
                                                echo 'green';
                                             }; ?>">
                        <?= $fetch_orders['payment_status']; ?>
                    </span>
                </p>

                <?php if ($fetch_orders['payment_status'] === 'đang giao hàng') { ?>
                <form action="" method="POST">
                    <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                    <button type="submit" name="confirm_received" class="btn">Đã nhận được hàng</button>
                </form>
                <?php } ?>
            </div>
            <?php
               }
            } else {
               echo '<p class="empty">chưa có đơn hàng nào được đặt!</p>';
            }
         }
         ?>

        </div>

    </section>

    <!-- footer section starts  -->
    <?php include 'components/footer.php'; ?>
    <!-- footer section ends -->

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>
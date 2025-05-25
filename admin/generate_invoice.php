<?php
require '../vendor/autoload.php'; // Đường dẫn tới autoload của Composer
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit();
}

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Lấy dữ liệu đơn hàng từ cơ sở dữ liệu
    $select_order = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
    $select_order->execute([$order_id]);

    if ($select_order->rowCount() > 0) {
        $order = $select_order->fetch(PDO::FETCH_ASSOC);

        // Khởi tạo mPDF
        $mpdf = new \Mpdf\Mpdf();

        // Nội dung hóa đơn được thiết kế gọn gàng cho 1 trang A4
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
                        <tr><td style="padding: 3px 15px; text-align: right;"><strong>Trạng thái thanh toán:</strong></td><td style="padding: 3px 15px;"><span style="background: ' . ($order['payment_status'] === 'completed' ? '#28a745' : '#ffc107') . '; color: white; padding: 2px 8px; border-radius: 10px; font-size: 10px;">' . ucfirst($order['payment_status']) . '</span></td></tr>
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

        // Viết nội dung vào PDF
        $mpdf->WriteHTML($html);

        // Xuất file PDF với tên file phù hợp
        $filename = 'VN-PHONE-HoaDon-' . str_pad($order_id, 6, '0', STR_PAD_LEFT) . '-' . date('Ymd') . '.pdf';
        
        // Uncomment dòng dưới để tải file về máy
        // $mpdf->Output($filename, 'D');
        
        // Hiển thị PDF trên trình duyệt
        $mpdf->Output();
    } else {
        echo "<div style='text-align: center; padding: 50px; font-family: Arial;'>
                <h2 style='color: #FF6B35;'>⚠️ Lỗi</h2>
                <p>Không tìm thấy đơn hàng với ID: " . htmlspecialchars($order_id) . "</p>
                <a href='javascript:history.back()' style='color: #FF6B35; text-decoration: none;'>← Quay lại</a>
              </div>";
    }
} else {
    echo "<div style='text-align: center; padding: 50px; font-family: Arial;'>
            <h2 style='color: #FF6B35;'>⚠️ Lỗi</h2>
            <p>Thiếu thông tin ID đơn hàng!</p>
            <a href='javascript:history.back()' style='color: #FF6B35; text-decoration: none;'>← Quay lại</a>
          </div>";
}
?>
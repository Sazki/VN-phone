<?php
require('../include/fpdf/fpdf.php');
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

        // Khởi tạo FPDF
        $pdf = new FPDF();
        $pdf->AddPage();

        // Tiêu đề hóa đơn
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Hoa Don Thanh Toan', 0, 1, 'C');
        $pdf->Ln(10);

        // Thông tin đơn hàng
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, 'ID Khach Hang:', 0, 0);
        $pdf->Cell(50, 10, $order['userID'], 0, 1);

        $pdf->Cell(50, 10, 'Ngay Dat Hang:', 0, 0);
        $pdf->Cell(50, 10, $order['placed_on'], 0, 1);

        $pdf->Cell(50, 10, 'Ten:', 0, 0);
        $pdf->Cell(50, 10, $order['name'], 0, 1);

        $pdf->Cell(50, 10, 'Email:', 0, 0);
        $pdf->Cell(50, 10, $order['email'], 0, 1);

        $pdf->Cell(50, 10, 'So Dien Thoai:', 0, 0);
        $pdf->Cell(50, 10, $order['phoneNumber'], 0, 1);

        $pdf->Cell(50, 10, 'Dia Chi:', 0, 0);
        $pdf->Cell(50, 10, $order['address'], 0, 1);

        $pdf->Cell(50, 10, 'Chi Tiet Don Hang:', 0, 0);
        $pdf->Cell(50, 10, $order['total_products'], 0, 1);

        $pdf->Cell(50, 10, 'Gia Don:', 0, 0);
        $pdf->Cell(50, 10, '$' . $order['total_price'] . '/-', 0, 1);

        $pdf->Cell(50, 10, 'Phuong Thuc:', 0, 0);
        $pdf->Cell(50, 10, $order['method'], 0, 1);

        $pdf->Cell(50, 10, 'Trang Thai Thanh Toan:', 0, 0);
        $pdf->Cell(50, 10, $order['payment_status'], 0, 1);

        // Xuất file PDF
        $pdf->Output();
        //     $pdf->Output('D', 'hoadon_' . $order_id . '.pdf');
    } else {
        echo "Đơn hàng không tồn tại!";
    }
} else {
    echo "Không có ID đơn hàng!";
}
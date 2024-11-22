<?php

require('../include/fpdf/fpdf.php');

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
};

if (isset($_POST['update_payment'])) {

    $order_id = $_POST['order_id'];
    $update_status = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
    $update_status->execute(['đã giao hàng', $order_id]);
    $message[] = 'Đơn hàng đã được giao!';
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
    $delete_order->execute([$delete_id]);
    header('location:placed_orders.php');
}

// //in hóa đơn
// if (isset($_GET['order_id'])) {
//     $order_id = $_GET['order_id'];

//     // Lấy dữ liệu đơn hàng từ cơ sở dữ liệu
//     $select_order = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
//     $select_order->execute([$order_id]);
//     if ($select_order->rowCount() > 0) {
//         $order = $select_order->fetch(PDO::FETCH_ASSOC);

//         // Khởi tạo FPDF
//         $pdf = new FPDF();
//         $pdf->AddPage();

//         // Tiêu đề hóa đơn
//         $pdf->SetFont('Arial', 'B', 16);
//         $pdf->Cell(0, 10, 'Hoa Don Thanh Toan', 0, 1, 'C');
//         $pdf->Ln(10);

//         // Thông tin đơn hàng
//         $pdf->SetFont('Arial', '', 12);
//         $pdf->Cell(50, 10, 'ID Khach Hang:', 0, 0);
//         $pdf->Cell(50, 10, $order['userID'], 0, 1);

//         $pdf->Cell(50, 10, 'Ngay Dat Hang:', 0, 0);
//         $pdf->Cell(50, 10, $order['placed_on'], 0, 1);

//         $pdf->Cell(50, 10, 'Ten:', 0, 0);
//         $pdf->Cell(50, 10, $order['name'], 0, 1);

//         $pdf->Cell(50, 10, 'Email:', 0, 0);
//         $pdf->Cell(50, 10, $order['email'], 0, 1);

//         $pdf->Cell(50, 10, 'So Dien Thoai:', 0, 0);
//         $pdf->Cell(50, 10, $order['phoneNumber'], 0, 1);

//         $pdf->Cell(50, 10, 'Dia Chi:', 0, 0);
//         $pdf->Cell(50, 10, $order['address'], 0, 1);

//         $pdf->Cell(50, 10, 'Chi Tiet Don Hang:', 0, 0);
//         $pdf->Cell(50, 10, $order['total_products'], 0, 1);

//         $pdf->Cell(50, 10, 'Gia Don:', 0, 0);
//         $pdf->Cell(50, 10, '$' . $order['total_price'] . '/-', 0, 1);

//         $pdf->Cell(50, 10, 'Phuong Thuc:', 0, 0);
//         $pdf->Cell(50, 10, $order['method'], 0, 1);

//         $pdf->Cell(50, 10, 'Trang Thai Thanh Toan:', 0, 0);
//         $pdf->Cell(50, 10, $order['payment_status'], 0, 1);

//         // Xuất file PDF
//         $pdf->Output('D', 'hoadon_' . $order_id . '.pdf');
//     } else {
//         echo "Đơn hàng không tồn tại!";
//     }
// }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>placed orders</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

    <style>
    /* CSS cho bảng */
    .orders-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 18px;
        text-align: left;
    }

    .orders-table th,
    .orders-table td {
        border: 1px solid #ddd;
        padding: 8px;
    }

    .orders-table th {
        background-color: #f4f4f4;
        color: #333;
        font-weight: bold;
        text-align: center;
    }

    .orders-table td {
        text-align: center;
    }

    /* Badge trạng thái */
    .badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 14px;
        color: #fff;
        font-weight: bold;
    }

    .badge-success {
        background-color: #28a745;
        /* Xanh lá */
    }

    .badge-warning {
        background-color: #ffc107;
        /* Vàng */
    }

    /* Nút hành động */
    .btn {
        padding: 6px 12px;
        border: none;
        border-radius: 5px;
        color: #fff;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-primary {
        background-color: #007bff;
    }

    .btn-danger {
        background-color: #dc3545;
    }

    .btn-primary:hover,
    .btn-danger:hover {
        opacity: 0.9;
    }

    /* Căn chỉnh hành động */
    .action-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
    }
    </style>

</head>

<body>

    <?php include '../components/admin_header.php' ?>


    <h1 class="heading" style="text-align: center; margin-bottom: 20px;">Đơn đặt hàng</h1>

    <table class="orders-table">
        <thead>
            <tr>
                <th>Ngày đặt hàng</th>
                <th>Tên</th>
                <th>Số điện thoại</th>
                <th>Địa chỉ</th>
                <th>Chi tiết đơn hàng</th>
                <th>Giá đơn</th>
                <th>Phương thức thanh toán</th>
                <th>Trạng thái thanh toán</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders`");
            $select_orders->execute();
            if ($select_orders->rowCount() > 0) {
                while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <tr>
                <td><?= $fetch_orders['placed_on']; ?></td>
                <td><?= $fetch_orders['name']; ?></td>
                <td><?= $fetch_orders['phoneNumber']; ?></td>
                <td><?= $fetch_orders['address']; ?></td>
                <td><?= $fetch_orders['total_products']; ?></td>
                <td><?= $fetch_orders['total_price']; ?>k</td>
                <td><?= $fetch_orders['method']; ?></td>
                <td>
                    <span
                        class="badge <?= $fetch_orders['payment_status'] === 'đã giao hàng' ? 'badge-success' : 'badge-warning'; ?>">
                        <?= $fetch_orders['payment_status']; ?>
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <?php if ($fetch_orders['payment_status'] !== 'đã giao hàng') { ?>
                        <form action="" method="POST">
                            <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                            <button type="submit" class="btn btn-primary" name="update_payment">Giao hàng</button>
                        </form>
                        <?php } ?>
                        <a href="generate_invoice.php?order_id=<?= $fetch_orders['id']; ?>" class="btn btn-primary"
                            target="_blank">In</a>
                        <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>" class="btn btn-danger"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa đơn này không?');">Xóa</a>
                    </div>
                </td>

            </tr>
            <?php
                }
            } else {
                echo '<tr><td colspan="11" style="text-align: center;">Chưa có đơn hàng nào được đặt!</td></tr>';
            }
            ?>
        </tbody>
    </table>








    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>
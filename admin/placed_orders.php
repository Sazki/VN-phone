<?php

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
    /* CSS cho bảng orders */
    .orders-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 16px;
        text-align: left;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .orders-table th,
    .orders-table td {
        padding: 12px 20px;
        border: 1px solid #ddd;
    }

    .orders-table th {
        background-color: #7b7b7b;
        ;
        color: white;
        font-weight: bold;
        text-align: center;
    }

    .orders-table td {
        text-align: center;
    }

    /* Hiệu ứng hover cho hàng trong bảng */
    .orders-table tr:hover {
        background-color: #f1f1f1;
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

    .badge-danger {
        background-color: #dc3545;
        /* Đỏ */
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
        transition: background-color 0.3s;
    }

    .btn-primary {
        background-color: #007bff;
    }

    .btn-danger {
        background-color: #dc3545;
    }

    /* Hiệu ứng hover cho nút */
    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    /* Căn chỉnh hành động */
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    /* Hiệu ứng khi di chuột qua các ô */
    .orders-table td:hover {
        background-color: #f8f8f8;
    }

    /* Các hàng có màu nền nhẹ */
    .orders-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .orders-table td {
        background-color: #fff;
        text-align: center;
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
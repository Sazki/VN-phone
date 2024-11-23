<?php

include 'components/connect.php';

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
   $update_status = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_status->execute(['đã giao hàng', $order_id]);
   $message[] = 'Trạng thái đơn hàng đã được cập nhật thành "Đã giao hàng".';
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
    </style>

</head>

<body>

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <div class="heading">
        <h3>đơn đặt hàng</h3>
        <p><a href="html.php">trang chủ</a> <span> / đơn đặt hàng</span></p>
    </div>

    <section class="orders">

        <h1 class="title">đơn hàng của bạn</h1>

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
                <p>Tổng giá đơn : <span><?= $fetch_orders['total_price']; ?>k</span></p>
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
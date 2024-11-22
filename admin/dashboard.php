<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <?php include '../components/admin_header.php' ?>

    <!-- admin dashboard section starts  -->

    <section class="dashboard">

        <h1 class="heading">Bảng điều khiển</h1>

        <div class="box-container">

            <div class="box">
                <h3>Chào mừng!</h3>
                <p><?= $fetch_profile['name']; ?></p>
                <a href="update_profile.php" class="btn">cập nhật hồ sơ</a>
            </div>

            <div class="box">
                <?php
                $total_pendings = 0;
                $select_pendings = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
                $select_pendings->execute(['chờ giao hàng']);
                $numbers_of_pendings = $select_pendings->rowCount();
                ?>
                <h3><?= $numbers_of_pendings; ?></h3>
                <p>tổng số đơn hàng đang chờ xử lý</p>
                <a href="placed_orders.php" class="btn">xem đơn hàng</a>
            </div>

            <div class="box">
                <?php
                $total_completes = 0;
                $select_completes = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
                $select_completes->execute(['Đã giao hàng']);
                $numbers_of_completes = $select_completes->rowCount();
                ?>
                <h3><?= $numbers_of_completes; ?></h3>
                <p>tổng số đơn hàng đã giao</p>
                <a href="placed_orders.php" class="btn">Xem đơn hàng</a>
            </div>

            <div class="box">
                <?php
                $select_orders = $conn->prepare("SELECT * FROM `orders`");
                $select_orders->execute();
                $numbers_of_orders = $select_orders->rowCount();
                ?>
                <h3><?= $numbers_of_orders; ?></h3>
                <p>tổng số đơn hàng</p>
                <a href="placed_orders.php" class="btn">xem đơn hàng</a>
            </div>

            <div class="box">
                <?php
                $select_products = $conn->prepare("SELECT * FROM `products`");
                $select_products->execute();
                $numbers_of_products = $select_products->rowCount();
                ?>
                <h3><?= $numbers_of_products; ?></h3>
                <p>sản phẩm đã thêm</p>
                <a href="products.php" class="btn">xem sản phẩm</a>
            </div>

            <div class="box">
                <?php
                $select_users = $conn->prepare("SELECT * FROM `users` WHERE role = ?");
                $select_users->execute(['client']);
                $numbers_of_users = $select_users->rowCount();
                ?>
                <h3><?= $numbers_of_users; ?></h3>
                <p>tài khoản người dùng</p>
                <a href="users_accounts.php" class="btn">xem người dùng</a>
            </div>

            <div class="box">
                <?php
                $select_admins = $conn->prepare("SELECT * FROM `users` WHERE role = ?");
                $select_admins->execute(['admin']);
                $numbers_of_admins = $select_admins->rowCount();
                ?>
                <h3><?= $numbers_of_admins; ?></h3>
                <p>quản trị viên</p>
                <a href="admin_accounts.php" class="btn">xem quản trị viên</a>
            </div>

            <div class="box">
                <?php
                $select_messages = $conn->prepare("SELECT * FROM `messages`");
                $select_messages->execute();
                $numbers_of_messages = $select_messages->rowCount();
                ?>
                <h3><?= $numbers_of_messages; ?></h3>
                <p>tin nhắn mới</p>
                <a href="messages.php" class="btn">xem tin nhắn</a>
            </div>

        </div>

    </section>

    <!-- admin dashboard section ends -->









    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>
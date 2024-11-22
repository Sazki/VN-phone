<?php
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
?>

<header class="header">

    <section class="flex">

        <a href="dashboard.php" class="logo">VN<span>Food</span></a>

        <nav class="navbar">
            <a href="dashboard.php">Trang chủ</a>
            <a href="products.php">Sản phẩm</a>
            <a href="placed_orders.php">Đơn đặt hàng</a>
            <a href="admin_accounts.php">Quản trị viên</a>
            <a href="users_accounts.php">Người dùng</a>
            <a href="messages.php">Tin nhắn</a>
        </nav>

        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="user-btn" class="fas fa-user"></div>
        </div>

        <div class="profile">
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE userID = ? AND role = ?");
            $select_profile->execute([$admin_id, 'admin']);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
            <p><?= $fetch_profile['name']; ?></p>
            <a href="update_profile.php" class="btn">cập nhật hồ sơ</a>
            <div class="flex-btn">
                <a href="admin_login.php" class="option-btn">đăng nhập</a>
                <a href="register_admin.php" class="option-btn">đăng ký</a>
            </div>
            <a href="../components/admin_logout.php" onclick="return confirm('đăng xuất khỏi trang web này?');"
                class="delete-btn">đăng xuất</a>
        </div>

    </section>

</header>
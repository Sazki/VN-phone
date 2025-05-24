<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
      <div class="message">
         <span>' . $msg . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
    }
}
?>

<head>
    <!-- Google Fonts Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .header {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(90deg, #232526, #434343 90%);
            box-shadow: 0 3px 12px rgba(30, 30, 60, 0.11);
            padding: 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            transition: all 0.4s;
        }
        .header .container-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 68px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 30px;
        }
        .logo {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .logo i {
            color: #ff9800;
            margin-right: 9px;
            font-size: 2.1rem;
        }
        .navbar {
            display: flex;
            align-items: center;
            gap: 26px;
        }
        .navbar a {
            font-size: 2rem;
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            padding: 7px 0;
            position: relative;
            transition: color 0.22s;
        }
        .navbar a:hover,
        .navbar a.active {
            color: #ff9800;
        }
        .navbar a.active::after,
        .navbar a:hover::after {
            width: 100%;
        }
        .navbar a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -3px;
            width: 0;
            height: 2px;
            background: #ff9800;
            transition: width 0.22s;
        }
        .icons {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .icons a, .icons #user-btn {
            color: #fff;
            font-size: 1.8rem;
            margin: 0 2px;
            cursor: pointer;
            position: relative;
            transition: color 0.22s, transform 0.22s;
        }
        .icons a:hover,
        .icons #user-btn:hover {
            color: #ff9800;
            transform: translateY(-2px) scale(1.13);
        }
        .cart-count {
            background: #ff9800;
            color: #fff;
            font-size: 0.82rem;
            border-radius: 8px;
            padding: 0 7px;
            position: absolute;
            top: -7px;
            right: -9px;
            font-weight: bold;
        }
        .avatar-mini {
            width: 32px; height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px #2221;
            margin-left: 5px;
        }
        .user-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 60px;
            background: #fff;
            color: #333;
            min-width: 185px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.14);
            border-radius: 10px;
            z-index: 9999;
            padding: 12px 0;
            animation: fadeIn 0.24s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px);}
            to   { opacity: 1; transform: none;}
        }
        .user-dropdown a{
            font-size: 1.5rem;
        }
        .user-dropdown a, .user-dropdown .btn, .user-dropdown .delete-btn {
            display: block;
            color: #232526;
            padding: 8px 20px;
            text-decoration: none;
            font-weight: 500;
            border: none;
            background: none;
            transition: background 0.17s;
            border-radius: 5px;
            cursor: pointer;
            text-align: left;
        }
        .user-dropdown .delete-btn {
            color: #d32f2f;
        }
        .user-dropdown a:hover,
        .user-dropdown .btn:hover,
        .user-dropdown .delete-btn:hover {
            background: #ffe1bc;
        }
        .user-info-summary {
            font-size: 1.5rem;
            font-weight: 500;
            margin-bottom: 8px;
            padding: 0 20px 4px 20px;
            color: #2d3a4e;
            border-bottom: 1px solid #eee;
        }
        /* Responsive */
        @media (max-width: 800px) {
            .header .container-nav { padding: 0 12px;}
            .navbar { gap: 10px;}
        }
        @media (max-width: 650px) {
            .navbar { display: none;}
            .header .container-nav { padding: 0 4px;}
        }
        /* dfsdsaddfsdsad */
        #menu-btn {
    display: none;
    font-size: 2.3rem;
    color: #fff;
    cursor: pointer;
    margin-left: 10px;
}

@media (max-width: 650px) {
    #menu-btn { display: block; }
    .navbar {
        display: none;
        position: absolute;
        top: 65px; /* hoặc 68px nếu header cao hơn */
        left: 0;
        width: 100vw;
        background: #232526;
        flex-direction: column;
        align-items: flex-start;
        z-index: 999;
        box-shadow: 0 8px 32px rgba(0,0,0,0.10);
    }
    .navbar.show {
        display: flex;
    }
    .navbar a {
        width: 100%;
        padding: 15px 20px;
        color: #fff;
        border-bottom: 1px solid #4443;
    }
}

    </style>
</head>
<header class="header">
    <div class="container-nav">
        <a href="home.php" class="logo">
            <i class="fas fa-mobile-alt"></i>VN-Phone
        </a>
        <nav class="navbar">
            <a href="home.php">Trang chủ</a>
            <a href="about.php">Giới thiệu</a>
            <a href="menu.php">Sản phẩm</a>
            <a href="orders.php">Đơn hàng</a>
            <a href="contact.php">Liên hệ</a>
        </nav>
        <div class="icons">
            <a href="search.php" title="Tìm kiếm"><i class="fas fa-search"></i></a>
            <?php
            $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE userID = ?");
            $count_cart_items->execute([$user_id]);
            $total_cart_items = $count_cart_items->rowCount();
            ?>
            <a href="cart.php" title="Giỏ hàng">
                <i class="fas fa-shopping-cart"></i>
                <?php if($total_cart_items>0): ?>
                    <span class="cart-count"><?= $total_cart_items; ?></span>
                <?php endif;?>
            </a>
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE userID = ?");
            $select_profile->execute([$user_id]);
            $avatar = "uploaded_img/user-icon.png";
            $user_name = "Khách";
            if ($select_profile->rowCount() > 0) {
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                $avatar = "uploaded_img/{$fetch_profile['avatar']}";
                $user_name = $fetch_profile['name'];
            }
            ?>
            <div id="user-btn" tabindex="0" style="position:relative;">
                <img src="<?= $avatar; ?>" alt="Avatar" class="avatar-mini">
            </div>
            <div id="menu-btn" class="fas fa-bars"></div>

        </div>
        <!-- User Dropdown -->
        <div class="user-dropdown" id="userDropdown">
            <?php if(isset($fetch_profile) && $fetch_profile): ?>
                <div class="user-info-summary">👤 <?= htmlspecialchars($user_name) ?></div>
                <a href="profile.php">Trang cá nhân</a>
                <a href="orders.php">Đơn hàng của tôi</a>
                <a href="components/user_logout.php" class="delete-btn" onclick="return confirm('Đăng xuất khỏi trang web?');">Đăng xuất</a>
            <?php else: ?>
                <a href="login.php" class="btn">Đăng nhập</a>
                <a href="register.php" class="btn">Đăng ký</a>
            <?php endif; ?>
        </div>
        

    </div>
    <script>
        // Dropdown user menu
        const userBtn = document.getElementById('user-btn');
        const userDropdown = document.getElementById('userDropdown');
        document.addEventListener('click', function(e){
            if(userDropdown && (e.target===userBtn || userBtn.contains(e.target))){
                userDropdown.style.display = (userDropdown.style.display === "block") ? "none" : "block";
            } else if(userDropdown && !userDropdown.contains(e.target)){
                userDropdown.style.display = "none";
            }
        });
    </script>
    <script>
const menuBtn = document.getElementById('menu-btn');
const navbar = document.querySelector('.navbar');

menuBtn.onclick = (e) => {
    e.stopPropagation(); // không đóng khi bấm menu-btn
    navbar.classList.toggle('show');
};
// Ẩn navbar khi bấm ra ngoài (chỉ ở mobile)
document.addEventListener('click', function(e){
    if(window.innerWidth <= 650 && navbar.classList.contains('show') && !navbar.contains(e.target) && e.target !== menuBtn){
        navbar.classList.remove('show');
    }
});
</script>

</header>

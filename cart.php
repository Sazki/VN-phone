<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
    header('location:home.php');
    exit();
}

if (isset($_POST['delete'])) {
    $cart_id = $_POST['cart_id'];
    $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE cartID = ?");
    $delete_cart_item->execute([$cart_id]);
    $message[] = 'Đã xóa sản phẩm khỏi giỏ hàng!';
}

if (isset($_POST['delete_all'])) {
    $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE userID = ?");
    $delete_cart_item->execute([$user_id]);
    $message[] = 'Đã xóa tất cả sản phẩm khỏi giỏ hàng!';
}

if (isset($_POST['update_qty'])) {
    $cart_id = $_POST['cart_id'];
    $qty = max(1, min(99, intval($_POST['qty']))); // Giới hạn từ 1-99
    $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE cartID = ?");
    $update_qty->execute([$qty, $cart_id]);
    $message[] = 'Đã cập nhật số lượng!';
}

$grand_total = 0;
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background: #f3f4f8; }
        .products .title {
            margin-top: 110px;
            font-size: 2.2rem;
            letter-spacing: 1px;
            text-align: center;
            color: #2b3445;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }
        .products .box-container {
            display: flex;
            flex-wrap: wrap;
            gap: 28px;
            justify-content: center; /* Chính giữa! */
            margin-top: 22px;
            margin-bottom: 22px;
        }
        .products .box {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 18px #b5b6c440;
            width: 370px;
            padding: 26px 22px 18px 22px;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: box-shadow 0.23s, transform 0.17s;
        }
        .products .box:hover {
            box-shadow: 0 9px 32px #7273d13c;
            transform: translateY(-2px) scale(1.025);
        }
        .products .box img {
            width: 126px;
            height: 126px;
            object-fit: contain;
            border-radius: 8px;
            margin-bottom: 16px;
            background: #f5f5f7;
        }
        .products .box .name {
            font-size: 1.18rem;
            font-weight: 600;
            margin-bottom: 7px;
            color: #232526;
            text-align: center;
        }
        .products .box .name span {
            color: #888;
            font-size: 0.98rem;
        }
        .products .box .flex {
            display: flex;
            align-items: center;
            gap: 11px;
            margin-bottom: 8px;
            width: 100%;
            justify-content: center;
        }
        .products .box .price {
            color: #ff9800;
            font-size: 1.17rem;
            font-weight: 700;
            min-width: 94px;
            text-align: left;
        }
        .products .box .qty {
            width: 54px;
            padding: 8px 9px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
            margin-left: 4px;
        }
        .products .box .fas.fa-edit,
        .products .box .fas.fa-times,
        .products .box .fas.fa-eye {
            color: #fff;
            font-size: 1.09rem;
            border-radius: 5px;
            padding: 7px 11px;
            margin-left: 4px;
            margin-right: 4px;
            cursor: pointer;
            border: none;
            transition: background 0.18s, color 0.18s, box-shadow 0.18s;
        }
        .products .box .fas.fa-edit {
            background: linear-gradient(90deg,#36d1c4,#5b86e5);
            box-shadow: 0 2px 8px #5b86e55c;
        }
        .products .box .fas.fa-edit:hover {
            background: linear-gradient(90deg,#11998e,#38ef7d);
            color: #fff;
        }
        .products .box .fas.fa-times {
            background: linear-gradient(90deg,#f857a6,#ff5858);
            box-shadow: 0 2px 8px #fa5b7a55;
        }
        .products .box .fas.fa-times:hover {
            background: linear-gradient(90deg,#ff5858,#f09819);
            color: #fff;
            transform: scale(1.15) rotate(-14deg);
        }
        .products .box .fas.fa-eye {
            background: linear-gradient(90deg,#ffb347,#ffcc33);
            color: #333;
            box-shadow: 0 2px 8px #f2d16a65;
        }
        .products .box .fas.fa-eye:hover {
            background: linear-gradient(90deg,#36d1c4,#5b86e5);
            color: #fff;
        }
        .products .box .sub-total {
            font-size: 1.08rem;
            font-weight: 500;
            margin-top: 8px;
            color: #333;
            letter-spacing: 0.5px;
        }
        .cart-total, .more-btn {
            text-align: center;
            margin: 28px 0 13px 0;
        }
        .cart-total p {
            font-size: 1.23rem;
            font-weight: 600;
            color: #1a202c;
        }
        .cart-total .btn {
            margin-top: 13px;
            display: inline-block;
        }
        .btn, .delete-btn {
            font-size: 1.08rem;
            font-family: 'Poppins',sans-serif;
            padding: 11px 23px;
            border-radius: 7px;
            margin: 0 7px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            background: linear-gradient(90deg,#ff9800,#ffcc33);
            color: #222;
            box-shadow: 0 2px 12px #fae69c55;
            transition: background 0.22s, color 0.17s, transform 0.22s;
        }
        .btn:hover, .delete-btn:hover {
            background: linear-gradient(90deg,#36d1c4,#5b86e5);
            color: #fff;
            transform: scale(1.04);
        }
        .delete-btn {
            background: linear-gradient(90deg,#f857a6,#ff5858);
            color: #fff;
            box-shadow: 0 2px 12px #fa5b7a55;
        }
        .delete-btn:hover {
            background: linear-gradient(90deg,#ff5858,#f09819);
            color: #fff;
        }
        .disabled, .disabled:hover { 
            opacity: 0.48; cursor: not-allowed; pointer-events: none; 
            background: #eee !important; color: #aaa !important;
        }
        @media (max-width: 900px) {
            .products .box { width: 98%; }
        }
    </style>
</head>

<body>
    <?php include 'components/user_header.php'; ?>

    <section class="products">
        <h1 class="title">Giỏ hàng của bạn</h1>
        <div class="box-container">
            <?php
            $grand_total = 0;
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE userID = ?");
            $select_cart->execute([$user_id]);
            if ($select_cart->rowCount() > 0) {
                while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                    $color = isset($fetch_cart['color']) ? $fetch_cart['color'] : '';
            ?>
            <form action="" method="post" class="box">
                <input type="hidden" name="cart_id" value="<?= $fetch_cart['cartID']; ?>">
                <a href="quick_view.php?pid=<?= $fetch_cart['productID']; ?>" class="fas fa-eye" title="Xem chi tiết"></a>
                <button type="submit" class="fas fa-times" name="delete" title="Xóa khỏi giỏ"
                    onclick="return confirm('Xóa sản phẩm này khỏi giỏ?');"></button>
                <img src="uploaded_img/<?= htmlspecialchars($fetch_cart['image']); ?>" alt="Ảnh sản phẩm">
                <div class="name"><?= htmlspecialchars($fetch_cart['cartName']); ?>
                    <?php if ($color): ?>
                        <span style="color:#666; font-size:0.97rem;">(Màu: <?= htmlspecialchars($color); ?>)</span>
                    <?php endif; ?>
                </div>
                <div class="flex">
                    <div class="price"><?= number_format((float)$fetch_cart['price']); ?>₫</div>
                    <input type="number" name="qty" class="qty" min="1" max="99" value="<?= $fetch_cart['quantity']; ?>" maxlength="2">
                    <button type="submit" class="fas fa-edit" name="update_qty" title="Cập nhật số lượng"></button>
                </div>
                <div class="sub-total">
                    Thành tiền: <span><?= number_format($sub_total = $fetch_cart['price'] * $fetch_cart['quantity']); ?>₫</span>
                </div>
            </form>
            <?php
                    $grand_total += $sub_total;
                }
            } else {
                echo '<p class="empty">Giỏ hàng của bạn đang trống</p>';
            }
            ?>
        </div>

        <div class="cart-total">
            <p>Tổng thanh toán: <span><?= number_format($grand_total); ?>₫</span></p>
            <a href="checkout.php" class="btn <?= ($grand_total > 0) ? '' : 'disabled'; ?>">Tiến hành thanh toán</a>
        </div>
        <div class="more-btn">
            <form action="" method="post" style="display:inline;">
                <button type="submit" class="delete-btn <?= ($grand_total > 0) ? '' : 'disabled'; ?>" name="delete_all"
                    onclick="return confirm('Xóa tất cả sản phẩm khỏi giỏ?');">Xóa tất cả</button>
            </form>
            <a href="menu.php" class="btn">Tiếp tục mua sắm</a>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>

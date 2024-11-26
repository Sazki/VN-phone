<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
    header('location:home.php');
};

if (isset($_POST['delete'])) {
    $cart_id = $_POST['cart_id'];
    $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE cartID = ?");
    $delete_cart_item->execute([$cart_id]);
    $message[] = 'đã xóa sản phẩm trong giỏ hàng!';
}

if (isset($_POST['delete_all'])) {
    $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE userID = ?");
    $delete_cart_item->execute([$user_id]);
    // header('location:cart.php');
    $message[] = 'đã xóa tất cả sản phẩm khỏi giỏ hàng!';
}

if (isset($_POST['update_qty'])) {
    $cart_id = $_POST['cart_id'];
    $qty = htmlspecialchars($_POST['qty'], ENT_QUOTES, 'UTF-8');
    $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE cartID = ?");
    $update_qty->execute([$qty, $cart_id]);
    $message[] = 'cart quantity updated';
}

$grand_total = 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cart</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <!-- <div class="heading">
        <h3>giỏ hàng</h3>
        <p><a href="home.php">trang chủ</a> <span> / giỏ hàng</span></p>
    </div> -->

    <!-- shopping cart section starts  -->

    <section class="products">

        <h1 class="title" style="margin-top:100px">giỏ hàng của bạn</h1>

        <div class="box-container">

            <?php
            $grand_total = 0;
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE userID = ?");
            $select_cart->execute([$user_id]);
            if ($select_cart->rowCount() > 0) {
                while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <form action="" method="post" class="box">
                <input type="hidden" name="cart_id" value="<?= $fetch_cart['cartID']; ?>">
                <a href="quick_view.php?pid=<?= $fetch_cart['productID']; ?>" class="fas fa-eye"></a>
                <button type="submit" class="fas fa-times" name="delete"
                    onclick="return confirm('delete this item?');"></button>
                <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
                <div class="name"><?= $fetch_cart['cartName']; ?></div>
                <div class="flex">
                    <div class="price"><?= $fetch_cart['price']; ?>k</div>
                    <input type="number" name="qty" class="qty" min="1" max="99" value="<?= $fetch_cart['quantity']; ?>"
                        maxlength="2">
                    <button type="submit" class="fas fa-edit" name="update_qty"></button>
                </div>
                <div class="sub-total">tổng cộng:
                    <span><?= $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?>k</span>
                </div>
            </form>
            <?php
                    $grand_total += $sub_total;
                }
            } else {
                echo '<p class="empty">giỏ hàng của bạn đang trống</p>';
            }
            ?>

        </div>

        <div class="cart-total">
            <p>Tổng số tiền giỏ hàng: <span><?= $grand_total; ?>k</span></p>
            <a href="checkout.php" class="btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>">tiến hành thanh toán</a>
        </div>

        <div class="more-btn">
            <form action="" method="post">
                <button type="submit" class="delete-btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>" name="delete_all"
                    onclick="return confirm('delete all from cart?');">xóa tất cả</button>
            </form>
            <a href="menu.php" class="btn">tiếp tục chọn món</a>
        </div>

    </section>

    <!-- shopping cart section ends -->










    <!-- footer section starts  -->
    <?php include 'components/footer.php'; ?>
    <!-- footer section ends -->








    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>
<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:home.php');
};

if (isset($_POST['submit'])) {

   $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
   $number = htmlspecialchars($_POST['number'], ENT_QUOTES, 'UTF-8');
   $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
   $method = htmlspecialchars($_POST['method'], ENT_QUOTES, 'UTF-8');
   $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
   $total_products = htmlspecialchars($_POST['total_products'], ENT_QUOTES, 'UTF-8');
   $total_price = htmlspecialchars($_POST['total_price'], ENT_QUOTES, 'UTF-8');

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE userID = ?");
   $check_cart->execute([$user_id]);

   if ($check_cart->rowCount() > 0) {

      if ($address == '') {
         $message[] = 'vui lòng thêm địa chỉ của bạn!';
      } else {

         $insert_order = $conn->prepare("INSERT INTO `orders`(userID, name, phoneNumber, email, method, address, total_products, total_price, payment_status, placed_on) VALUES(?,?,?,?,?,?,?,?,?,NOW())");
         $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price, 'chờ giao hàng']);

         $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE userID = ?");
         $delete_cart->execute([$user_id]);

         $message[] = 'đơn hàng đã được đặt thành công!';
      }
   } else {
      $message[] = 'giỏ hàng của bạn đang trống';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <div class="heading">
        <h3>Thanh toán</h3>
        <p><a href="home.php">Trang chủ</a> <span> / Thanh toán</span></p>
    </div>

    <section class="checkout">

        <h1 class="title">tóm tắt đơn hàng</h1>

        <form action="" method="post">

            <div class="cart-items">
                <h3>Các mặt hàng trong giỏ hàng</h3>
                <?php
            $grand_total = 0;
            $cart_items[] = '';
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE userID = ?");
            $select_cart->execute([$user_id]);
            if ($select_cart->rowCount() > 0) {
               while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                  $cart_items[] = $fetch_cart['cartName'] . ' (' . $fetch_cart['price'] . ' x ' . $fetch_cart['quantity'] . ') - ';
                  $total_products = implode($cart_items);
                  $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
            ?>
                <p><span class="name"><?= $fetch_cart['cartName']; ?></span><span
                        class="price"><?= $fetch_cart['price']; ?>k x <?= $fetch_cart['quantity']; ?></span></p>
                <?php
               }
            } else {
               echo '<p class="empty">giỏ hàng của bạn đang trống!</p>';
            }
            ?>
                <p class="grand-total"><span class="name">tổng giá đơn:</span><span
                        class="price"><?= $grand_total; ?>k</span></p>
                <a href="cart.php" class="btn">xem giỏ hàng</a>
            </div>

            <input type="hidden" name="total_products" value="<?= $total_products; ?>">
            <input type="hidden" name="total_price" value="<?= $grand_total; ?>">
            <input type="hidden" name="name" value="<?= $fetch_profile['name'] ?>">
            <input type="hidden" name="number" value="<?= $fetch_profile['phoneNumber'] ?>">
            <input type="hidden" name="email" value="<?= $fetch_profile['email'] ?>">
            <input type="hidden" name="address" value="<?= $fetch_profile['address'] ?>">

            <div class="user-info">
                <h3>thông tin của bạn</h3>
                <p><i class="fas fa-user"></i><span><?= $fetch_profile['name'] ?></span></p>
                <p><i class="fas fa-phone"></i><span><?= $fetch_profile['phoneNumber'] ?></span></p>
                <p><i class="fas fa-envelope"></i><span><?= $fetch_profile['email'] ?></span></p>
                <a href="update_profile.php" class="btn">cập nhật thông tin</a>
                <h3>địa chỉ giao hàng</h3>
                <p><i class="fas fa-map-marker-alt"></i><span><?php if ($fetch_profile['address'] == '') {
                                                               echo 'vui lòng nhập địa chỉ của bạn';
                                                            } else {
                                                               echo $fetch_profile['address'];
                                                            } ?></span>
                </p>
                <a href="update_address.php" class="btn">cập nhật địa chỉ</a>
                <select name="method" class="box" required>
                    <option value="" disabled selected>chọn phương thức thanh toán</option>
                    <option value="thanh toán khi nhận hàng">thanh toán khi nhận hàng</option>
                    <option value="thẻ tín dụng">thẻ tín dụng</option>
                </select>
                <input type="submit" value="đặt hàng" class="btn <?php if ($fetch_profile['address'] == '') {
                                                                     echo 'disabled';
                                                                  } ?>"
                    style="width:100%; background:var(--red); color:var(--white);" name="submit">
            </div>

        </form>

    </section>









    <!-- footer section starts  -->
    <?php include 'components/footer.php'; ?>
    <!-- footer section ends -->






    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>
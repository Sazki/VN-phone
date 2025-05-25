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

// Lấy thông tin user cho phần dưới
$select_profile = $conn->prepare("SELECT * FROM `users` WHERE userID = ?");
$select_profile->execute([$user_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {

    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $number = htmlspecialchars($_POST['number'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $method = htmlspecialchars($_POST['method'], ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
    $total_products = htmlspecialchars($_POST['total_products'], ENT_QUOTES, 'UTF-8');
    $discount_code = htmlspecialchars($_POST['discount_code'], ENT_QUOTES, 'UTF-8');

    // Ép kiểu số nguyên cho tổng giá
    $total_price = isset($_POST['total_price']) ? preg_replace('/\D/', '', $_POST['total_price']) : 0;
    $total_price_coupon = isset($_POST['total_price_coupon']) ? preg_replace('/\D/', '', $_POST['total_price_coupon']) : 0;

    $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE userID = ?");
    $check_cart->execute([$user_id]);

    if ($check_cart->rowCount() > 0) {

        if ($address == '') {
            $message[] = 'Vui lòng thêm địa chỉ của bạn!';
        } else {
            // Nếu có mã giảm giá và đã nhập giá sau giảm giá
            if (!empty($discount_code) && $total_price_coupon > 0) {
                $total_price = $total_price_coupon;
            }

            // Xử lý theo phương thức thanh toán
            if ($method === 'thẻ tín dụng') {
                // Chuyển hướng đến trang xử lý VNPay
                ?>
<form id="vnpay-form" action="vnpay_payment.php" method="post" style="display: none;">
    <input type="hidden" name="name" value="<?= $name ?>">
    <input type="hidden" name="number" value="<?= $number ?>">
    <input type="hidden" name="email" value="<?= $email ?>">
    <input type="hidden" name="address" value="<?= $address ?>">
    <input type="hidden" name="total_products" value="<?= $total_products ?>">
    <input type="hidden" name="total_price" value="<?= $total_price ?>">
    <input type="hidden" name="total_price_coupon" value="<?= $total_price_coupon ?>">
    <input type="hidden" name="discount_code" value="<?= $discount_code ?>">
</form>
<script>
document.getElementById('vnpay-form').submit();
</script>
<?php
                exit();
            } else {
                // Thanh toán khi nhận hàng - xử lý như cũ
                $insert_order = $conn->prepare("INSERT INTO `orders`(userID, name, phoneNumber, email, method, address, total_products, total_price, payment_status, placed_on) VALUES(?,?,?,?,?,?,?,?,?,NOW())");

                $insert_order->execute([
                    $user_id, $name, $number, $email, $method, $address, $total_products, $total_price, 'chờ giao hàng'
                ]);

                $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE userID = ?");
                $delete_cart->execute([$user_id]);

                $message[] = 'Đơn hàng đã được đặt thành công!';
            }
        }
    } else {
        $message[] = 'Giỏ hàng của bạn đang trống';
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

    <style>
    .discount {
        margin: 20px 0;
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .discount h3 {
        font-size: 18px;
        color: #333;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .discount input[type="text"] {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 5px;
        outline: none;
        transition: all 0.3s ease;
    }

    .discount input[type="text"]:focus {
        border-color: #5c6bc0;
        box-shadow: 0 0 10px rgba(92, 107, 192, 0.3);
    }

    .discount input[type="text"]::placeholder {
        color: #888;
        font-style: italic;
    }

    .discount .btn {
        width: 100%;
        padding: 12px;
        background-color: #5c6bc0;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .discount .btn:hover {
        background-color: #3f51b5;
    }

    .total-price {
        display: none;
        /* Ẩn mặc định */
        margin: 20px 0;
        padding: 15px;
        background-color: #e8f5e9;
        border: 2px solid #4caf50;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        font-size: 18px;
        font-weight: bold;
        color: #4caf50;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .total-price span {
        font-size: 24px;
        font-weight: bold;
        color: #388e3c;
    }

    .total-price.show {
        display: block;
        /* Hiện khung khi áp dụng mã giảm giá */
    }

    .payment-method {
        margin: 20px 0;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #f9f9f9;
    }

    .payment-method h3 {
        margin-bottom: 15px;
        color: #333;
        font-size: 18px;
    }

    .payment-method select {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        background: white;
    }

    .payment-method select:focus {
        border-color: #5c6bc0;
        outline: none;
        box-shadow: 0 0 5px rgba(92, 107, 192, 0.3);
    }

    .vnpay-info {
        margin-top: 10px;
        padding: 12px;
        background: #e3f2fd;
        border-left: 4px solid #2196f3;
        border-radius: 4px;
        display: none;
    }

    .vnpay-info.show {
        display: block;
    }

    .vnpay-info p {
        margin: 5px 0;
        font-size: 14px;
        color: #1976d2;
    }

    .vnpay-info i {
        margin-right: 8px;
        color: #2196f3;
    }
    </style>
</head>
<?php
$message = $message ?? [];
if (isset($_GET['address_updated']) && $_GET['address_updated'] == 1) {
    $message[] = "Địa chỉ đã được cập nhật thành công!";
}
?>
<?php if (!empty($message)) foreach ($message as $msg): ?>
<div class="message"
    style="margin:12px 0;background:#f0fff0;border-left:5px solid #3adb76;padding:10px 18px;border-radius:8px;color:#237e28;">
    <?= htmlspecialchars($msg) ?>
</div>
<?php endforeach; ?>
<?php
if (isset($_GET['profile_updated'])) {
    echo '<div class="message" style="margin:12px 0;background:#f0fff0;border-left:5px solid #3adb76;padding:10px 18px;border-radius:8px;color:#237e28;">Hồ sơ đã được cập nhật thành công!</div>';
}
?>

<body>
    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <section class="checkout">
        <h1 class="title" style="margin-top: 100px;">Tóm tắt đơn hàng</h1>

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
                <p><span class="name"><?= $fetch_cart['cartName']; ?></span>
                    <span class="price"><?= number_format($fetch_cart['price']); ?>₫ x
                        <?= $fetch_cart['quantity']; ?></span>
                </p>
                <?php
                    }
                } else {
                    echo '<p class="empty">Giỏ hàng của bạn đang trống!</p>';
                }
                ?>
                <p class="grand-total"><span class="name">Tổng giá đơn:</span>
                    <span class="price"><?= number_format($grand_total); ?>₫</span>
                </p>
                <a href="cart.php" class="btn">Xem giỏ hàng</a>
            </div>

            <div class="discount">
                <h3>Nhập mã giảm giá (nếu có)</h3>
                <input type="text" name="discount_code" placeholder="Nhập mã giảm giá">
                <button type="button" class="btn btn-apply-discount">Áp dụng</button>
                <p class="discount-message"></p> <!-- Hiển thị thông báo giảm giá -->
            </div>

            <div class="total-price" id="discounted-total">
                Tổng giá sau giảm giá: <span
                    id="total_after_discount"><?= number_format($total_after_discount ?? 0); ?>₫</span>
            </div>

            <div class="user-info">
                <h3>Thông tin của bạn</h3>
                <p><i class="fas fa-user"></i><span><?= $fetch_profile['name'] ?></span></p>
                <p><i class="fas fa-phone"></i><span><?= $fetch_profile['phoneNumber'] ?></span></p>
                <p><i class="fas fa-envelope"></i><span><?= $fetch_profile['email'] ?></span></p>
                <a href="update_profile.php" class="btn">Cập nhật thông tin</a>
                <h3>Địa chỉ giao hàng</h3>
                <p><i class="fas fa-map-marker-alt"></i><span><?php if ($fetch_profile['address'] == '') {
                                                                    echo 'Vui lòng nhập địa chỉ của bạn';
                                                                } else {
                                                                    echo $fetch_profile['address'];
                                                                } ?></span></p>
                <a href="update_address.php" class="btn">Cập nhật địa chỉ</a>

                <!-- Input ẩn để gửi dữ liệu -->
                <input type="hidden" name="total_products" value="<?= $total_products; ?>">
                <input type="hidden" name="total_price" value="<?= $grand_total; ?>">
                <input type="hidden" name="name" value="<?= $fetch_profile['name'] ?>">
                <input type="hidden" name="number" value="<?= $fetch_profile['phoneNumber'] ?>">
                <input type="hidden" name="email" value="<?= $fetch_profile['email'] ?>">
                <input type="hidden" name="address" value="<?= $fetch_profile['address'] ?>">
                <input type="hidden" name="total_price_coupon" id="total_price_coupon" value="<?= $grand_total; ?>">

                <div class="payment-method">
                    <h3>Phương thức thanh toán</h3>
                    <select name="method" class="box" required id="payment-method-select">
                        <option value="" disabled selected>Chọn phương thức thanh toán</option>
                        <option value="thanh toán khi nhận hàng">Thanh toán khi nhận hàng</option>
                        <option value="thẻ tín dụng">Thanh toán trực tuyến (VNPay)</option>
                    </select>

                    <div class="vnpay-info" id="vnpay-info">
                        <p><i class="fas fa-info-circle"></i> Thanh toán qua VNPay hỗ trợ:</p>
                        <p><i class="fas fa-credit-card"></i> Thẻ ATM/Tài khoản ngân hàng</p>
                        <p><i class="fas fa-mobile-alt"></i> Ví điện tử (MoMo, ZaloPay, etc.)</p>
                        <p><i class="fas fa-qrcode"></i> Quét mã QR</p>
                        <p><i class="fas fa-shield-alt"></i> Bảo mật cao với công nghệ SSL</p>
                    </div>
                </div>

                <input type="submit" value="Đặt hàng" class="btn <?php if ($fetch_profile['address'] == '') {
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý hiển thị thông tin VNPay
        const paymentSelect = document.getElementById('payment-method-select');
        const vnpayInfo = document.getElementById('vnpay-info');

        paymentSelect.addEventListener('change', function() {
            if (this.value === 'thẻ tín dụng') {
                vnpayInfo.classList.add('show');
            } else {
                vnpayInfo.classList.remove('show');
            }
        });

        // Xử lý áp dụng mã giảm giá
        document.querySelector('.btn-apply-discount').addEventListener('click', function() {
            let discountCode = document.querySelector('input[name="discount_code"]').value;

            if (discountCode.trim() === '') {
                alert("Vui lòng nhập mã giảm giá.");
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "apply_discount.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    const discountMessage = document.querySelector('.discount-message');
                    const discountedTotal = document.getElementById('discounted-total');
                    const totalAfterDiscount = document.getElementById('total_after_discount');
                    const totalPriceCouponInput = document.getElementById('total_price_coupon');

                    if (response.success) {
                        // Hiển thị thông báo thành công
                        discountMessage.textContent = response.message;
                        discountMessage.style.color = 'green';

                        // Cập nhật tổng giá sau giảm giá và hiển thị nó
                        totalAfterDiscount.textContent = response.total_after_discount + "₫";
                        discountedTotal.classList.add('show'); // Hiện phần giá sau giảm giá

                        // Cập nhật giá trị vào input ẩn
                        totalPriceCouponInput.value = response.total_after_discount;
                    } else {
                        // Hiển thị thông báo lỗi
                        discountMessage.textContent = response.message;
                        discountMessage.style.color = 'red';

                        // Ẩn phần hiển thị giá sau giảm giá
                        discountedTotal.classList.remove('show');

                        // Reset giá trị trong input ẩn về giá gốc
                        totalPriceCouponInput.value = <?= $grand_total ?>;
                    }
                }
            };

            xhr.send("discount_code=" + encodeURIComponent(discountCode) + "&grand_total=" +
                encodeURIComponent(<?= $grand_total ?>));
        });
    });
    </script>
</body>

</html>
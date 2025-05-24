<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit();
}

if (isset($_POST['add_product'])) {
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $brand = htmlspecialchars($_POST['brand'], ENT_QUOTES, 'UTF-8');
    $model = htmlspecialchars($_POST['model'], ENT_QUOTES, 'UTF-8');
    $category = htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8');
    $price = htmlspecialchars($_POST['price'], ENT_QUOTES, 'UTF-8');
    $ram = htmlspecialchars($_POST['ram'], ENT_QUOTES, 'UTF-8');
    $storage = htmlspecialchars($_POST['storage'], ENT_QUOTES, 'UTF-8');
    $color = htmlspecialchars($_POST['color'], ENT_QUOTES, 'UTF-8');
    $screen = htmlspecialchars($_POST['screen'], ENT_QUOTES, 'UTF-8');
    $battery = htmlspecialchars($_POST['battery'], ENT_QUOTES, 'UTF-8');
    $camera = htmlspecialchars($_POST['camera'], ENT_QUOTES, 'UTF-8');

    // Xử lý file upload
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_ext = pathinfo($image, PATHINFO_EXTENSION);
    $image_folder = '../uploaded_img/';

    $hashed_name = md5(uniqid(rand(), true)) . '.' . $image_ext;
    $full_path = $image_folder . $hashed_name;

    $select_products = $conn->prepare("SELECT * FROM `products` WHERE productName = ?");
    $select_products->execute([$name]);

    if ($select_products->rowCount() > 0) {
        $message[] = 'Tên điện thoại đã tồn tại!';
    } else {
        if ($image_size > 10000000) {
            $message[] = 'Kích thước hình ảnh quá lớn';
        } else {
            move_uploaded_file($image_tmp_name, $full_path);

            $insert_product = $conn->prepare(
                "INSERT INTO `products`
                (productName, brand, model, category, price, ram, storage, color, screen, battery, camera, image)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"
            );
            $insert_product->execute([
                $name, $brand, $model, $category, $price, $ram, $storage, $color, $screen, $battery, $camera, $hashed_name
            ]);

            $message[] = 'Điện thoại mới đã được thêm vào!';
        }
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE productID = ?");
    $delete_product_image->execute([$delete_id]);
    $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
    if (!empty($fetch_delete_image['image'])) {
        @unlink('../uploaded_img/' . $fetch_delete_image['image']);
    }
    $delete_product = $conn->prepare("DELETE FROM `products` WHERE productID = ?");
    $delete_product->execute([$delete_id]);
    $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE productID = ?");
    $delete_cart->execute([$delete_id]);
    header('location:products.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý điện thoại</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>

<?php include '../components/admin_header.php' ?>

<!-- add products section starts  -->

<section class="add-products">
    <form action="" method="POST" enctype="multipart/form-data">
        <h3>Thêm điện thoại mới</h3>
        <input type="text" required placeholder="Tên điện thoại" name="name" maxlength="100" class="box">
        <input type="text" required placeholder="Hãng (Apple, Samsung...)" name="brand" maxlength="50" class="box">
        <input type="text" required placeholder="Model (A3100, SM-S928B...)" name="model" maxlength="50" class="box">
        <select name="category" class="box" required>
            <option value="" disabled selected>Chọn hãng</option>
            <option value="iPhone">iPhone</option>
            <option value="Samsung">Samsung</option>
            <option value="Xiaomi">Xiaomi</option>
            <option value="Oppo">Oppo</option>
            <option value="Realme">Realme</option>
            <option value="Vivo">Vivo</option>
            <option value="Nokia">Nokia</option>
            <option value="Asus">Asus</option>
            <option value="Khác">Khác</option>
        </select>
        <input type="number" min="0" max="9999999999" required placeholder="Giá điện thoại" name="price" class="box">
        <input type="number" min="1" max="32" required placeholder="RAM (GB)" name="ram" class="box">
        <input type="number" min="1" max="4096" required placeholder="Bộ nhớ (GB)" name="storage" class="box">
        <input type="text" required placeholder="Màu sắc (Đen, Xám...)" name="color" maxlength="20" class="box">
        <input type="text" required placeholder="Màn hình (6.7 inch OLED...)" name="screen" maxlength="50" class="box">
        <input type="text" required placeholder="Pin (4500 mAh...)" name="battery" maxlength="30" class="box">
        <input type="text" required placeholder="Camera (48MP, 200MP...)" name="camera" maxlength="20" class="box">
        <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" required>
        <input type="submit" value="Thêm điện thoại" name="add_product" class="btn">
    </form>
</section>

<!-- add products section ends -->

<!-- show products section starts  -->
<section class="show-products" style="padding-top: 0;">
    <div class="box-container">
        <?php
        $show_products = $conn->prepare("SELECT * FROM `products`");
        $show_products->execute();
        if ($show_products->rowCount() > 0) {
            while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
        ?>
                <div class="box">
                    <img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="">
                    <div class="flex">
                        <div class="price"><?= number_format($fetch_products['price']); ?>₫</div>
                        <div class="category"><?= $fetch_products['category']; ?></div>
                    </div>
                    <div class="name"><?= $fetch_products['productName']; ?> (<?= $fetch_products['brand']; ?>)</div>
                    <div class="desc">
                        Model: <?= $fetch_products['model']; ?> | RAM: <?= $fetch_products['ram']; ?>GB | ROM: <?= $fetch_products['storage']; ?>GB | Màu: <?= $fetch_products['color']; ?><br>
                        Màn hình: <?= $fetch_products['screen']; ?> | Pin: <?= $fetch_products['battery']; ?> | Camera: <?= $fetch_products['camera']; ?>
                    </div>
                    <div class="flex-btn">
                        <a href="update_product.php?update=<?= $fetch_products['productID']; ?>" class="option-btn">Sửa</a>
                        <a href="products.php?delete=<?= $fetch_products['productID']; ?>" class="delete-btn"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa điện thoại này?');">Xóa</a>
                    </div>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty">Chưa có điện thoại nào được thêm vào!</p>';
        }
        ?>
    </div>
</section>
<!-- show products section ends -->

<script src="../js/admin_script.js"></script>
</body>
</html>

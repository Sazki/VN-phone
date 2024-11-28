<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
};

if (isset($_POST['add_product'])) {

    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $price = htmlspecialchars($_POST['price'], ENT_QUOTES, 'UTF-8');
    $category = htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8');

    // Xử lý file upload
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_ext = pathinfo($image, PATHINFO_EXTENSION); // Lấy phần mở rộng của file
    $image_folder = '../uploaded_img/';

    // Tạo tên file mã hóa
    $hashed_name = md5(uniqid(rand(), true)) . '.' . $image_ext;

    $full_path = $image_folder . $hashed_name;

    $select_products = $conn->prepare("SELECT * FROM `products` WHERE productName = ?");
    $select_products->execute([$name]);

    if ($select_products->rowCount() > 0) {
        $message[] = 'tên sản phẩm đã tồn tại!';
    } else {
        if ($image_size > 10000000) {
            $message[] = 'kích thước hình ảnh quá lớn';
        } else {
            move_uploaded_file($image_tmp_name, $full_path);

            $insert_product = $conn->prepare("INSERT INTO `products`(productName, category, price, image) VALUES(?,?,?,?)");
            $insert_product->execute([$name, $category, $price, $hashed_name]);

            $message[] = 'sản phẩm mới đã được thêm vào!';
        }
    }
}

if (isset($_GET['delete'])) {

    $delete_id = $_GET['delete'];
    $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE productID = ?");
    $delete_product_image->execute([$delete_id]);
    $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
    unlink('../uploaded_img/' . $fetch_delete_image['image']);
    $delete_product = $conn->prepare("DELETE FROM `products` WHERE productID = ?");
    $delete_product->execute([$delete_id]);
    $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE productID = ?");
    $delete_cart->execute([$delete_id]);
    header('location:products.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>products</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <?php include '../components/admin_header.php' ?>

    <!-- add products section starts  -->

    <section class="add-products">

        <form action="" method="POST" enctype="multipart/form-data">
            <h3>add product</h3>
            <input type="text" required placeholder="nhập tên sản phẩm" name="name" maxlength="100" class="box">
            <input type="number" min="0" max="9999999999" required placeholder="nhập giá sản phẩm" name="price"
                onkeypress="if(this.value.length == 10) return false;" class="box">
            <select name="category" class="box" required>
                <option value="" disabled selected>chọn danh mục sản phẩm</option>
                <option value="món ăn chính">món ăn chính</option>
                <option value="thức ăn nhanh">thức ăn nhanh</option>
                <option value="đồ uống">đồ uống</option>
                <option value="món tráng miệng">món tráng miệng</option>
            </select>
            <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" required>
            <input type="submit" value="add product" name="add_product" class="btn">
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
                            <div class="price"><?= $fetch_products['price']; ?>k</div>
                            <div class="category"><?= $fetch_products['category']; ?></div>
                        </div>
                        <div class="name"><?= $fetch_products['productName']; ?></div>
                        <div class="flex-btn">
                            <a href="update_product.php?update=<?= $fetch_products['productID']; ?>" class="option-btn">Sửa</a>
                            <a href="products.php?delete=<?= $fetch_products['productID']; ?>" class="delete-btn"
                                onclick="return confirm('delete this product?');">Xóa</i></a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">chưa có sản phẩm nào được thêm vào!</p>';
            }

            ?>

        </div>

    </section>

    <!-- show products section ends -->










    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>
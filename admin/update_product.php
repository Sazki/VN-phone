<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
};

if (isset($_POST['update'])) {

    $pid = htmlspecialchars($_POST['pid'], ENT_QUOTES, 'UTF-8');
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $price = htmlspecialchars($_POST['price'], ENT_QUOTES, 'UTF-8');
    $category = htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8');

    $update_product = $conn->prepare("UPDATE `products` SET productName = ?, category = ?, price = ? WHERE productID = ?");
    $update_product->execute([$name, $category, $price, $pid]);

    $message[] = 'sản phẩm đã được cập nhật!';

    $old_image = $_POST['old_image'];
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_ext = pathinfo($image, PATHINFO_EXTENSION); // Lấy phần mở rộng của file
    $image_folder = '../uploaded_img/';

    // Tạo tên file mới được mã hóa
    $hashed_name = md5(uniqid(rand(), true)) . '.' . $image_ext;
    $full_path = $image_folder . $hashed_name;


    if (!empty($image)) {
        if ($image_size > 10000000) {
            $message[] = 'kích thước hình ảnh quá lớn!';
        } else {
            $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE productID = ?");
            $update_image->execute([$hashed_name, $pid]);

            // Di chuyển file ảnh mới
            move_uploaded_file($image_tmp_name, $full_path);

            // Xóa ảnh cũ (nếu tồn tại)
            if (file_exists($image_folder . $old_image) && !empty($old_image)) {
                unlink($image_folder . $old_image);
            }

            $message[] = 'Ảnh sản phẩm đã được cập nhật!';
        }
    } else {
        $message[] = 'Không có ảnh nào được chọn!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>update product</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <?php include '../components/admin_header.php' ?>

    <!-- update product section starts  -->

    <section class="update-product">

        <h1 class="heading">update product</h1>

        <?php
        $update_id = $_GET['update'];
        $show_products = $conn->prepare("SELECT * FROM `products` WHERE productID = ?");
        $show_products->execute([$update_id]);
        if ($show_products->rowCount() > 0) {
            while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="pid" value="<?= $fetch_products['productID']; ?>">
            <input type="hidden" name="old_image" value="<?= $fetch_products['image']; ?>">
            <img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="">
            <span>cập nhật tên</span>
            <input type="text" required placeholder="nhập tên sản phẩm" name="name" maxlength="100" class="box"
                value="<?= $fetch_products['productName']; ?>">
            <span>cập nhật giá</span>
            <input type="number" min="0" max="9999999999" required placeholder="nhập giá sản phẩm" name="price"
                onkeypress="if(this.value.length == 10) return false;" class="box"
                value="<?= $fetch_products['price']; ?>">
            <span>cập nhật danh mục</span>
            <select name="category" class="box" required>
                <option selected value="<?= $fetch_products['category']; ?>"><?= $fetch_products['category']; ?>
                </option>
                <option value="món ăn chính">món ăn chính</option>
                <option value="thức ăn nhanh">thức ăn nhanh</option>
                <option value="đồ uống">đồ uống</option>
                <option value="món tráng miệng">món tráng miệng</option>
            </select>
            <span>update image</span>
            <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
            <div class="flex-btn">
                <input type="submit" value="cập nhật" class="btn" name="update">
                <a href="products.php" class="option-btn">quay lại</a>
            </div>
        </form>
        <?php
            }
        } else {
            echo '<p class="empty">chưa có sản phẩm nào được thêm vào!</p>';
        }
        ?>

    </section>

    <!-- update product section ends -->










    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>
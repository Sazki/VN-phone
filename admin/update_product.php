<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit();
}

if (isset($_POST['update'])) {
    $pid = htmlspecialchars($_POST['pid'], ENT_QUOTES, 'UTF-8');
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
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');

    $update_product = $conn->prepare("UPDATE `products` SET productName = ?, brand = ?, model = ?, category = ?, price = ?, ram = ?, storage = ?, color = ?, screen = ?, battery = ?, camera = ?, description = ? WHERE productID = ?");
    $update_product->execute([$name, $brand, $model, $category, $price, $ram, $storage, $color, $screen, $battery, $camera, $description, $pid]);

    $message[] = 'Sản phẩm đã được cập nhật!';

    // Xử lý ảnh
    $old_image = $_POST['old_image'];
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_ext = pathinfo($image, PATHINFO_EXTENSION);
    $image_folder = '../uploaded_img/';
    $hashed_name = md5(uniqid(rand(), true)) . '.' . $image_ext;
    $full_path = $image_folder . $hashed_name;

    if (!empty($image)) {
        if ($image_size > 10000000) {
            $message[] = 'Kích thước hình ảnh quá lớn!';
        } else {
            $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE productID = ?");
            $update_image->execute([$hashed_name, $pid]);
            move_uploaded_file($image_tmp_name, $full_path);

            if (file_exists($image_folder . $old_image) && !empty($old_image)) {
                unlink($image_folder . $old_image);
            }

            $message[] = 'Ảnh sản phẩm đã được cập nhật!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật sản phẩm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<style>
    
    body {
        background: #f5f6fa;
        font-family: 'Segoe UI', Arial, sans-serif;
    }
    .update-product {
        max-width: 550px;
        margin: 40px auto 0 auto;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.10);
        padding: 32px 32px 24px 32px;
    }
    .update-product h1.heading {
        text-align: center;
        color: #2d568d;
        margin-bottom: 24px;
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: 1px;
    }
    .update-product form {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .update-product span {
        font-weight: 600;
        margin-bottom: 3px;
        color: #2d568d;
    }
    .update-product input[type="text"],
    .update-product input[type="number"],
    .update-product select,
    .update-product textarea {
        width: 100%;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #c7d0e2;
        font-size: 2rem;
        margin-bottom: 4px;
        background: #f7faff;
        transition: border-color 0.2s;
    }
    .update-product input[type="file"] {
        margin-top: 2px;
        background: #f7faff;
    }
    .update-product input:focus,
    .update-product select:focus,
    .update-product textarea:focus {
        border-color: #2d568d;
        outline: none;
        background: #e8f0fe;
    }
    .update-product img {
        display: block;
        margin: 0 auto 14px auto;
        border-radius: 14px;
        border: 1.5px solid #dde7f3;
        max-width: 160px;
        max-height: 180px;
        box-shadow: 0 1px 8px rgba(44,86,141,0.12);
        background: #fff;
        object-fit: cover;
    }
    .flex-btn {
        display: flex;
        gap: 12px;
        margin-top: 14px;
        justify-content: center;
    }
    .btn, .option-btn {
        padding: 10px 22px;
        border-radius: 8px;
        border: none;
        background: linear-gradient(90deg, #2d568d, #5487c8 80%);
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 1px 6px rgba(44,86,141,0.12);
        transition: background 0.2s, box-shadow 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .option-btn {
        background: #f1f5fa;
        color: #2d568d;
        border: 1px solid #c2d4ec;
    }
    .btn:hover, .option-btn:hover {
        box-shadow: 0 4px 12px rgba(44,86,141,0.14);
        opacity: 0.93;
    }
    .update-product textarea.box {
        min-height: 54px;
        resize: vertical;
    }
    .message {
        background: #eaf6f9;
        color: #2292b1;
        border: 1px solid #b7e0f3;
        padding: 8px 12px;
        border-radius: 7px;
        margin-bottom: 14px;
        font-size: 1rem;
        text-align: center;
    }
    .empty {
        text-align: center;
        color: #bbb;
        margin: 32px 0;
        font-size: 1.2rem;
    }
    @media (max-width: 650px) {
        .update-product {
            padding: 18px 4vw 12px 4vw;
        }
        .update-product img {
            max-width: 95vw;
        }
    }

</style>
<body>
    <?php include '../components/admin_header.php' ?>

    <section class="update-product">
        <h1 class="heading">Cập nhật sản phẩm</h1>
        <?php
        if (isset($message)) {
            foreach ($message as $msg) {
                echo '<div class="message">' . $msg . '</div>';
            }
        }
        $update_id = $_GET['update'];
        $show_products = $conn->prepare("SELECT * FROM `products` WHERE productID = ?");
        $show_products->execute([$update_id]);
        if ($show_products->rowCount() > 0) {
            while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="pid" value="<?= $fetch_products['productID']; ?>">
            <input type="hidden" name="old_image" value="<?= $fetch_products['image']; ?>">
            <img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="" style="width:150px;height:auto;">
            
            <span>Cập nhật tên</span>
            <input type="text" required name="name" maxlength="100" class="box" value="<?= $fetch_products['productName']; ?>">

            <span>Cập nhật hãng (Brand)</span>
            <input type="text" required name="brand" class="box" value="<?= $fetch_products['brand']; ?>">

            <span>Cập nhật model</span>
            <input type="text" required name="model" class="box" value="<?= $fetch_products['model']; ?>">

            <span>Cập nhật danh mục</span>
            <select name="category" class="box" required>
                <option selected value="<?= $fetch_products['category']; ?>"><?= $fetch_products['category']; ?></option>
                <option value="iPhone">iPhone</option>
                <option value="Samsung">Samsung</option>
                <option value="Xiaomi">Xiaomi</option>
                <option value="Oppo">Oppo</option>
                <option value="Realme">Realme</option>
                <option value="Khác">Khác</option>
            </select>

            <span>Cập nhật giá</span>
            <input type="number" min="0" max="9999999999" required name="price" class="box" value="<?= $fetch_products['price']; ?>">

            <span>Cập nhật RAM (GB)</span>
            <input type="number" name="ram" min="1" class="box" value="<?= $fetch_products['ram']; ?>">

            <span>Cập nhật bộ nhớ (Storage, GB)</span>
            <input type="number" name="storage" min="1" class="box" value="<?= $fetch_products['storage']; ?>">

            <span>Cập nhật màu sắc</span>
            <input type="text" name="color" class="box" value="<?= $fetch_products['color']; ?>">

            <span>Cập nhật màn hình</span>
            <input type="text" name="screen" class="box" value="<?= $fetch_products['screen']; ?>">

            <span>Cập nhật pin (Battery, mAh)</span>
            <input type="text" name="battery" class="box" value="<?= $fetch_products['battery']; ?>">

            <span>Cập nhật camera</span>
            <input type="text" name="camera" class="box" value="<?= $fetch_products['camera']; ?>">

            <span>Cập nhật mô tả</span>
            <textarea name="description" class="box" rows="3"><?= $fetch_products['description']; ?></textarea>

            <span>Update image</span>
            <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
            <div class="flex-btn">
                <input type="submit" value="Cập nhật" class="btn" name="update">
                <a href="products.php" class="option-btn">Quay lại</a>
            </div>
        </form>
        <?php
            }
        } else {
            echo '<p class="empty">Chưa có sản phẩm nào được thêm vào!</p>';
        }
        ?>
    </section>
    <script src="../js/admin_script.js"></script>
</body>
</html>

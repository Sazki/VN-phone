<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
};

include 'components/add_cart.php';

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm sản phẩm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .search-form form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: 110px;
        }
        .search-form .box {
            width: 340px;
            padding: 11px 15px;
            border-radius: 8px;
            font-size: 1.09rem;
            border: 1.5px solid #bbb;
        }
        .search-form button {
            background: linear-gradient(90deg,#ff9800,#ffcc33);
            color: #232526;
            border: none;
            padding: 12px 19px;
            border-radius: 7px;
            font-size: 1.17rem;
            cursor: pointer;
            transition: background 0.21s, color 0.17s, transform 0.22s;
        }
        .search-form button:hover {
            background: linear-gradient(90deg,#36d1c4,#5b86e5);
            color: #fff;
            transform: scale(1.04);
        }
        .products .box-container {
            display: flex;
            flex-wrap: wrap;
            gap: 26px;
            justify-content: center;
            margin-top: 30px;
        }
        .products .box {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px #dedede55;
            width: 300px;
            padding: 23px 15px 13px 15px;
            position: relative;
            transition: box-shadow 0.18s;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .products .box:hover {
            box-shadow: 0 8px 28px #b7e8fd3c;
            transform: translateY(-2px) scale(1.012);
        }
        .products .box img {
            width: 116px; height: 116px; object-fit: contain;
            border-radius: 7px; margin-bottom: 8px; background: #f8f8f8;
        }
        .products .cat {
            font-size: 0.98rem;
            color: #555;
            margin-bottom: 2px;
        }
        .products .name {
            font-size: 1.12rem;
            font-weight: 600;
            color: #232526;
            text-align: center;
            margin-bottom: 6px;
        }
        .products .specs {
            font-size: 0.98rem;
            color: #666;
            text-align: center;
            margin-bottom: 4px;
        }
        .products .flex {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            margin-bottom: 7px;
            width: 100%;
        }
        .products .price {
            color: #ff9800;
            font-size: 1.09rem;
            font-weight: 700;
            min-width: 84px;
            text-align: left;
        }
        .products .qty {
            width: 45px;
            padding: 7px 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        .products .fas.fa-eye,
        .products .fas.fa-shopping-cart {
            color: #fff;
            font-size: 1.06rem;
            border-radius: 5px;
            padding: 6px 10px;
            margin-right: 3px;
            cursor: pointer;
            background: linear-gradient(90deg,#ff9800,#ffcc33);
            transition: background 0.15s, color 0.15s;
        }
        .products .fas.fa-eye:hover {
            background: linear-gradient(90deg,#36d1c4,#5b86e5); color: #fff;
        }
        .products .fas.fa-shopping-cart:hover {
            background: linear-gradient(90deg,#36d1c4,#5b86e5); color: #fff;
        }
        @media (max-width: 700px) {
            .products .box { width: 97vw; }
            .search-form .box { width: 93vw; }
        }
    </style>
</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <section class="search-form">
        <form method="post" action="">
            <input type="text" name="search_box" placeholder="Tìm điện thoại, hãng, model, ... " class="box">
            <button type="submit" name="search_btn" class="fas fa-search"></button>
        </form>
    </section>

    <section class="products" style="min-height: 100vh; padding-top:0;">
        <div class="box-container">
            <?php
            function short_specs($product) {
                $specs = [];
                if (!empty($product['brand'])) $specs[] = $product['brand'];
                if (!empty($product['model'])) $specs[] = $product['model'];
                if (!empty($product['ram'])) $specs[] = "RAM {$product['ram']}GB";
                if (!empty($product['storage'])) $specs[] = "ROM {$product['storage']}GB";
                if (!empty($product['color'])) $specs[] = "Màu: ".explode(",", $product['color'])[0];
                return implode(' | ', $specs);
            }

            if (isset($_POST['search_box']) && !empty($_POST['search_box'])) {
                $search_box = htmlspecialchars($_POST['search_box'], ENT_QUOTES, 'UTF-8');
                $select_products = $conn->prepare("SELECT * FROM `products` WHERE productName LIKE ? OR category LIKE ? OR brand LIKE ? OR model LIKE ?");
                $select_products->execute([
                    "%{$search_box}%", "%{$search_box}%", "%{$search_box}%", "%{$search_box}%"
                ]);
                if ($select_products->rowCount() > 0) {
                    while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                        // Lấy ảnh theo màu đầu tiên nếu có nhiều ảnh
                        $images = [];
                        if (!empty($fetch_products['images'])) {
                            $images = array_map('trim', explode(',', $fetch_products['images']));
                        }
                        $product_img = !empty($images) ? $images[0] : $fetch_products['image'];
            ?>
                        <form action="" method="post" class="box">
                            <input type="hidden" name="pid" value="<?= $fetch_products['productID']; ?>">
                            <input type="hidden" name="name" value="<?= $fetch_products['productName']; ?>">
                            <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
                            <input type="hidden" name="image" value="<?= $product_img; ?>">
                            <a href="quick_view.php?pid=<?= $fetch_products['productID']; ?>" class="fas fa-eye" title="Xem chi tiết"></a>
                            <button type="submit" class="fas fa-shopping-cart" name="add_to_cart" title="Thêm vào giỏ"></button>
                            <img src="uploaded_img/<?= $product_img; ?>" alt="">
                            <a href="category.php?category=<?= $fetch_products['category']; ?>" class="cat"><?= $fetch_products['category']; ?></a>
                            <div class="name"><?= $fetch_products['productName']; ?></div>
                            <div class="specs"><?= short_specs($fetch_products); ?></div>
                            <div class="flex">
                                <div class="price"><?= number_format($fetch_products['price']); ?>₫</div>
                                <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
                            </div>
                        </form>
            <?php
                    }
                } else {
                    echo '<p class="empty">Không có sản phẩm nào khớp với tìm kiếm của bạn!</p>';
                }
            } else {
                // Hiển thị tất cả sản phẩm mặc định
                $select_products = $conn->prepare("SELECT * FROM `products`");
                $select_products->execute();
                if ($select_products->rowCount() > 0) {
                    while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                        $images = [];
                        if (!empty($fetch_products['images'])) {
                            $images = array_map('trim', explode(',', $fetch_products['images']));
                        }
                        $product_img = !empty($images) ? $images[0] : $fetch_products['image'];
            ?>
                        <form action="" method="post" class="box">
                            <input type="hidden" name="pid" value="<?= $fetch_products['productID']; ?>">
                            <input type="hidden" name="name" value="<?= $fetch_products['productName']; ?>">
                            <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
                            <input type="hidden" name="image" value="<?= $product_img; ?>">
                            <a href="quick_view.php?pid=<?= $fetch_products['productID']; ?>" class="fas fa-eye" title="Xem chi tiết"></a>
                            <button type="submit" class="fas fa-shopping-cart" name="add_to_cart" title="Thêm vào giỏ"></button>
                            <img src="uploaded_img/<?= $product_img; ?>" alt="">
                            <a href="category.php?category=<?= $fetch_products['category']; ?>" class="cat"><?= $fetch_products['category']; ?></a>
                            <div class="name"><?= $fetch_products['productName']; ?></div>
                            <div class="specs"><?= short_specs($fetch_products); ?></div>
                            <div class="flex">
                                <div class="price"><?= number_format($fetch_products['price']); ?>₫</div>
                                <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
                            </div>
                        </form>
            <?php
                    }
                } else {
                    echo '<p class="empty">Chưa có sản phẩm nào được thêm vào!</p>';
                }
            }
            ?>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>

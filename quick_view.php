<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

include 'components/add_cart.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem chi tiết sản phẩm</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .quick-view {
            max-width: 900px;
            margin: 0 auto;
            margin-top: 120px;
        }
        .quick-view .box {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 18px #b5b6c440;
            padding: 38px 28px 24px 28px;
            align-items: flex-start;
        }
        .quick-view .box img {
            width: 280px;
            height: 320px;
            object-fit: contain;
            border-radius: 10px;
            background: #f5f5f7;
        }
        .product-info {
            flex: 1;
            min-width: 290px;
        }
        .product-info h2 {
            font-size: 2.1rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .product-specs {
            margin: 19px 0 14px 0;
            padding: 13px 18px;
            background: #fafbff;
            border-radius: 8px;
            font-size: 1.04rem;
            color: #333;
            line-height: 2.15;
            border: 1px solid #ececec;
        }
        .spec-row span {
            display: inline-block;
            min-width: 110px;
            color: #888;
            font-weight: 600;
        }
        .product-info .price {
            color: #ff9800;
            font-size: 1.32rem;
            font-weight: 700;
            margin: 17px 0 11px 0;
        }
        .color-swatches {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 15px;
        }
        .color-dot {
            display: inline-block;
            width: 27px;
            height: 27px;
            border-radius: 50%;
            border: 2px solid #bbb;
            cursor: pointer;
            margin-right: 2px;
            transition: border 0.17s, transform 0.16s;
        }
        .color-dot.selected, .color-dot:hover {
            border: 3px solid #ff9800;
            transform: scale(1.1);
        }
        .product-info .desc {
            margin-top: 18px;
            font-size: 1.09rem;
            color: #333;
            line-height: 1.6;
            background: #f7f7fb;
            padding: 15px 16px;
            border-radius: 7px;
            border: 1px solid #e8e8e8;
        }
        .product-info .flex {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .cart-btn {
            background: linear-gradient(90deg,#ff9800,#ffcc33);
            color: #232526;
            font-size: 1.14rem;
            font-weight: 600;
            padding: 11px 33px;
            border-radius: 8px;
            border: none;
            margin-top: 20px;
            transition: background 0.22s, color 0.17s, transform 0.22s;
            cursor: pointer;
            box-shadow: 0 2px 12px #fae69c55;
        }
        .cart-btn:hover {
            background: linear-gradient(90deg,#36d1c4,#5b86e5);
            color: #fff;
            transform: scale(1.04);
        }
        .qty {
            width: 64px;
            padding: 7px 11px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 1.12rem;
        }
        @media (max-width: 700px) {
            .quick-view .box { flex-direction: column; align-items: center; gap: 15px; }
            .quick-view .box img { width: 90vw; max-width:320px; height: auto;}
        }
    </style>
</head>

<body>
    <?php include 'components/user_header.php'; ?>

    <section class="quick-view">
        <h1 class="title">Chi tiết sản phẩm</h1>
        <?php
        $pid = $_GET['pid'];
        $select_products = $conn->prepare("SELECT * FROM `products` WHERE productID = ?");
        $select_products->execute([$pid]);
        if ($select_products->rowCount() > 0) {
            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                // Xử lý ảnh/màu sắc
                $images = [];
                if (!empty($fetch_products['images'])) {
                    $images = array_map('trim', explode(',', $fetch_products['images']));
                } else {
                    $images[] = $fetch_products['image'];
                }
                $colors = [];
                if (!empty($fetch_products['color'])) {
                    $colors = array_map('trim', explode(',', $fetch_products['color']));
                }
                $product_id = $fetch_products['productID'];
        ?>
        <form action="" method="post" class="box" id="product-form">
            <input type="hidden" name="pid" value="<?= $fetch_products['productID']; ?>">
            <input type="hidden" name="name" value="<?= $fetch_products['productName']; ?>">
            <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
            <input type="hidden" name="image" value="<?= $images[0]; ?>" id="selected-image">
            <div>
                <img id="main-image" src="uploaded_img/<?= $images[0]; ?>" alt="" style="margin-bottom: 10px;">
                <?php if (count($colors) > 1): ?>
                <div class="color-swatches">
                    <?php foreach ($colors as $index => $color): ?>
                        <span 
                            class="color-dot<?= $index==0 ? ' selected':'';?>" 
                            data-img="uploaded_img/<?= $images[$index] ?? $images[0]; ?>" 
                            data-imgname="<?= $images[$index] ?? $images[0]; ?>" 
                            data-color="<?= htmlspecialchars($color); ?>"
                            title="<?= htmlspecialchars($color); ?>"
                            style="background:<?= strtolower($color); ?>"
                        ></span>
                    <?php endforeach; ?>
                </div>
                <?php elseif(count($colors) === 1): ?>
                    <div style="margin: 11px 0 8px 0; color:#666;"><b>Màu:</b> <?= htmlspecialchars($colors[0]); ?></div>
                <?php endif; ?>
            </div>
            <div class="product-info">
                <h2><?= htmlspecialchars($fetch_products['productName']); ?></h2>
                <div class="product-specs">
                    <div class="spec-row"><span>Hãng:</span> <?= htmlspecialchars($fetch_products['brand']); ?></div>
                    <div class="spec-row"><span>Model:</span> <?= htmlspecialchars($fetch_products['model']); ?></div>
                    <div class="spec-row"><span>RAM:</span> <?= htmlspecialchars($fetch_products['ram']); ?> GB</div>
                    <div class="spec-row"><span>Bộ nhớ:</span> <?= htmlspecialchars($fetch_products['storage']); ?> GB</div>
                    <div class="spec-row"><span>Màn hình:</span> <?= htmlspecialchars($fetch_products['screen']); ?></div>
                    <div class="spec-row"><span>Pin:</span> <?= htmlspecialchars($fetch_products['battery']); ?></div>
                    <div class="spec-row"><span>Camera:</span> <?= htmlspecialchars($fetch_products['camera']); ?></div>
                    <div class="spec-row"><span>Danh mục:</span> <?= htmlspecialchars($fetch_products['category']); ?></div>
                </div>
                <div class="price"><?= number_format($fetch_products['price']); ?>₫</div>
                <div class="flex">
                    <label for="qty">Số lượng:</label>
                    <input type="number" name="qty" class="qty" id="qty" min="1" max="99" value="1" maxlength="2">
                </div>
                <button type="submit" name="add_to_cart" class="cart-btn">Thêm vào giỏ hàng</button>
                <div class="desc"><?= nl2br(htmlspecialchars($fetch_products['description'])); ?></div>
            </div>
        </form>
        <script>
        // Xử lý đổi ảnh khi chọn màu ở trang chi tiết
        document.querySelectorAll('.color-dot').forEach(function(dot){
            dot.addEventListener('click', function(){
                document.querySelectorAll('.color-dot').forEach(function(d){ d.classList.remove('selected'); });
                this.classList.add('selected');
                var imgSrc = this.getAttribute('data-img');
                var imgName = this.getAttribute('data-imgname');
                document.getElementById('main-image').src = imgSrc;
                document.getElementById('selected-image').value = imgName;
            });
        });
        </script>
        <?php
            }
        } else {
            echo '<p class="empty">Không tìm thấy sản phẩm nào!</p>';
        }
        ?>
    </section>
    <?php include 'components/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>

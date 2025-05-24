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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>home</title>

    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css\style.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>



    <section class="hero">

        <div class="swiper hero-slider" style="margin-top:100px">

            <div class="swiper-wrapper">
                <div class="swiper-slide slide">
                    <div class="content">
                        <!-- <span>đặt hàng trực tuyến</span> -->
                        <h3>Samsung Galaxy S25 Ultra</h3>
                        <a href="menu.php" class="btn">Xem ngay</a>
                    </div>
                    <div class="image">
                        <img src="images/s25-ultra.png" alt="">
                    </div>
                </div>
                <div class="swiper-slide slide">
                    <div class="content">
                        <!-- <span>đặt hàng trực tuyến</span> -->
                        <h3>Iphone 15 Pro Max</h3>
                        <a href="menu.php" class="btn">Xem ngay</a>
                    </div>
                    <div class="image">
                        <img src="images/iphone15-home.jpg" alt="">
                    </div>
                </div>
                <div class="swiper-slide slide">
                    <div class="content">
                        <!-- <span>đặt hàng trực tuyến</span> -->
                        <h3>Samsung S24 Ultra</h3>
                        <a href="menu.php" class="btn">Xem ngay</a>
                    </div>
                    <div class="image">
                        <img src="images/s24.jpg" alt="">
                    </div>
                </div>

                <div class="swiper-slide slide">
                    <div class="content">
                        <!-- <span>đặt hàng trực tuyến</span> -->
                        <h3>Xiaomi 14 Pro</h3>
                        <a href="menu.php" class="btn">Xem ngay</a>
                    </div>
                    <div class="image">
                        <img src="images/xiaomi14.jpg" alt="">
                    </div>
                </div>

                <div class="swiper-slide slide">
                    <div class="content">
                        <!-- <span>đặt hàng trực tuyến</span> -->
                        <h3>Oppo Reno 12</h3>
                        <a href="menu.php" class="btn">Xem ngay</a>
                    </div>
                    <div class="image">
                        <img src="images/reno12.jpg" alt="">
                    </div>
                </div>

                <div class="swiper-slide slide">
                    <div class="content">
                        <!-- <span>đặt hàng trực tuyến</span> -->
                        <h3>Oppo Find N5</h3>
                        <a href="menu.php" class="btn">Xem ngay</a>
                    </div>
                    <div class="image">
                        <img src="images/oppo-find-n5.png" alt="">
                    </div>
                </div>

            </div>

            <div class="swiper-pagination"></div>

        </div>

    </section>

    <!DOCTYPE html>
    <html lang="vi">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Danh Mục</title>
        <style>
            /* Container Styling */
            .box-container-cate {
                display: flex;
                flex-wrap: wrap;
                /* Cho phép các hộp xuống dòng */
                gap: 5px;
                /* Khoảng cách giữa các hộp */
                justify-content: space-evenly;
                /* Căn đều các hộp, kể cả khoảng trống */
                padding: 5px;
                /* Thêm khoảng cách giữa container và các cạnh màn hình */
                box-sizing: border-box;
                /* Đảm bảo padding không làm thay đổi kích thước */
            }

            /* Individual Box Styling */
            .box-show {
                position: relative;
                width: 280px;
                height: 200px;
                border-radius: 8px;
                overflow: hidden;
                text-decoration: none;
                color: white;
                transition: transform 0.3s ease-in-out;
                background: #f3f3f3;
            }

            .box-show img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: 0.5s ease-in-out;
            }

            .box-show .info {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: rgba(0, 0, 0, 0.7);
                color: white;
                text-align: center;
                padding: 10px;
                font-size: 18px;
                font-weight: bold;
                transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
                z-index: 1;
            }

            .box-show .overlay {
                position: absolute;
                bottom: -60px;
                /* Ban đầu ở ngoài khung */
                left: 0;
                right: 0;
                background: rgba(0, 0, 0, 0.7);
                color: white;
                text-align: center;
                padding: 10px;
                font-size: 18px;
                font-weight: bold;
                transition: 0.5s ease-in-out;
                z-index: 2;
                opacity: 0;
            }

            /* Hover Effects */
            .box-show:hover {
                transform: scale(1.05);
            }

            .box-show:hover img {
                filter: brightness(0.8);
            }

            .box-show:hover .info {
                opacity: 0;
                transform: translateY(-20px);
                /* Đẩy nội dung lên trên khi ẩn */
            }

            .box-show:hover .overlay {
                bottom: 0;
                /* Hiện overlay lên khi hover */
                opacity: 1;
            }

            /* Section Title Styling */
            .category .title {
                text-align: center;
                font-size: 28px;
                margin-bottom: 20px;
                color: #333;
            }
        </style>
    </head>

    <body>
        <section class="category">
            <h1 class="title">Danh mục</h1>
            <div class="box-container-cate">
                <a href="category.php?category=Iphone" class="box-show">
                    <img src="images/apple.jpg" alt="Apple">
                    <div class="info">Iphone</div>
                    <div class="overlay">Xem ngay</div>
                </a>

                <a href="category.php?category=Samsung" class="box-show">
                    <img src="images/samsung.jpg" alt="Samsung">
                    <div class="info">Samsung</div>
                    <div class="overlay">Xem ngay</div>
                </a>

                <a href="category.php?category=Xiaomi" class="box-show">
                    <img src="images/xiaomi.jpg" alt="Xiaomi">
                    <div class="info">Xiaomi</div>
                    <div class="overlay">Xem ngay</div>
                </a>

                <a href="category.php?category=Oppo" class="box-show">
                    <img src="images/oppo.jpg" alt="Oppo">
                    <div class="info">Oppo</div>
                    <div class="overlay">Xem ngay</div>
                                </a>
            </div>
        </section>
    </body>

    </html>






    <section class="products">

    <h1 class="title">Sản phẩm mới nhất</h1>

    <div class="box-container">

        <?php
        $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY productID DESC LIMIT 6");
        $select_products->execute();
        if ($select_products->rowCount() > 0) {
            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                // Lấy ảnh màu đầu nếu có nhiều ảnh
                $images = [];
                if (!empty($fetch_products['images'])) {
                    $images = array_map('trim', explode(',', $fetch_products['images']));
                }
                $product_img = !empty($images) ? $images[0] : $fetch_products['image'];

                // Hiển thị các thông số cơ bản ngắn gọn
                $specs = [];
                if (!empty($fetch_products['brand'])) $specs[] = $fetch_products['brand'];
                if (!empty($fetch_products['model'])) $specs[] = $fetch_products['model'];
                if (!empty($fetch_products['ram'])) $specs[] = "RAM {$fetch_products['ram']}GB";
                if (!empty($fetch_products['storage'])) $specs[] = "ROM {$fetch_products['storage']}GB";
                if (!empty($fetch_products['color'])) $specs[] = "Màu: ".explode(",", $fetch_products['color'])[0];
                $short_specs = implode(' | ', $specs);
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
                    <div class="specs" style="font-size:0.97rem;color:#666; margin-bottom:3px;"><?= $short_specs; ?></div>
                    <div class="flex">
                        <div class="price"><?= number_format((float)$fetch_products['price']); ?>₫</div>
                        <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
                    </div>
                </form>
        <?php
            }
        } else {
            echo '<p class="empty">Chưa có sản phẩm nào được thêm vào!</p>';
        }
        ?>

    </div>

    <div class="more-btn">
        <a href="menu.php" class="btn">Xem tất cả</a>
    </div>

</section>





    <div id="vnfood-loader-container">
        <div class="vnfood-loader">VN-Phone</div>
    </div>



    <?php include 'components/footer.php'; ?>


    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <script>
        var swiper = new Swiper(".hero-slider", {
            loop: true,
            grabCursor: true,
            effect: "flip",
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });
        document.addEventListener('DOMContentLoaded', () => {
            const loaderContainer = document.getElementById('vnfood-loader-container');
            setTimeout(() => {
                loaderContainer.style.display = 'none'; // Ẩn loader sau 2.5 giây
            }, 2000); // Thời gian khớp với animation (2.5s)
        });
    </script>
    

</body>

</html>
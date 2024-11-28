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
    <title>search page</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <!-- search form section starts  -->

    <section class="search-form">
        <form method="post" action="" style="margin-top: 100px">
            <input type="text" name="search_box" placeholder="Tìm kiếm ở đây" class="box">
            <button type="submit" name="search_btn" class="fas fa-search"></button>
        </form>
    </section>

    <!-- search form section ends -->


    <section class="products" style="min-height: 100vh; padding-top:0;">
        <div class="box-container">
            <?php
            // Kiểm tra xem có tìm kiếm hay không
            if (isset($_POST['search_box']) && !empty($_POST['search_box'])) {
                $search_box = htmlspecialchars($_POST['search_box'], ENT_QUOTES, 'UTF-8');
                $select_products = $conn->prepare("SELECT * FROM `products` WHERE productName LIKE ? OR category LIKE ?");
                $select_products->execute(["%{$search_box}%", "%{$search_box}%"]);
                if ($select_products->rowCount() > 0) {
                    while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
            ?>
                        <form action="" method="post" class="box">
                            <input type="hidden" name="pid" value="<?= $fetch_products['productID']; ?>">
                            <input type="hidden" name="name" value="<?= $fetch_products['productName']; ?>">
                            <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
                            <input type="hidden" name="image" value="<?= $fetch_products['image']; ?>">
                            <a href="quick_view.php?pid=<?= $fetch_products['productID']; ?>" class="fas fa-eye"></a>
                            <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
                            <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
                            <a href="category.php?category=<?= $fetch_products['category']; ?>"
                                class="cat"><?= $fetch_products['category']; ?></a>
                            <div class="name"><?= $fetch_products['productName']; ?></div>
                            <div class="flex">
                                <div class="price"><?= $fetch_products['price']; ?>k</div>
                                <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
                            </div>
                        </form>
                    <?php
                    }
                } else {
                    echo '<p class="empty">Không có sản phẩm nào khớp với tìm kiếm của bạn!</p>';
                }
            } else {
                // Hiển thị danh sách sản phẩm mặc định nếu không tìm kiếm
                $select_products = $conn->prepare("SELECT * FROM `products`");
                $select_products->execute();
                if ($select_products->rowCount() > 0) {
                    while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                        <form action="" method="post" class="box">
                            <input type="hidden" name="pid" value="<?= $fetch_products['productID']; ?>">
                            <input type="hidden" name="name" value="<?= $fetch_products['productName']; ?>">
                            <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
                            <input type="hidden" name="image" value="<?= $fetch_products['image']; ?>">
                            <a href="quick_view.php?pid=<?= $fetch_products['productID']; ?>" class="fas fa-eye"></a>
                            <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
                            <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
                            <a href="category.php?category=<?= $fetch_products['category']; ?>"
                                class="cat"><?= $fetch_products['category']; ?></a>
                            <div class="name"><?= $fetch_products['productName']; ?></div>
                            <div class="flex">
                                <div class="price"><?= $fetch_products['price']; ?>k</div>
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










    <!-- footer section starts  -->
    <?php include 'components/footer.php'; ?>
    <!-- footer section ends -->







    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>
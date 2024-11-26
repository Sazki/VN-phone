<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
    header('location:home.php');
};

// Lấy địa chỉ hiện tại của người dùng
$current_address = '';
$select_address = $conn->prepare("SELECT address FROM `users` WHERE userID = ?");
$select_address->execute([$user_id]);
if ($select_address->rowCount() > 0) {
    $fetch_address = $select_address->fetch(PDO::FETCH_ASSOC);
    $current_address = $fetch_address['address'];
}

if (isset($_POST['submit'])) {
    $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');

    $update_address = $conn->prepare("UPDATE `users` set address = ? WHERE userID = ?");
    $update_address->execute([$address, $user_id]);

    $message[] = 'đã lưu địa chỉ';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>update address</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php include 'components/user_header.php' ?>

    <section class="form-container" style="margin-top: 100px;">

        <form action="" method="post">
            <h3>địa chỉ của bạn</h3>
            <!-- <textarea name="address" id="address" class="box" placeholder="Nhập địa chỉ của bạn" rows="5"></textarea> -->
            <textarea name="address" id="address" class="box" placeholder="Nhập địa chỉ của bạn" maxlength="500"
                cols="30" rows="5"><?= htmlspecialchars($current_address, ENT_QUOTES, 'UTF-8'); ?></textarea>
            <input type="submit" value="lưu địa chỉ" name="submit" class="btn">
        </form>

    </section>










    <?php include 'components/footer.php' ?>







    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>
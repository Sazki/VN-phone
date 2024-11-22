<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
};

if (isset($_POST['submit'])) {

    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $number = htmlspecialchars($_POST['number'], ENT_QUOTES, 'UTF-8');
    $pass = htmlspecialchars($_POST['pass'], ENT_QUOTES, 'UTF-8');
    $cpass = htmlspecialchars($_POST['cpass'], ENT_QUOTES, 'UTF-8');

    // Kiểm tra người dùng đã tồn tại
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? OR phoneNumber = ?");
    $select_user->execute([$email, $number]);
    if ($select_user->rowCount() > 0) {
        $message[] = 'Email hoặc số điện thoại đã tồn tại!';
    } else {
        if ($pass !== $cpass) {
            $message[] = 'Mật khẩu xác nhận không khớp!';
        } else {
            // Mã hóa mật khẩu
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

            // Thêm người dùng với vai trò mặc định là 'client'
            $insert_user = $conn->prepare("INSERT INTO `users` (name, email, phoneNumber, password, role) VALUES (?, ?, ?, ?, ?)");
            $insert_user->execute([$name, $email, $number, $hashed_password, 'client']);

            // Lấy thông tin người dùng mới thêm
            $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
            $select_user->execute([$email]);
            $row = $select_user->fetch(PDO::FETCH_ASSOC);

            if ($select_user->rowCount() > 0) {
                $_SESSION['user_id'] = $row['userID'];
                header('location:home.php');
            }
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
    <title>register</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <section class="form-container">

        <form action="" method="post">
            <h3>Đăng ký tài khoản</h3>
            <input type="text" name="name" required placeholder="nhập tên của bạn" class="box" maxlength="50">
            <input type="email" name="email" required placeholder="nhập email của bạn" class="box" maxlength="50"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="number" name="number" required placeholder="nhập số điện thoại của bạn" class="box" min="0"
                max="9999999999" maxlength="10" pattern="^\d{10}$"
                title="Vui lòng nhập số điện thoại hợp lệ (chính xác 10 chữ số)">
            <input type="password" name="pass" required placeholder="nhập mật khẩu của bạn" class="box" maxlength="50"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="cpass" required placeholder="confirm your password" class="box" maxlength="50"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="Đăng ký" name="submit" class="btn">
            <p>Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
        </form>

    </section>











    <?php include 'components/footer.php'; ?>







    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>
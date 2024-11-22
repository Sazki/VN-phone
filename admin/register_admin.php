<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

// if (!isset($admin_id)) {
//     header('location:admin_login.php');
// }

if (isset($_POST['submit'])) {

    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $pass = htmlspecialchars($_POST['pass'], ENT_QUOTES, 'UTF-8');
    $cpass = htmlspecialchars($_POST['cpass'], ENT_QUOTES, 'UTF-8');

    $select_admin = $conn->prepare("SELECT * FROM `users` WHERE name = ?");
    $select_admin->execute([$name]);

    if ($select_admin->rowCount() > 0) {
        $message[] = 'tên người dùng đã tồn tại!';
    } else {
        if ($pass !== $cpass) {
            $message[] = 'xác nhận mật khẩu không khớp!';
        } else {
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
            $insert_admin = $conn->prepare("INSERT INTO `users`(name, email, password, role) VALUES(?,?,?)");
            $insert_admin->execute([$name, $email, $hashed_pass, 'admin']);
            $message[] = 'đã đăng ký quản trị viên mới!';
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
    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <?php include '../components/admin_header.php' ?>

    <!-- register admin section starts  -->

    <section class="form-container">

        <form action="" method="POST">
            <h3>đăng ký quản trị viên</h3>
            <input type="text" name="name" maxlength="20" required placeholder="nhập tên người dùng của bạn" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="email" name="email" maxlength="20" required placeholder="nhập email của bạn" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="pass" maxlength="20" required placeholder="nhập mật khẩu của bạn" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="cpass" maxlength="20" required placeholder="xác nhận mật khẩu của bạn"
                class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="đăng ký ngay" name="submit" class="btn">
        </form>

    </section>

    <!-- register admin section ends -->
















    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>
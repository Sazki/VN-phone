<?php

include '../components/connect.php';

session_start();

if (isset($_POST['submit'])) {

   $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');

   $pass = htmlspecialchars($_POST['pass'], ENT_QUOTES, 'UTF-8');

   // Lấy thông tin người dùng từ cơ sở dữ liệu chỉ dựa vào tên
   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);

   if ($select_user->rowCount() > 0) {
      $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

      // Kiểm tra role
      if ($fetch_user['role'] === 'admin') {
         // Kiểm tra mật khẩu
         if (password_verify($pass, $fetch_user['password'])) {
            $_SESSION['admin_id'] = $fetch_user['userID'];
            header('location:dashboard.php');
         } else {
            $message[] = 'Mật khẩu không đúng!';
         }
      } else {
         $message[] = 'Người dùng không phải là quản trị viên!';
      }
   } else {
      $message[] = 'Tài khoản quản trị viên không tồn tại!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <?php
   if (isset($message)) {
      foreach ($message as $message) {
         echo '
      <div class="message">
         <span>' . $message . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
      }
   }
   ?>

    <!-- admin login form section starts  -->

    <section class="form-container">

        <form action="" method="POST">
            <h3>đăng nhập quản trị viên</h3>
            <input type="email" name="email" maxlength="20" required placeholder="nhập email quản trị của bạn"
                class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="pass" maxlength="20" required placeholder="nhập mật khẩu của bạn" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="Đăng nhập ngay" name="submit" class="btn">
        </form>

    </section>

    <!-- admin login form section ends -->











</body>

</html>
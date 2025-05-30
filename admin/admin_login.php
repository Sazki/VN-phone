<?php

include '../components/connect.php';

session_start();

if (isset($_POST['submit'])) {

   $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
   $pass = htmlspecialchars($_POST['pass'], ENT_QUOTES, 'UTF-8');

   // Lấy thông tin người dùng từ cơ sở dữ liệu dựa vào email
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
    <title>Đăng nhập</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">


    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/style_login_admin.css" />
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

    <!-- admin login form section starts -->
    <section>
        <div class="form-box">
            <div class="form-value">
                <form action="" method="POST">
                    <h2>Đăng nhập Admin</h2>
                    <div class="inputbox">
                        <ion-icon name="mail-outline"></ion-icon>
                        <input type="email" name="email" maxlength="50" required placeholder="Nhập email của bạn" />
                    </div>
                    <div class="inputbox">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" name="pass" maxlength="50" required
                            placeholder="Nhập mật khẩu của bạn" />
                    </div>
                    <button type="submit" name="submit">Đăng nhập</button>
                </form>
            </div>
        </div>
    </section>
    <!-- admin login form section ends -->

    <script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
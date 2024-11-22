<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

if (isset($_POST['submit'])) {

   $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
   $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');

   if (!empty($name)) {
      $select_name = $conn->prepare("SELECT * FROM `users` WHERE name = ? OR email = ?");
      $select_name->execute([$name, $email]);
      if ($select_name->rowCount() > 0) {
         $message[] = 'Tên người dùng hoặc email đã được sử dụng!';
      } else {
         $update_user = $conn->prepare("UPDATE `users` SET name = ?, email = ? WHERE userID = ?");
         $update_user->execute([$name, $email, $admin_id]);

         $message[] = 'Thông tin người dùng đã được cập nhật!';
      }
   }

   $current_pass = htmlspecialchars($_POST['current_pass'], ENT_QUOTES, 'UTF-8');
   $new_pass = htmlspecialchars($_POST['new_pass'], ENT_QUOTES, 'UTF-8');
   $confirm_pass = htmlspecialchars($_POST['confirm_pass'], ENT_QUOTES, 'UTF-8');

   // Xử lý cập nhật mật khẩu
   if (!empty($current_pass)) {
      $select_current_pass = $conn->prepare("SELECT password FROM `users` WHERE userID = ?");
      $select_current_pass->execute([$admin_id]);
      $fetch_prev_pass = $select_current_pass->fetch(PDO::FETCH_ASSOC);
      $stored_pass = $fetch_prev_pass['password'];

      if (!password_verify($current_pass, $stored_pass)) {
         $message[] = 'Mật khẩu hiện tại không chính xác!';
      } elseif ($new_pass != $confirm_pass) {
         $message[] = 'Xác nhận mật khẩu không khớp!';
      } elseif (!empty($new_pass)) {

         // Hash mật khẩu mới
         $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);

         $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE userID = ?");
         $update_pass->execute([$hashed_new_pass, $admin_id]);
         $message[] = 'Cập nhật mật khẩu thành công!';
      } else {
         $message[] = 'Vui lòng nhập mật khẩu mới!';
      }
   } else {
      $message[] = 'Vui lòng nhập mật khẩu!';
   }
}




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>profile update</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <?php include '../components/admin_header.php' ?>

    <!-- admin profile update section starts  -->

    <section class="form-container">

        <form action="" method="POST">
            <h3>update profile</h3>
            <input type="text" name="name" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')" placeholder="<?= $fetch_profile['name']; ?>">
            <input type="email" name="email" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')" placeholder="<?= $fetch_profile['email']; ?>">
            <input type="password" name="current_pass" maxlength="20" placeholder="nhập mật khẩu hiện tại của bạn"
                class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="new_pass" maxlength="20" placeholder="nhập mật khẩu mới của bạn" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="confirm_pass" maxlength="20" placeholder="xác nhận mật khẩu mới của bạn"
                class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="cập nhật ngay" name="submit" class="btn">
        </form>

    </section>

    <!-- admin profile update section ends -->









    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>
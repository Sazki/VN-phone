<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:home.php');
   exit();
}

// Lấy thông tin profile để hiển thị
$fetch_profile = [
    'name' => '',
    'email' => '',
    'phoneNumber' => ''
];
$select_profile = $conn->prepare("SELECT name, email, phoneNumber FROM `users` WHERE userID = ?");
$select_profile->execute([$user_id]);
if($select_profile->rowCount() > 0){
    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
}

if (isset($_POST['submit'])) {

   $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
   $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
   $number = htmlspecialchars($_POST['number'], ENT_QUOTES, 'UTF-8');
   $old_pass = htmlspecialchars($_POST['old_pass'], ENT_QUOTES, 'UTF-8');
   $new_pass = htmlspecialchars($_POST['new_pass'], ENT_QUOTES, 'UTF-8');
   $confirm_pass = htmlspecialchars($_POST['confirm_pass'], ENT_QUOTES, 'UTF-8');
   $message = [];

   // Cập nhật thông tin người dùng
   if (!empty($name)) {
      $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE userID = ?");
      $update_name->execute([$name, $user_id]);
      $message[] = 'Cập nhật tên người dùng thành công';
   }

   if (!empty($email)) {
      $select_email = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND userID != ?");
      $select_email->execute([$email, $user_id]);
      if ($select_email->rowCount() > 0) {
         $message[] = 'Email đã được sử dụng!';
      } else {
         $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE userID = ?");
         $update_email->execute([$email, $user_id]);
         $message[] = 'Cập nhật email thành công';
      }
   }

   if (!empty($number)) {
      $select_number = $conn->prepare("SELECT * FROM `users` WHERE phoneNumber = ? AND userID != ?");
      $select_number->execute([$number, $user_id]);
      if ($select_number->rowCount() > 0) {
         $message[] = 'Số điện thoại đã được sử dụng!';
      } else {
         $update_number = $conn->prepare("UPDATE `users` SET phoneNumber = ? WHERE userID = ?");
         $update_number->execute([$number, $user_id]);
         $message[] = 'Cập nhật số điện thoại thành công';
      }
   }

   // Xử lý cập nhật mật khẩu
   if (!empty($old_pass) || !empty($new_pass) || !empty($confirm_pass)) {
      $select_prev_pass = $conn->prepare("SELECT password FROM `users` WHERE userID = ?");
      $select_prev_pass->execute([$user_id]);
      $fetch_prev_pass = $select_prev_pass->fetch(PDO::FETCH_ASSOC);
      $prev_pass = $fetch_prev_pass['password'];

      if (empty($old_pass)) {
         $message[] = 'Vui lòng nhập mật khẩu hiện tại!';
      } elseif (!password_verify($old_pass, $prev_pass)) {
         $message[] = 'Mật khẩu hiện tại không chính xác!';
      } elseif ($new_pass !== $confirm_pass) {
         $message[] = 'Xác nhận mật khẩu không khớp!';
      } elseif (empty($new_pass)) {
         $message[] = 'Vui lòng nhập mật khẩu mới!';
      } else {
         $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);
         $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE userID = ?");
         $update_pass->execute([$hashed_new_pass, $user_id]);
         $message[] = 'Cập nhật mật khẩu thành công!';
      }
   }

   // Sau khi cập nhật, chuyển về lại checkout.php và truyền thông báo (nếu muốn)
   header('Location: checkout.php?profile_updated=1');
   exit();
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật hồ sơ</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Google Fonts cho giao diện đẹp -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f3f4f8;}
        .update-form {
            min-height: 80vh; display: flex; align-items: center; justify-content: center;
        }
        .update-form form {
            background: #fff;
            padding: 36px 28px 25px 28px;
            border-radius: 14px;
            box-shadow: 0 6px 28px #2222a022;
            min-width: 350px; max-width: 420px; width: 100%;
            margin: 80px auto 0 auto;
            display: flex; flex-direction: column; align-items: center;
        }
        .update-form h3 {
            margin-bottom: 21px;
            font-size: 1.8rem;
            color: #232526;
            font-weight: 700;
            letter-spacing: 0.4px;
        }
        .update-form .box {
            width: 100%;
            padding: 13px 16px;
            border-radius: 8px;
            border: 1.5px solid #b9b9b9;
            margin-bottom: 15px;
            font-size: 1.07rem;
            transition: border 0.17s, box-shadow 0.18s;
        }
        .update-form .box:focus {
            border-color: #ff9800;
            box-shadow: 0 0 0 2px #ffe4b333;
            outline: none;
        }
        .update-form .btn {
            width: 100%;
            padding: 13px 0;
            border: none;
            border-radius: 8px;
            background: linear-gradient(90deg,#ff9800,#ffcc33);
            color: #222;
            font-size: 1.15rem;
            font-weight: 600;
            margin: 10px 0 0 0;
            box-shadow: 0 2px 10px #ffedb355;
            cursor: pointer;
            transition: background 0.18s, color 0.18s, transform 0.17s;
        }
        .update-form .btn:hover {
            background: linear-gradient(90deg,#36d1c4,#5b86e5);
            color: #fff;
            transform: scale(1.03);
        }
        @media (max-width: 500px) {
            .update-form form { min-width: unset; padding: 15px 5vw;}
        }
    </style>
</head>

<body>
    <?php include 'components/user_header.php'; ?>

    <section class="form-container update-form">
        <form action="" method="post">
            <h3>Cập nhật hồ sơ</h3>
            <input type="text" name="name" placeholder="<?= htmlspecialchars($fetch_profile['name']); ?>" class="box" maxlength="50">
            <input type="email" name="email" placeholder="<?= htmlspecialchars($fetch_profile['email']); ?>" class="box" maxlength="50"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="number" name="number" placeholder="<?= htmlspecialchars($fetch_profile['phoneNumber']); ?>" class="box" min="0"
                max="9999999999" maxlength="10">
            <input type="password" name="old_pass" placeholder="Nhập mật khẩu hiện tại" class="box" maxlength="50"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="new_pass" placeholder="Nhập mật khẩu mới" class="box" maxlength="50"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="confirm_pass" placeholder="Xác nhận mật khẩu mới của bạn" class="box"
                maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="Cập nhật ngay" name="submit" class="btn">
        </form>
    </section>

    <?php include 'components/footer.php'; ?>
    <script src="js/script.js"></script>
</body>

</html>

<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:home.php');
};

if (isset($_POST['update_avatar'])) {
   $avatar = $_FILES['avatar']['name'];
   $avatar_tmp_name = $_FILES['avatar']['tmp_name'];
   $avatar_size = $_FILES['avatar']['size'];
   $avatar_ext = pathinfo($avatar, PATHINFO_EXTENSION); // Lấy phần mở rộng của file
   $avatar_folder = 'uploaded_img/';
   $hashed_avatar = md5(uniqid(rand(), true)) . '.' . $avatar_ext; // Tạo tên file mã hóa
   $full_path = $avatar_folder . $hashed_avatar;

   if ($avatar_size > 2000000) {
      $message[] = 'Kích thước ảnh quá lớn!';
   } else {
      // Xóa ảnh cũ nếu không phải ảnh mặc định
      $select_old_avatar = $conn->prepare("SELECT avatar FROM `users` WHERE userID = ?");
      $select_old_avatar->execute([$user_id]);
      $fetch_old_avatar = $select_old_avatar->fetch(PDO::FETCH_ASSOC);

      if ($fetch_old_avatar['avatar'] != 'user-icon.png') {
         unlink($avatar_folder . $fetch_old_avatar['avatar']);
      }

      // Cập nhật ảnh mới
      move_uploaded_file($avatar_tmp_name, $full_path);
      $update_avatar = $conn->prepare("UPDATE `users` SET avatar = ? WHERE userID = ?");
      $update_avatar->execute([$hashed_avatar, $user_id]);

      $message[] = 'Ảnh đại diện đã được cập nhật!';
   }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>profile</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">


</head>

<body>

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <section class="user-details">

    <div class="user" style="margin-top:100px"> 
    <?php

         ?>
            <div class="avatar">
                <!-- Hiển thị ảnh đại diện -->
                <img src="uploaded_img/<?= $fetch_profile['avatar'] ? $fetch_profile['avatar'] : 'user-icon.png'; ?>"
                    alt="">
            </div>

            <!-- Form thay đổi ảnh đại diện -->
            <form action="" method="post" enctype="multipart/form-data" class="update-avatar-form">
                <input type="file" name="avatar" accept="image/*" required class="box">
                <button type="submit" name="update_avatar" class="btn">đổi avatar</button>
            </form>

            <p><i class="fas fa-user"></i><span><span><?= $fetch_profile['name']; ?></span></span></p>
            <p><i class="fas fa-phone"></i><span><?= $fetch_profile['phoneNumber']; ?></span></p>
            <p><i class="fas fa-envelope"></i><span><?= $fetch_profile['email']; ?></span></p>
            <a href="update_profile.php" class="btn">cập nhật hồ sơ</a>
            <p class="address"><i class="fas fa-map-marker-alt"></i><span><?php if ($fetch_profile['address'] == '') {
                                                                           echo 'vui lòng nhập địa chỉ của bạn';
                                                                        } else {
                                                                           echo $fetch_profile['address'];
                                                                        } ?></span>
            </p>
            <a href="update_address.php" class="btn">cập nhật địa chỉ</a>
        </div>

    </section>










    <?php include 'components/footer.php'; ?>







    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>
<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_admin = $conn->prepare("DELETE FROM `users` WHERE userID = ?");
   $delete_admin->execute([$delete_id]);
   header('location:admin_accounts.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tài khoản quản trị viên</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <?php include '../components/admin_header.php' ?>

    <!-- admins accounts section starts  -->

    <section class="accounts">

        <h1 class="heading">tài khoản quản trị viên</h1>

        <div class="box-container">

            <div class="box">
                <p>đăng ký quản trị viên mới</p>
                <a href="register_admin.php" class="option-btn">đăng ký</a>
            </div>

            <?php
      $select_account = $conn->prepare("SELECT * FROM `users` WHERE role = ?");
      $select_account->execute(['admin']);
      if($select_account->rowCount() > 0){
         while($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)){  
   ?>
            <div class="box">
                <p> admin id : <span><?= $fetch_accounts['userID']; ?></span> </p>
                <p> username : <span><?= $fetch_accounts['name']; ?></span> </p>
                <div class="flex-btn">
                    <a href="admin_accounts.php?delete=<?= $fetch_accounts['userID']; ?>" class="delete-btn"
                        onclick="return confirm('delete this account?');">delete</a>
                    <?php
            if($fetch_accounts['userID'] == $admin_id){
               echo '<a href="update_profile.php" class="option-btn">update</a>';
            }
         ?>
                </div>
            </div>
            <?php
      }
   }else{
      echo '<p class="empty">no accounts available</p>';
   }
   ?>

        </div>

    </section>

    <!-- admins accounts section ends -->




















    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>
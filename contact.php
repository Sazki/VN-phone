<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
};

if (isset($_POST['send'])) {

   $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
   $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
   $number = htmlspecialchars($_POST['number'], ENT_QUOTES, 'UTF-8');
   $msg = htmlspecialchars($_POST['msg'], ENT_QUOTES, 'UTF-8');

   $select_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND phoneNumber = ? AND message = ?");
   $select_message->execute([$name, $email, $number, $msg]);

   if ($select_message->rowCount() > 0) {
      $message[] = 'đã gửi tin nhắn rồi!';
   } else {

      $insert_message = $conn->prepare("INSERT INTO `messages`(userID, name, email, phoneNumber, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$user_id, $name, $email, $number, $msg]);

      $message[] = 'đã gửi tin nhắn thành công!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>liên hệ</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <!-- header section starts  -->
    <?php include 'components/user_header.php'; ?>
    <!-- header section ends -->

    <div class="heading">
        <h3>liên hệ với chúng tôi</h3>
        <p><a href="home.php">trang chủ</a> <span> / liên hệ</span></p>
    </div>

    <!-- contact section starts  -->

    <section class="contact">

        <div class="row">

            <div class="image">
                <img src="images/contact-img.svg" alt="">
            </div>

            <form action="" method="post">
                <h3>hãy nói cho chúng tôi biết điều gì đó!</h3>
                <input type="text" name="name" maxlength="50" class="box" placeholder="nhập tên của bạn" required>
                <input type="number" name="number" min="0" max="9999999999" class="box" placeholder="nhập số của bạn"
                    required maxlength="10">
                <input type="email" name="email" maxlength="50" class="box" placeholder="nhập email của bạn" required>
                <textarea name="msg" class="box" required placeholder="nhập tin nhắn của bạn" maxlength="500" cols="30"
                    rows="10"></textarea>
                <input type="submit" value="gửi tin nhắn" name="send" class="btn">
            </form>

        </div>

    </section>

    <!-- contact section ends -->










    <!-- footer section starts  -->
    <?php include 'components/footer.php'; ?>
    <!-- footer section ends -->








    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>
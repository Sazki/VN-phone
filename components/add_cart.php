<?php

if(isset($_POST['add_to_cart'])){

   if($user_id == ''){
      header('location:login.php');
   }else{

      $pid = htmlspecialchars($_POST['pid'], ENT_QUOTES, 'UTF-8');
      $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
      $price = htmlspecialchars($_POST['price'], ENT_QUOTES, 'UTF-8');
      $image = htmlspecialchars($_POST['image'], ENT_QUOTES, 'UTF-8');
      $qty = htmlspecialchars($_POST['qty'], ENT_QUOTES, 'UTF-8');


      $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE cartName = ? AND userID = ?");
      $check_cart_numbers->execute([$name, $user_id]);

      if($check_cart_numbers->rowCount() > 0){
         $message[] = 'already added to cart!';
      }else{
         $insert_cart = $conn->prepare("INSERT INTO `cart`(userID, productID, cartName, price, quantity, image) VALUES(?,?,?,?,?,?)");
         $insert_cart->execute([$user_id, $pid, $name, $price, $qty, $image]);
         $message[] = 'đã thêm vào giỏ hàng!';
         
      }

   }

}

?>
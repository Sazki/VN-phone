<?php
include 'components/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $discount_code = $_POST['discount_code'] ?? '';
   $grand_total = $_POST['grand_total'] ?? 0;
   $response = ['success' => false, 'message' => ''];

   if ($discount_code) {
      $check_discount = $conn->prepare("SELECT * FROM `discounts` WHERE code = ? AND status = 'còn hạn'");
      $check_discount->execute([$discount_code]);

      if ($check_discount->rowCount() > 0) {
         $fetch_discount = $check_discount->fetch(PDO::FETCH_ASSOC);
         $discount_percent = $fetch_discount['discount_percent'];
         $total_after_discount = $grand_total - ($grand_total * $discount_percent / 100);

         $response['success'] = true;
         $response['message'] = 'Mã giảm giá hợp lệ, bạn được giảm ' . $discount_percent . '%';
         $response['total_after_discount'] = $total_after_discount;
      } else {
         $response['message'] = 'Mã giảm giá không hợp lệ hoặc đã hết hạn';
      }
   } else {
      $response['message'] = 'Vui lòng nhập mã giảm giá';
   }

   echo json_encode($response);
}
<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>About VN-Phone</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<!-- about section starts  -->

<section class="about">

   <div class="row" style="margin-top:100px">

      <div class="image">
         <img src="images/about-phone.svg" alt="About VN-Phone">
         <!-- Bạn nên thay bằng hình minh họa điện thoại: about-phone.svg/png -->
      </div>

      <div class="content">
         <h3>Tại sao nên chọn VN-Phone?</h3>
         <p>
         VN-Phone tự hào là nhà phân phối điện thoại chính hãng uy tín hàng đầu tại Việt Nam. Chúng tôi cung cấp các dòng điện thoại mới nhất từ Apple, Samsung, Xiaomi, Oppo,... với giá cả cạnh tranh, bảo hành chính hãng và dịch vụ hậu mãi tận tâm. Đội ngũ nhân viên chuyên nghiệp sẽ tư vấn cho bạn chọn lựa sản phẩm phù hợp nhất với nhu cầu và ngân sách.
         </p>
         <a href="menu.php" class="btn">Xem sản phẩm</a>
      </div>

   </div>

</section>

<!-- about section ends -->

<!-- steps section starts  -->

<section class="steps">

   <h1 class="title">Quy trình mua hàng</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/step-1.png" alt="Chọn sản phẩm">
         <h3>Chọn sản phẩm yêu thích</h3>
         <p>Khách hàng dễ dàng lựa chọn các mẫu điện thoại đa dạng, cập nhật liên tục các dòng mới nhất trên thị trường.</p>
      </div>

      <div class="box">
         <img src="images/step-2.png" alt="Đặt hàng và thanh toán">
         <h3>Đặt hàng & Thanh toán tiện lợi</h3>
         <p>Đặt hàng trực tuyến nhanh chóng, hỗ trợ nhiều hình thức thanh toán an toàn.</p>
      </div>

      <div class="box">
         <img src="images/step-4.png" alt="Nhận hàng & hỗ trợ">
         <h3>Giao hàng tận nơi - Hỗ trợ nhiệt tình</h3>
         <p>Giao hàng toàn quốc, hỗ trợ bảo hành chính hãng và chăm sóc khách hàng tận tâm.</p>
      </div>

   </div>

</section>

<!-- steps section ends -->

<!-- reviews section starts  -->

<section class="reviews">

   <h1 class="title">Phản hồi khách hàng</h1>

   <div class="swiper reviews-slider">

      <div class="swiper-wrapper">

         <div class="swiper-slide slide">
            <img src="images/pic-1.png" alt="Customer 1">
            <p>Mình đã mua iPhone 15 Pro Max tại VN-Phone, máy chính hãng, giá tốt hơn so với thị trường. Sẽ tiếp tục ủng hộ shop!</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>Nguyễn Văn A</h3>
         </div>

         <div class="swiper-slide slide">
            <img src="images/pic-2.png" alt="Customer 2">
            <p>Shop tư vấn rất tận tâm, mình mua Samsung S24 Ultra còn được tặng thêm quà, cảm ơn VN-Phone!</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
            </div>
            <h3>Trần Thị B</h3>
         </div>

         <div class="swiper-slide slide">
            <img src="images/pic-3.png" alt="Customer 3">
            <p>Giao hàng nhanh, máy còn nguyên seal, có đầy đủ bảo hành điện tử. Sẽ giới thiệu cho bạn bè.</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
            </div>
            <h3>Hoàng Minh C</h3>
         </div>

      </div>

      <div class="swiper-pagination"></div>

   </div>

</section>

<!-- reviews section ends -->

<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".reviews-slider", {
   loop:true,
   grabCursor: true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      0: {
      slidesPerView: 1,
      },
      700: {
      slidesPerView: 2,
      },
      1024: {
      slidesPerView: 3,
      },
   },
});

</script>

</body>
</html>

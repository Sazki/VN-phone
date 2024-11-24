<footer>
    <div class="content">
        <div class="top">
            <div class="logo">
                <h1 class="logo-text"><span>VN</span>Food</h1>
            </div>
            <div class="media-icons">
                <a href="https://www.facebook.com/viet.nguyenhuu.750983"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#"><i class="fa-brands fa-twitter"></i></a>
                <a href="https://www.instagram.com/huuviet119/"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                <a href="https://www.youtube.com/@VietHuu-ob3qg"><i class="fa-brands fa-youtube"></i></a>
            </div>
        </div>

        <div class="link-boxes">
            <ul class="box">
                <li class="link-name">Về VNFood</li>
                <li><a href="#">Giới thiệu</a></li>
                <li><a href="#">Cửa hàng</a></li>
                <li><a href="#">Tin tức</a></li>
                <li><a href="#">Tuyển dụng</a></li>
            </ul>
            <ul class="box">
                <li class="link-name">Hỗ trợ khách hàng</li>
                <li><a href="#">Trung tâm trợ giúp</a></li>
                <li><a href="#">Liên hệ</a></li>
                <li><a href="#">Chính sách đổi trả</a></li>
                <li><a href="#">Câu hỏi thường gặp</a></li>
            </ul>
            <ul class="box">
                <li class="link-name">Sản phẩm</li>
                <li><a href="#">Burgers</a></li>
                <li><a href="#">Pizza</a></li>
                <li><a href="#">Đồ uống</a></li>
                <li><a href="#">Combo ưu đãi</a></li>
            </ul>
            <ul class="box input-box">
                <li class="link-name">Đăng ký nhận tin</li>
                <li>
                    <input type="email" placeholder="Nhập email của bạn" required />
                </li>
                <li><input type="button" value="Gửi" /></li>
            </ul>
        </div>
    </div>
    <div class="bottom-details">
        <div class="bottom-text">
            <span class="copyright-text">
                Copyright &#169; 2024 <a href="#">VN-Food</a>. All rights reserved.
            </span>
            <span class="policy-terms">
                <a href="#">Chính sách bảo mật</a>
                <a href="#">Điều khoản sử dụng</a>
            </span>
        </div>
    </div>
</footer>
<style>
    footer {
    position: relative;
    background: linear-gradient(90deg, #ff7e5f, #feb47b);
    width: 100%;
    color: white;
    font-family: 'Poppins', sans-serif;
}

footer .content {
    max-width: 1250px;
    margin: auto;
    padding: 30px 40px;
}

footer .content .top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 30px;
}

footer .content .top .logo .logo-text {
    font-size: 24px;
    font-weight: bold;
    color: #fff;
    letter-spacing: 1px;
}

footer .content .top .logo .logo-text span {
    color: #004042;
}

footer .content .top .media-icons {
    display: flex;
}

footer .content .top .media-icons a {
    height: 40px;
    width: 40px;
    /* background: #004042; */
    margin: 0 8px;
    border-radius: 50%;
    text-align: center;
    line-height: 40px;
    color: #fff;
    font-size: 17px;
    transition: all 0.3s ease;
}

footer .content .top .media-icons a:hover {
    background: white;
    color: #004042;
}

footer .content .link-boxes {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

footer .content .link-boxes .box {
    flex: 1;
    min-width: 200px;
    margin: 10px;
}

footer .content .link-boxes .box .link-name {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
}

footer .content .link-boxes .box li {
    list-style: none;
    margin: 6px 0;
}

footer .content .link-boxes .box li a {
    color: white;
    text-decoration: none;
    font-size: 14px;
    opacity: 0.8;
}

footer .content .link-boxes .box li a:hover {
    text-decoration: underline;
    opacity: 1;
}

footer .content .link-boxes .input-box input[type="email"] {
    width: calc(100% - 10px);
    padding: 10px;
    border: none;
    border-radius: 4px;
    outline: none;
    font-size: 14px;
}

footer .content .link-boxes .input-box input[type="button"] {
    padding: 10px;
    margin-top: 5px;
    background: white;
    color: #004042;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
}

footer .content .link-boxes .input-box input[type="button"]:hover {
    background: #004042;
    color: white;
}

footer .bottom-details {
    background: linear-gradient(90deg, #fa4417, #f98933);
    padding: 10px 0;
}

footer .bottom-details .bottom-text {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1250px;
    margin: auto;
}

footer .bottom-details .bottom-text span,
footer .bottom-details .bottom-text a {
    font-size: 14px;
    color: white;
    text-decoration: none;
    opacity: 0.8;
}

footer .bottom-details .bottom-text a:hover {
    text-decoration: underline;
    opacity: 1;
}

</style>
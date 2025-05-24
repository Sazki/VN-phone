<footer style="font-family:'Poppins',sans-serif;">
    <style>
        footer {
    background: linear-gradient(90deg, #232526 0%, #434343 100%);
    color: #fff;
    padding: 0;
}
.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 48px 24px 20px 24px;
}
.footer-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 30px;
}
.footer-logo {
    display: flex;
    align-items: center;
    font-size: 2.1rem;
    font-weight: 700;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 2px;
}
.footer-logo i {
    color: #ff9800;
    font-size: 2.2rem;
    margin-right: 10px;
}
.footer-socials {
    display: flex;
    align-items: center;
    gap: 15px;
}
.footer-socials a {
    color: #fff;
    font-size: 2rem;
    transition: color 0.18s, transform 0.18s;
}
.footer-socials a:hover {
    color: #ff9800;
    transform: translateY(-2px) scale(1.15);
}
.footer-links {
    display: flex;
    flex-wrap: wrap;
    gap: 36px;
    margin: 38px 0 18px 0;
    width: 100%;
    justify-content: center;
}
.footer-links ul {
    list-style: none;
    padding: 0;
    min-width: 185px;
    max-width: 250px;
    background: rgba(255,255,255,0.01);
}
.footer-links .link-name {
    font-weight: 600;
    font-size: 2rem;
    margin-bottom: 12px;
    color: #ff9800;
}
.footer-links li a {
    color: #fff;
    font-size: 1rem;
    text-decoration: none;
    display: block;
    margin-bottom: 9px;
    transition: color 0.19s;
}
.footer-links li a:hover {
    color: #ff9800;
}
.footer-links .input-box input[type="email"] {
    width: 100%;
    padding: 8px 10px;
    border-radius: 6px;
    border: none;
    margin-bottom: 7px;
    font-size: 1rem;
    box-sizing: border-box;
}
.footer-links .input-box input[type="button"] {
    width: 100%;
    padding: 8px 10px;
    border-radius: 6px;
    border: none;
    background: #ff9800;
    color: #fff;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.19s;
}
.footer-links .input-box input[type="button"]:hover {
    background: #d97706;
}
.footer-bottom {
    border-top: 1.5px solid #454545;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 13px 0 7px 0;
    font-size: 1rem;
    color: #e2e2e2;
    flex-wrap: wrap;
    margin-top: 10px;
}
.footer-bottom a {
    color: #ff9800;
    margin-left: 16px;
    text-decoration: none;
    font-size: 1rem;
}
.footer-bottom a:hover {
    text-decoration: underline;
    color: #ffd46b;
}

/* -------- Responsive Fix -------- */
@media (max-width: 900px) {
    .footer-container {
        padding: 32px 6vw 15px 6vw;
    }
    .footer-top {
        flex-direction: column;
        align-items: center;
        gap: 16px;
        text-align: center;
    }
    .footer-socials {
        justify-content: center;
        width: 100%;
    }
    .footer-links {
        flex-direction: column;
        gap: 16px;
        align-items: center;
        margin: 26px 0 12px 0;
    }
    .footer-links ul {
        min-width: 60vw;
        max-width: 98vw;
        text-align: center;
        margin-bottom: 10px;
    }
    .footer-links .link-name {
        font-size: 1.28rem;
    }
    .footer-bottom {
        flex-direction: column;
        align-items: center;
        gap: 5px;
        font-size: 0.96rem;
        text-align: center;
    }
    .footer-bottom .policy-terms {
        margin-left: 0;
    }
}
@media (max-width: 540px) {
    .footer-container {
        padding: 22px 2vw 10px 2vw;
    }
    .footer-logo { font-size: 1.17rem; }
    .footer-links ul {
        min-width: 93vw;
        max-width: 99vw;
        padding: 0 2vw;
    }
    .footer-links .link-name { font-size: 1.05rem; }
}
@media (max-width: 650px) {
    .footer-logo,
    .footer-links,
    .footer-bottom {
        display: none !important;
    }
    .footer-socials {
        justify-content: center;
        width: 100%;
        margin: 0 auto;
        padding: 24px 0 12px 0;
        gap: 28px;
    }
    .footer-container {
        padding: 0 !important;
        max-width: 100vw;
    }
    .footer-socials a {
        font-size: 2.5rem;
    }
    footer {
        padding: 0 !important;
    }
}

    </style>
    <div class="footer-container">
        <div class="footer-top">
            <div class="footer-logo">
                <i class="fas fa-mobile-alt"></i>
                <span style="color:#ff9800;">VN</span>-Phone
            </div>
            <div class="footer-socials">
                <a href="https://www.facebook.com/whynotpnam/" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="https://www.instagram.com/whynot_pnam/" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                <a href="https://www.youtube.com" target="_blank"><i class="fab fa-youtube"></i></a>
            </div>
        </div>

        <div class="footer-links">
            <ul>
                <li class="link-name">Về VN-Phone</li>
                <li><a href="#">Giới thiệu</a></li>
                <li><a href="#">Hệ thống cửa hàng</a></li>
                <li><a href="#">Tin công nghệ</a></li>
                <li><a href="#">Tuyển dụng</a></li>
            </ul>
            <ul>
                <li class="link-name">Hỗ trợ khách hàng</li>
                <li><a href="#">Trung tâm bảo hành</a></li>
                <li><a href="#">Liên hệ</a></li>
                <li><a href="#">Chính sách đổi trả</a></li>
                <li><a href="#">Câu hỏi thường gặp</a></li>
            </ul>
            <ul>
                <li class="link-name">Danh mục sản phẩm</li>
                <li><a href="#">iPhone</a></li>
                <li><a href="#">Samsung</a></li>
                <li><a href="#">Xiaomi</a></li>
                <li><a href="#">Oppo</a></li>
            </ul>
            <ul class="input-box">
                <li class="link-name">Đăng ký nhận tin</li>
                <li>
                    <input type="email" placeholder="Nhập email của bạn" required />
                </li>
                <li><input type="button" value="Gửi" /></li>
            </ul>
        </div>
        <div class="footer-bottom">
            <span class="copyright-text">
                Copyright &#169; 2024 <a href="#">VN-Phone</a>. All rights reserved.
            </span>
            <span class="policy-terms">
                <a href="#">Chính sách bảo mật</a>
                <a href="#">Điều khoản sử dụng</a>
            </span>
        </div>
    </div>
    <!-- FontAwesome CDN nếu chưa có trên toàn site -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Google Fonts nếu chưa có trên toàn site -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</footer>

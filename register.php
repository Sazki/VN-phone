<?php
include 'components/connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (isset($_POST['submit'])) {
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $number = htmlspecialchars($_POST['number'], ENT_QUOTES, 'UTF-8');
    $address_detail = htmlspecialchars($_POST['address_detail'], ENT_QUOTES, 'UTF-8');
    $province = htmlspecialchars($_POST['province'], ENT_QUOTES, 'UTF-8');
    $district = htmlspecialchars($_POST['district'], ENT_QUOTES, 'UTF-8');
    $ward = htmlspecialchars($_POST['ward'], ENT_QUOTES, 'UTF-8');
    $pass = htmlspecialchars($_POST['pass'], ENT_QUOTES, 'UTF-8');
    $cpass = htmlspecialchars($_POST['cpass'], ENT_QUOTES, 'UTF-8');

    // Ghép địa chỉ đầy đủ
    $address = $address_detail;
    if ($ward != '') $address .= ', ' . $ward;
    if ($district != '') $address .= ', ' . $district;
    if ($province != '') $address .= ', ' . $province;

    $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? OR phoneNumber = ?");
    $select_user->execute([$email, $number]);
    if ($select_user->rowCount() > 0) {
        $message[] = 'Email hoặc số điện thoại đã tồn tại!';
    } else {
        if ($pass !== $cpass) {
            $message[] = 'Mật khẩu xác nhận không khớp!';
        } else {
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
            $insert_user = $conn->prepare("INSERT INTO `users` (name, email, phoneNumber, password, role, address) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_user->execute([$name, $email, $number, $hashed_password, 'client', $address]);

            $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
            $select_user->execute([$email]);
            $row = $select_user->fetch(PDO::FETCH_ASSOC);

            if ($select_user->rowCount() > 0) {
                $_SESSION['user_id'] = $row['userID'];
                header('location:home.php');
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f3f4f8; }
        .form-container { display: flex; align-items: center; justify-content: center; min-height: 85vh; }
        .form-container form {
            background: #fff;
            padding: 40px 30px 28px 30px;
            border-radius: 14px;
            box-shadow: 0 6px 28px #2222a022;
            min-width: 350px;
            max-width: 480px;
            width: 100%;
            margin: 80px auto 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .form-container h3 {margin-bottom:22px;font-size:2rem;color:#232526;font-weight:700;}
        .form-container .box, .form-container select {
            width: 100%; padding: 13px 16px; border-radius: 8px;
            border: 1.5px solid #b9b9b9; margin-bottom: 13px;
            font-size: 1.1rem; transition: border 0.17s, box-shadow 0.18s;
        }
        .form-container .box:focus, .form-container select:focus {
            border-color: #ff9800; box-shadow: 0 0 0 2px #ffe4b333; outline: none;
        }
        .form-container .btn {
            width: 100%; padding: 13px 0; border: none; border-radius: 8px;
            background: linear-gradient(90deg,#ff9800,#ffcc33);
            color: #222;
            font-size: 1.45rem; font-weight: 600;
            margin: 9px 0 10px 0;
            box-shadow: 0 2px 10px #ffedb355;
            cursor: pointer;
            transition: background 0.19s, color 0.18s, transform 0.19s;
        }
        .form-container .btn:hover {
            background: linear-gradient(90deg,#36d1c4,#5b86e5);
            color: #fff;
            transform: scale(1.03);
        }
        .form-container p {margin-top: 7px; font-size: 1.02rem;}
        .form-container p a {color: #ff9800; font-weight: 600; text-decoration: none;}
        .form-container p a:hover {color: #232526; text-decoration: underline;}
        .message {
            background: #fffae6; color: #b85c00; padding: 12px 20px;
            border-radius: 8px; font-size: 1rem; margin: 14px 0; display: flex; align-items: center;
            box-shadow: 0 2px 10px #ffedb355;
        }
        .message i { margin-left: 10px; cursor: pointer; font-size: 1.05rem; }
        @media (max-width: 500px) {
            .form-container form { min-width: unset; padding: 14px 3vw;}
        }
    </style>
</head>

<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container" style="margin-top: 30px;">
    <form action="" method="post" autocomplete="off">
        <h3>Đăng ký tài khoản</h3>
        <?php
        if (isset($message)) {
            foreach ($message as $msg) {
                echo '
                <div class="message">
                    <span>' . $msg . '</span>
                    <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
                </div>
                ';
            }
        }
        ?>
        <input type="text" name="name" required placeholder="Nhập tên của bạn" class="box" maxlength="50">
        <input type="email" name="email" required placeholder="Nhập email của bạn" class="box" maxlength="50"
            oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="number" name="number" required placeholder="Nhập số điện thoại của bạn" class="box"
            min="0" max="9999999999" maxlength="10" pattern="^\d{10}$"
            title="Vui lòng nhập số điện thoại hợp lệ (10 số)">
        <!-- Địa chỉ dạng chọn -->
        <select name="province" id="province" required>
            <option value="">Chọn Tỉnh/Thành phố</option>
        </select>
        <select name="district" id="district" required>
            <option value="">Chọn Quận/Huyện</option>
        </select>
        <select name="ward" id="ward" required>
            <option value="">Chọn Phường/Xã</option>
        </select>
        <input type="text" name="address_detail" required placeholder="Địa chỉ cụ thể: Số nhà, tên đường..." class="box" maxlength="200">
        <input type="password" name="pass" required placeholder="Nhập mật khẩu" class="box" maxlength="50"
            oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="cpass" required placeholder="Xác nhận mật khẩu" class="box" maxlength="50"
            oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="submit" value="Đăng ký" name="submit" class="btn">
        <p>Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
    </form>
</section>

<?php include 'components/footer.php'; ?>
<!-- Đọc dữ liệu tỉnh/huyện/xã từ file JSON nội bộ (js/location-vn.json) -->
<script>
let locationData = [];
function loadProvinces() {
    const provinceSelect = document.getElementById('province');
    provinceSelect.innerHTML = '<option value="">Chọn Tỉnh/Thành phố</option>';
    locationData.forEach(p => {
        provinceSelect.innerHTML += `<option value="${p.name}">${p.name}</option>`;
    });
}
function loadDistricts(provinceName) {
    const districtSelect = document.getElementById('district');
    districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
    if (!provinceName) return;
    const province = locationData.find(p=>p.name==provinceName);
    if (!province) return;
    province.districts.forEach(d => {
        districtSelect.innerHTML += `<option value="${d.name}">${d.name}</option>`;
    });
}
function loadWards(provinceName, districtName) {
    const wardSelect = document.getElementById('ward');
    wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
    if (!provinceName || !districtName) return;
    const province = locationData.find(p=>p.name==provinceName);
    if (!province) return;
    const district = province.districts.find(d=>d.name==districtName);
    if (!district) return;
    district.wards.forEach(w => {
        wardSelect.innerHTML += `<option value="${w.name}">${w.name}</option>`;
    });
}
document.addEventListener('DOMContentLoaded', function() {
    fetch('js/location-vn.json')
        .then(res => res.json())
        .then(data => {
            locationData = data;
            loadProvinces();
            document.getElementById('province').addEventListener('change', function() {
                loadDistricts(this.value);
                document.getElementById('ward').innerHTML = '<option value="">Chọn Phường/Xã</option>';
            });
            document.getElementById('district').addEventListener('change', function() {
                const province = document.getElementById('province').value;
                loadWards(province, this.value);
            });
        });
});
</script>
<script src="js/script.js"></script>
</body>
</html>

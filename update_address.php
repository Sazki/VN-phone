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

// Tách địa chỉ thành phần nếu có
$current_address = '';
$address_detail = '';
$province = '';
$district = '';
$ward = '';

$select_address = $conn->prepare("SELECT address FROM `users` WHERE userID = ?");
$select_address->execute([$user_id]);
if ($select_address->rowCount() > 0) {
    $fetch_address = $select_address->fetch(PDO::FETCH_ASSOC);
    $current_address = $fetch_address['address'];
    // Tách các phần nếu có địa chỉ lưu theo dạng cũ (format: chi tiết, phường, quận, tỉnh)
    $address_parts = array_map('trim', explode(',', $current_address));
    $count = count($address_parts);
    if ($count > 0) $address_detail = $address_parts[0];
    if ($count > 1) $ward = $address_parts[1];
    if ($count > 2) $district = $address_parts[2];
    if ($count > 3) $province = $address_parts[3];
}

if (isset($_POST['submit'])) {
    $address_detail = htmlspecialchars($_POST['address_detail'], ENT_QUOTES, 'UTF-8');
    $province = htmlspecialchars($_POST['province'], ENT_QUOTES, 'UTF-8');
    $district = htmlspecialchars($_POST['district'], ENT_QUOTES, 'UTF-8');
    $ward = htmlspecialchars($_POST['ward'], ENT_QUOTES, 'UTF-8');
    // Ghép địa chỉ chuẩn Việt Nam
    $full_address = $address_detail;
    if ($ward != '') $full_address .= ', ' . $ward;
    if ($district != '') $full_address .= ', ' . $district;
    if ($province != '') $full_address .= ', ' . $province;

    $update_address = $conn->prepare("UPDATE `users` set address = ? WHERE userID = ?");
    $update_address->execute([$full_address, $user_id]);
    header('Location: checkout.php?address_updated=1');
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật địa chỉ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f3f4f8;}
        .form-container { min-height: 78vh; display: flex; align-items: center; justify-content: center;}
        .form-container form {
            background: #fff;
            padding: 38px 32px 28px 32px;
            border-radius: 14px;
            box-shadow: 0 6px 28px #2222a022;
            min-width: 350px; max-width: 480px; width: 100%;
            margin: 80px auto 0 auto;
            display: flex; flex-direction: column; align-items: stretch;
        }
        .form-container h3 {
            margin-bottom: 22px;
            font-size: 1.8rem;
            text-align: center;
            color: #2b313a;
            font-weight: 700;
            letter-spacing: 0.2px;
        }
        .form-container label {
            font-size: 1.11rem;
            font-weight: 600;
            margin-bottom: 7px;
            color: #222; letter-spacing: 0.15px;
        }
        .form-container .box, .form-container select {
            width: 100%;
            padding: 16px 16px;
            border-radius: 8px;
            border: 1.7px solid #b9b9b9;
            margin-bottom: 19px;
            font-size: 1.15rem;
            transition: border 0.18s, box-shadow 0.16s;
            background: #f8fafc;
        }
        .form-container .box:focus, .form-container select:focus {
            border-color: #ff9800;
            box-shadow: 0 0 0 2px #ffe4b355;
            outline: none;
            background: #fffbe7;
        }
        .form-container .btn {
            width: 100%; padding: 16px 0; border: none;
            border-radius: 8px;
            background: linear-gradient(90deg,#ff9800,#ffcc33);
            color: #222;
            font-size: 1.21rem; font-weight: 700;
            box-shadow: 0 2px 10px #ffedb355;
            cursor: pointer;
            transition: background 0.19s, color 0.16s, transform 0.16s;
            margin-top: 7px;
            letter-spacing: 0.2px;
        }
        .form-container .btn:hover {
            background: linear-gradient(90deg,#36d1c4,#5b86e5); color: #fff;
            transform: scale(1.02);
        }
        @media (max-width: 540px) {
            .form-container form { min-width: unset; max-width: 99vw; padding: 13px 2vw;}
            .form-container h3 { font-size: 1.23rem;}
        }
    </style>
</head>
<body>

<?php include 'components/user_header.php' ?>

<section class="form-container" style="margin-top: 80px;">
    <form action="" method="post" id="address-form" autocomplete="off">
        <h3>Cập nhật địa chỉ</h3>
        <label for="province">Tỉnh/Thành phố <span style="color:#ff9800">*</span></label>
        <select name="province" id="province" required>
            <option value="">Chọn tỉnh/thành phố</option>
        </select>
        <label for="district">Quận/Huyện <span style="color:#ff9800">*</span></label>
        <select name="district" id="district" required>
            <option value="">Chọn quận/huyện</option>
        </select>
        <label for="ward">Phường/Xã <span style="color:#ff9800">*</span></label>
        <select name="ward" id="ward" required>
            <option value="">Chọn phường/xã</option>
        </select>
        <label for="address_detail">Địa chỉ cụ thể <span style="color:#ff9800">*</span></label>
        <input type="text" name="address_detail" id="address_detail" class="box" maxlength="200" placeholder="Số nhà, tên đường..." value="<?= htmlspecialchars($address_detail, ENT_QUOTES, 'UTF-8'); ?>" required>
        <input type="submit" value="Lưu địa chỉ" name="submit" class="btn">
    </form>
</section>

<?php include 'components/footer.php' ?>

<!-- Đọc dữ liệu tỉnh/huyện/xã từ file JSON nội bộ (js/location-vn.json) -->
<script>
let locationData = [];
function loadProvinces(selectedProvince = '') {
    const provinceSelect = document.getElementById('province');
    provinceSelect.innerHTML = '<option value="">Chọn tỉnh/thành phố</option>';
    locationData.forEach(p => {
        provinceSelect.innerHTML += `<option value="${p.name}" ${p.name==selectedProvince?'selected':''}>${p.name}</option>`;
    });
}
function loadDistricts(selectedProvince, selectedDistrict = '') {
    const districtSelect = document.getElementById('district');
    districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
    if (!selectedProvince) return;
    const province = locationData.find(p=>p.name==selectedProvince);
    if (!province) return;
    province.districts.forEach(d => {
        districtSelect.innerHTML += `<option value="${d.name}" ${d.name==selectedDistrict?'selected':''}>${d.name}</option>`;
    });
}
function loadWards(selectedProvince, selectedDistrict, selectedWard = '') {
    const wardSelect = document.getElementById('ward');
    wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
    if (!selectedProvince || !selectedDistrict) return;
    const province = locationData.find(p=>p.name==selectedProvince);
    if (!province) return;
    const district = province.districts.find(d=>d.name==selectedDistrict);
    if (!district) return;
    district.wards.forEach(w => {
        wardSelect.innerHTML += `<option value="${w.name}" ${w.name==selectedWard?'selected':''}>${w.name}</option>`;
    });
}
document.addEventListener('DOMContentLoaded', function() {
    fetch('js/location-vn.json')
        .then(res => res.json())
        .then(data => {
            locationData = data;
            // Lấy giá trị cũ nếu có (auto-fill)
            let oldProvince = <?= json_encode($province) ?>;
            let oldDistrict = <?= json_encode($district) ?>;
            let oldWard = <?= json_encode($ward) ?>;

            loadProvinces(oldProvince);
            if (oldProvince) loadDistricts(oldProvince, oldDistrict);
            if (oldProvince && oldDistrict) loadWards(oldProvince, oldDistrict, oldWard);

            document.getElementById('province').addEventListener('change', function() {
                loadDistricts(this.value);
                document.getElementById('ward').innerHTML = '<option value="">Chọn phường/xã</option>';
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

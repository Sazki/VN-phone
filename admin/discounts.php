<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

// Xử lý xóa mã giảm giá
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_discount = $conn->prepare("DELETE FROM `discounts` WHERE discountID = ?");
    $delete_discount->execute([$delete_id]);
    header('location:discounts.php');
}

// Xử lý cập nhật mã giảm giá
if (isset($_POST['update_discount'])) {
    $update_id = $_POST['discountID'];
    $code = $_POST['code'];
    $discount_percent = $_POST['discount_percent'];
    $valid_from = $_POST['valid_from'];
    $valid_until = $_POST['valid_until'];

    // Cập nhật dữ liệu với SQL sử dụng NOW()
    $update_discount = $conn->prepare("
        UPDATE `discounts` 
        SET 
            code = ?, 
            discount_percent = ?, 
            valid_from = ?, 
            valid_until = ?, 
            status = CASE 
                        WHEN valid_until > NOW() THEN 'Còn hạn' 
                        ELSE 'Hết hạn' 
                    END
        WHERE discountID = ?
    ");
    $update_discount->execute([$code, $discount_percent, $valid_from, $valid_until, $update_id]);

    // Điều hướng lại trang
    header('location:discounts.php');
}


// Phân trang
$discounts_per_page = 5; // Số mã giảm giá trên mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Trang hiện tại
$start_from = ($page - 1) * $discounts_per_page;

// Truy vấn dữ liệu
$select_discounts = $conn->prepare("SELECT * FROM `discounts` LIMIT ?, ?");
$select_discounts->bindParam(1, $start_from, PDO::PARAM_INT);
$select_discounts->bindParam(2, $discounts_per_page, PDO::PARAM_INT);
$select_discounts->execute();

// Tính tổng số mã giảm giá
$total_discounts = $conn->query("SELECT COUNT(*) FROM `discounts`")->fetchColumn();
$total_pages = ceil($total_discounts / $discounts_per_page);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý mã giảm giá</title>

    <!-- Font Awesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Custom CSS File Link -->
    <link rel="stylesheet" href="../css/admin_style.css">

    <style>
    .heading {
        text-align: center;
        margin: 30px 0;
        font-size: 24px;
        color: #333;
        font-family: 'Arial', sans-serif;
        text-transform: uppercase;
    }

    .discounts-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 16px;
        text-align: left;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background-color: #f8f8f8;
    }

    .discounts-table th,
    .discounts-table td {
        padding: 14px 20px;
        border: 1px solid #ddd;
        text-align: center;
    }

    .discounts-table th {
        background-color: #ecb901;
        color: white;
        font-weight: bold;
        text-transform: uppercase;
    }

    .discounts-table td {
        font-size: 14px;
        color: #333;
    }

    .action-buttons {
        gap: 10px;
    }

    .action-buttons a {
        display: inline-flex;
        /* Đảm bảo nội dung nằm giữa */
        justify-content: center;
        /* Căn giữa theo chiều ngang */
        align-items: center;
        /* Căn giữa theo chiều dọc */
        width: 40px;
        /* Chiều rộng cố định */
        height: 40px;
        /* Chiều cao cố định */
        border-radius: 5px;
        /* Bo góc */
        font-size: 18px;
        /* Kích thước icon */
        text-decoration: none;
        /* Xóa gạch chân */
        transition: all 0.3s ease;
    }

    .action-buttons a.email-btn {
        background-color: #28a745;
        /* Màu xanh cho Gửi Email */
        color: white;
    }

    .action-buttons a.edit-btn {
        background-color: #007bff;
        /* Màu xanh lam cho Sửa */
        color: white;
    }

    .action-buttons a.delete-btn {
        background-color: #dc3545;
        /* Màu đỏ cho Xóa */
        color: white;
    }

    .action-buttons a:hover {
        transform: scale(1.1);
        /* Hiệu ứng phóng to nhẹ khi hover */
    }

    .action-buttons i {
        margin: 0;
        /* Xóa khoảng cách */
        padding: 0;
    }


    .pagination {
        text-align: center;
        margin-top: 20px;
    }

    .pagination a {
        padding: 8px 16px;
        margin: 0 5px;
        text-decoration: none;
        border: 1px solid #ddd;
        color: #ecb901;
        font-size: 14px;
        border-radius: 5px;
    }

    .pagination a.active {
        background-color: #ecb901;
        color: #fff;
    }

    .add-discount-btn {
        text-align: right;
        /* Đẩy nút sang bên phải */
        margin-bottom: 15px;
        /* Tạo khoảng cách giữa nút và bảng */
    }

    .add-discount-btn .btn {
        display: inline-block;
        background-color: #28a745;
        /* Màu nền xanh lá */
        color: white;
        /* Màu chữ */
        padding: 10px 20px;
        /* Kích thước padding */
        text-decoration: none;
        /* Xóa gạch chân */
        border-radius: 5px;
        /* Bo góc */
        font-size: 16px;
        /* Kích thước chữ */
        font-weight: bold;
        /* In đậm chữ */
        transition: all 0.3s ease;
        /* Hiệu ứng khi hover */
    }

    .add-discount-btn .btn:hover {
        background-color: #218838;
        /* Màu xanh đậm hơn khi hover */
        transform: scale(1.05);
        /* Phóng to nhẹ */
    }

    /* Định dạng form thêm mã giảm giá */
    .discount-form {
        background-color: #f8f8f8;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    /* Các trường input trong form */
    .discount-form input,
    .discount-form select {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    /* Nút "Hủy" */
    .cancel-btn {
        background-color: #dc3545;
        /* Màu đỏ */
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 16px;
    }

    .cancel-btn:hover {
        background-color: #c82333;
        transform: scale(1.05);
    }

    /* Hiệu ứng khi form hiển thị */
    #add-discount-form {
        display: none;
        /* Ẩn form mặc định */
    }

    .add-discount-btn {
        text-align: right;
        margin-bottom: 15px;
    }

    /* Style cho modal */
    .modal {
        display: none;
        /* Ẩn modal mặc định */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        /* Nền mờ */
        z-index: 1000;
        transition: opacity 0.3s ease-in-out;
    }

    .modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #fff;
        padding: 20px;
        border-radius: 12px;
        width: 80%;
        max-width: 900px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        /* Bóng đổ */
        animation: modalIn 0.3s ease-in-out;
    }

    @keyframes modalIn {
        0% {
            transform: translate(-50%, -50%) scale(0.8);
            opacity: 0;
        }

        100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
    }

    /* Style bảng danh sách người dùng */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-family: 'Arial', sans-serif;
    }

    table th,
    table td {
        padding: 12px;
        text-align: left;
        font-size: 16px;
    }

    table th {
        background-color: #f8f8f8;
        color: #333;
        border-bottom: 2px solid #ddd;
    }

    table td {
        border-bottom: 1px solid #f0f0f0;
    }

    table tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Style cho nút Đóng */
    .close-modal-btn {
        display: block;
        margin: 10px auto;
        padding: 12px 25px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .close-modal-btn:hover {
        background-color: #45a049;
    }

    /* Style cho nút Gửi Mã */
    .email-btn {
        display: inline-block;
        padding: 8px 16px;
        background-color: #007bff;
        color: #fff;
        border-radius: 5px;
        text-decoration: none;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }

    .email-btn:hover {
        background-color: #0056b3;
    }

    /* Các hiệu ứng nút */
    .email-btn,
    .close-modal-btn {
        transition: transform 0.3s ease;
    }

    .email-btn:hover,
    .close-modal-btn:hover {
        transform: scale(1.05);
    }

    /* Cơ bản: Định dạng chung cho form */
    .discount-form {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 15px;
        /* Bo góc */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* Đổ bóng mềm */
        margin-bottom: 20px;
        font-family: 'Arial', sans-serif;
        font-size: 16px;
    }

    /* Các trường input trong form */
    .discount-form input,
    .discount-form select {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 10px;
        /* Bo góc tròn */
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        /* Đổ bóng bên trong */
        font-size: 16px;
        transition: all 0.3s ease;
    }

    /* Khi người dùng hover hoặc focus vào trường */
    .discount-form input:hover,
    .discount-form input:focus,
    .discount-form select:hover,
    .discount-form select:focus {
        border-color: #ecb901;
        /* Đổi màu viền */
        background-color: #fffbe5;
        /* Nền nhạt */
        box-shadow: 0 0 8px rgba(236, 185, 1, 0.5);
        /* Đổ bóng khi tập trung */
        outline: none;
        /* Bỏ khung viền mặc định */
    }

    /* Label định dạng */
    .discount-form label {
        font-weight: bold;
        margin-bottom: 8px;
        display: block;
        color: #555;
    }

    /* Nút "Hủy" */
    .discount-form .cancel-btn {
        background-color: #dc3545;
        /* Đỏ */
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        /* Bo góc mềm mại */
        font-size: 16px;
        margin-top: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    /* Nút "Thêm" hoặc "Cập nhật" */
    .discount-form .btn {
        background-color: #28a745;
        /* Màu xanh */
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        /* Bo góc mềm mại */
        font-size: 16px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    /* Hiệu ứng hover nút */
    .discount-form .btn:hover {
        background-color: #218838;
        /* Xanh đậm hơn */
        transform: scale(1.05);
        /* Phóng to nhẹ */
    }

    /* Hiệu ứng hover nút "Hủy" */
    .discount-form .cancel-btn:hover {
        background-color: #c82333;
        /* Đỏ đậm */
        transform: scale(1.05);
        /* Phóng to nhẹ */
    }
    </style>
</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="discounts">
        <h1 class="heading">Quản lý mã giảm giá</h1>

        <div class="add-discount-btn">
            <a href="javascript:void(0);" class="btn" onclick="toggleForm()">+ Thêm Mã Giảm Giá</a>
        </div>

        <!-- Form Thêm Mã Giảm Giá (Ẩn mặc định) -->
        <div id="add-discount-form" class="discount-form" style="display: none;">
            <form action="add_discount.php" method="POST">
                <label for="code">Mã giảm giá:</label>
                <input type="text" id="code" name="code" required><br>

                <label for="discount_percent">Phần trăm giảm:</label>
                <input type="number" id="discount_percent" name="discount_percent" required><br>

                <label for="valid_from">Hiệu lực từ:</label>
                <input type="date" id="valid_from" name="valid_from" required><br>

                <label for="valid_until">Hết hạn vào:</label>
                <input type="date" id="valid_until" name="valid_until" required><br>

                <button type="submit" name="add_discount" class="btn">Thêm Mã</button>
                <button type="button" class="btn cancel-btn" onclick="toggleForm()">Hủy</button>
            </form>
        </div>


        <table class="discounts-table">
            <thead>
                <tr>
                    <th>Mã giảm giá</th>
                    <th>Code</th>
                    <th>Phần trăm giảm</th>
                    <th>Hiệu lực từ</th>
                    <th>Hết hạn vào</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($select_discounts->rowCount() > 0) {
                    while ($discount = $select_discounts->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                    <td><?= $discount['discountID']; ?></td>
                    <td><?= $discount['code']; ?></td>
                    <td><?= $discount['discount_percent']; ?>%</td>
                    <td><?= $discount['valid_from']; ?></td>
                    <td><?= $discount['valid_until']; ?></td>
                    <td><?= $discount['status']; ?></td>
                    <td class="action-buttons">
                        <a href="javascript:void(0);" class="email-btn"
                            onclick="openModal(<?= $discount['discountID']; ?>)">
                            <i class="bx bx-mail-send"></i>
                        </a>
                        <a href="javascript:void(0);" class="edit-btn"
                            onclick="showEditForm(<?= $discount['discountID']; ?>, '<?= $discount['code']; ?>', <?= $discount['discount_percent']; ?>, '<?= $discount['valid_from']; ?>', '<?= $discount['valid_until']; ?>')">
                            <i class='bx bx-edit'></i>
                        </a>
                        <a href="discounts.php?delete=<?= $discount['discountID']; ?>" class="delete-btn"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này?');">
                            <i class='bx bx-trash'></i>
                        </a>
                    </td>

                </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="7" class="empty">Không có mã giảm giá nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="discounts.php?page=<?= $page - 1; ?>">&laquo; Trước</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="discounts.php?page=<?= $i; ?>" class="<?= $i == $page ? 'active' : ''; ?>"><?= $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
            <a href="discounts.php?page=<?= $page + 1; ?>">Tiếp &raquo;</a>
            <?php endif; ?>
        </div>
    </section>
    <!-- Modal Danh Sách Người Dùng -->
    <div id="user-list-modal" class="modal">
        <div class="modal-content">
            <h2 style="text-align: center;">DANH SÁCH NGƯỜI DÙNG</h2>
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên khách hàng</th>
                        <th>Email</th>
                        <th>Tính năng</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dữ liệu người dùng sẽ được hiển thị ở đây thông qua AJAX -->
                </tbody>
            </table>
            <button class="close-modal-btn" onclick="closeModal()">Đóng</button>
        </div>
    </div>

    <!-- Form Chỉnh Sửa Mã Giảm Giá (Ẩn mặc định) -->
    <div id="edit-discount-form" class="discount-form" style="display: none;">
        <form action="discounts.php" method="POST">
            <input type="hidden" id="edit-discountID" name="discountID">

            <label for="edit-code">Mã giảm giá:</label>
            <input type="text" id="edit-code" name="code" required><br>

            <label for="edit-discount-percent">Phần trăm giảm:</label>
            <input type="number" id="edit-discount-percent" name="discount_percent" required><br>

            <label for="edit-valid-from">Hiệu lực từ:</label>
            <input type="date" id="edit-valid-from" name="valid_from" required><br>

            <label for="edit-valid-until">Hết hạn vào:</label>
            <input type="date" id="edit-valid-until" name="valid_until" required><br>

            <button type="submit" name="update_discount" class="btn">Cập nhật</button>
            <button type="button" class="btn cancel-btn" onclick="toggleEditForm()">Hủy</button>
        </form>
    </div>


    <script src="../js/admin_script.js"></script>

    <script>
    function toggleForm() {
        var form = document.getElementById('add-discount-form');
        // Chuyển đổi hiển thị của form
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block'; // Hiển thị form
        } else {
            form.style.display = 'none'; // Ẩn form
        }
    }
    // Hàm hiển thị modal
    function openModal(discountID) {
        var modal = document.getElementById("user-list-modal");
        modal.style.display = "block";

        // Gửi yêu cầu AJAX để lấy danh sách người dùng
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "get_users.php?discountID=" + discountID, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.querySelector("#user-list-modal tbody").innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }

    // Hàm đóng modal
    function closeModal() {
        var modal = document.getElementById("user-list-modal");
        modal.style.display = "none";
    }

    function showEditForm(discountID, code, discountPercent, validFrom, validUntil) {
        // Hiển thị form chỉnh sửa
        var form = document.getElementById('edit-discount-form');
        form.style.display = 'block';

        // Điền dữ liệu vào form
        document.getElementById('edit-discountID').value = discountID;
        document.getElementById('edit-code').value = code;
        document.getElementById('edit-discount-percent').value = discountPercent;
        document.getElementById('edit-valid-from').value = validFrom;
        document.getElementById('edit-valid-until').value = validUntil;
    }

    function toggleEditForm() {
        var form = document.getElementById('edit-discount-form');
        form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
    }
    </script>

</body>

</html>
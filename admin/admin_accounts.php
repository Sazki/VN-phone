<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

// Xóa tài khoản
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_admin = $conn->prepare("DELETE FROM `users` WHERE userID = ?");
    $delete_admin->execute([$delete_id]);
    header('location:admin_accounts.php');
}

// Số lượng tài khoản hiển thị trên mỗi trang
$limit = 5;

// Lấy trang hiện tại từ query string (mặc định là 1)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;

// Tính vị trí bắt đầu
$start = ($page - 1) * $limit;

// Lấy tổng số tài khoản
$total_accounts_query = $conn->prepare("SELECT COUNT(*) FROM `users` WHERE role = ?");
$total_accounts_query->execute(['admin']);
$total_accounts = $total_accounts_query->fetchColumn();

// Tổng số trang
$total_pages = ceil($total_accounts / $limit);

// Truy vấn lấy tài khoản theo giới hạn
$select_account = $conn->prepare("SELECT * FROM `users` WHERE role = ? LIMIT $start, $limit");
$select_account->execute(['admin']);

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

    <style>
    /* CSS cho bảng admin accounts */
    .accounts-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 16px;
        text-align: left;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background-color: #f8f8f8;
        animation: fadeIn 1s ease-in-out;
    }

    .accounts-table th,
    .accounts-table td {
        padding: 14px 20px;
        border: 1px solid #ddd;
        font-family: 'Arial', sans-serif;
    }

    .accounts-table th {
        background-color: #ecb901;
        color: white;
        font-weight: bold;
        text-align: center;
        text-transform: uppercase;
    }

    .accounts-table td {
        text-align: center;
        font-size: 14px;
        color: #333;
    }

    .accounts-table tr:hover {
        background-color: #f1f1f1;
    }

    .accounts-table tr:nth-child(even) {
        background-color: #fafafa;
    }

    /* Hiệu ứng fade-in cho bảng */
    @keyframes fadeIn {
        0% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }

    /* Cải thiện nút hành động */
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .action-buttons a {
        padding: 8px 16px;
        border-radius: 5px;
        color: #fff;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: bold;
    }

    .action-buttons a.delete-btn {
        background-color: #dc3545;
    }

    .action-buttons a.option-btn {
        background-color: #007bff;
    }

    .action-buttons a.delete-btn:hover {
        background-color: #c82333;
    }

    .action-buttons a.option-btn:hover {
        background-color: #0056b3;
    }

    .action-buttons a:hover {
        transform: scale(1.1);
    }

    /* Chỉnh sửa phân trang */
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
        transition: background-color 0.3s, transform 0.3s;
    }

    .pagination a:hover {
        background-color: #f1f1f1;
        transform: scale(1.1);
    }

    .pagination a.active {
        background-color: #ecb901;
        color: #fff;
    }

    /* Header */
    .heading {
        text-align: center;
        margin: 30px 0;
        font-size: 24px;
        color: #333;
        font-family: 'Arial', sans-serif;
        text-transform: uppercase;
    }
    </style>

</head>

<body>

    <?php include '../components/admin_header.php' ?>

    <section class="accounts">
        <h1 class="heading">Danh sách tài khoản quản trị viên</h1>

        <table class="accounts-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên quản trị viên</th>
                    <th>Email</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($select_account->rowCount() > 0) {
                    while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                    <td><?= $fetch_accounts['userID']; ?></td>
                    <td><?= $fetch_accounts['name']; ?></td>
                    <td><?= $fetch_accounts['email']; ?></td>
                    <td class="action-buttons">
                        <a href="admin_accounts.php?delete=<?= $fetch_accounts['userID']; ?>" class="delete-btn"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này không?');">Xóa</a>
                        <?php if ($fetch_accounts['userID'] == $admin_id): ?>
                        <a href="update_profile.php" class="option-btn">Sửa</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="4" class="empty">Không có tài khoản nào khả dụng</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1; ?>">&laquo; Trước</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i; ?>" class="<?= $i == $page ? 'active' : ''; ?>"><?= $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1; ?>">Tiếp &raquo;</a>
            <?php endif; ?>
        </div>
    </section>


    <script src="../js/admin_script.js"></script>

</body>

</html>
<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_admin = $conn->prepare("DELETE FROM `users` WHERE userID = ?");
    $delete_admin->execute([$delete_id]);
    header('location:admin_accounts.php');
}

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
    /* CSS cho bảng tài khoản */
    .accounts-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 16px;
        text-align: left;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .accounts-table th,
    .accounts-table td {
        padding: 12px 20px;
        border: 1px solid #ddd;
    }

    .accounts-table th {
        background-color: #ecb901;
        color: white;
        font-weight: bold;
        text-align: center;
    }

    .accounts-table td {
        text-align: left;
    }

    .accounts-table tr {
        transition: background-color 0.3s ease;
    }

    .accounts-table tr:hover {
        background-color: #f1f1f1;
    }

    .actions {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .actions a {
        padding: 8px 15px;
        background-color: #007bff;
        color: #fff;
        border-radius: 5px;
        text-decoration: none;
        font-size: 14px;
        transition: background-color 0.3s;
    }

    .actions a.delete-btn {
        background-color: #dc3545;
    }

    .actions a:hover {
        background-color: #0056b3;
    }

    .actions a.delete-btn:hover {
        background-color: #c82333;
    }

    .empty {
        text-align: center;
        font-size: 18px;
        color: #777;
    }

    .add-admin-btn {
        display: block;
        width: 200px;
        margin: 20px auto;
        padding: 12px;
        background-color: #28a745;
        color: white;
        text-align: center;
        border-radius: 5px;
        font-size: 16px;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .add-admin-btn:hover {
        background-color: #218838;
    }
    </style>
</head>

<body>

    <?php include '../components/admin_header.php' ?>

    <!-- admins accounts section starts  -->
    <section class="accounts">

        <h1 class="heading">tài khoản quản trị viên</h1>

        <!-- Thêm tài khoản quản trị viên mới
        <a href="register_admin.php" class="add-admin-btn">Thêm quản trị viên mới</a> -->

        <!-- Bảng hiển thị tài khoản quản trị viên -->
        <table class="accounts-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên quản trị viên</th>
                    <th>Email</th>
                    <th style="width: 100px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_account = $conn->prepare("SELECT * FROM `users` WHERE role = ?");
                $select_account->execute(['admin']);
                if ($select_account->rowCount() > 0) {
                    while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                    <td><?= $fetch_accounts['userID']; ?></td>
                    <td><?= $fetch_accounts['name']; ?></td>
                    <td><?= $fetch_accounts['email']; ?></td>
                    <td class="actions">
                        <!-- Hiển thị nút xóa và cập nhật -->
                        <a href="admin_accounts.php?delete=<?= $fetch_accounts['userID']; ?>" class="delete-btn"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này không?');">Xóa</a>
                        <?php
                                // Nếu tài khoản là tài khoản hiện tại, cho phép cập nhật
                                if ($fetch_accounts['userID'] == $admin_id) {
                                    echo '<a href="update_profile.php" class="option-btn">Sửa</a>';
                                }
                                ?>
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

    </section>
    <!-- admins accounts section ends -->

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>

</body>

</html>
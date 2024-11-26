<?php
include '../components/connect.php';

if (isset($_GET['discountID'])) {
    $discountID = $_GET['discountID'];

    // Truy vấn tất cả người dùng có role là 'client'
    $select_users = $conn->prepare("SELECT * FROM `users` WHERE role = ?");
    $select_users->execute(['client']);

    $counter = 1;
    while ($user = $select_users->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>{$counter}</td>
                <td>{$user['name']}</td>
                <td>{$user['email']}</td>
                <td><a href='send_discount.php?discountID=$discountID&userID={$user['userID']}' class='email-btn'>Gửi mã</a></td>
            </tr>";
        $counter++;
    }
}
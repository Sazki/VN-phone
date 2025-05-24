<?php
include '../components/connect.php';
session_start();

$email_error = '';
$name_error = '';
$success = '';

$name = '';
$email = '';

if (isset($_POST['submit'])) {
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $pass = htmlspecialchars($_POST['pass'], ENT_QUOTES, 'UTF-8');
    $cpass = htmlspecialchars($_POST['cpass'], ENT_QUOTES, 'UTF-8');

    $phoneNumber = '';
    $address = '';
    $avatar = '';

    // Kiểm tra tên đã có chưa
    $select_admin = $conn->prepare("SELECT * FROM `users` WHERE name = ?");
    $select_admin->execute([$name]);

    // Kiểm tra email đã có chưa
    $select_email = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
    $select_email->execute([$email]);

    if ($select_admin->rowCount() > 0) {
        $name_error = 'Tên người dùng đã tồn tại!';
    } else if ($select_email->rowCount() > 0) {
        $email_error = 'Email này đã được đăng ký!';
    } else if ($pass !== $cpass) {
        $email_error = 'Xác nhận mật khẩu không khớp!';
    } else {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        $insert_admin = $conn->prepare("INSERT INTO `users`(name, email, password, role, phoneNumber, address, avatar) VALUES(?,?,?,?,?,?,?)");
        $insert_admin->execute([$name, $email, $hashed_pass, 'admin', $phoneNumber, $address, $avatar]);
        $success = 'Đã đăng ký quản trị viên mới!';
        $name = $email = '';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký quản trị viên</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        body { background: #f4f7fb; font-family: 'Poppins', Arial, sans-serif;}
        .form-container {
            max-width: 400px;
            margin: 60px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 16px rgba(44,86,141,0.13);
            padding: 32px 24px 24px 24px;
        }
        .form-container h3 { text-align: center; color: #2d568d; margin-bottom: 20px; font-size: 1.5rem; }
        .form-container form { display: flex; flex-direction: column; gap: 12px; }
        .form-container input.box,
        .form-container select {
            border-radius: 8px; border: 1px solid #c7d0e2; padding: 10px 12px; background: #f8fbff; font-size: 2rem;
        }
        .form-container input:focus, .form-container select:focus {
            border-color: #2d568d; background: #e9f0fb;
        }
        .btn {
            background: linear-gradient(90deg, #2d568d, #5487c8 80%);
            color: #fff; border: none; border-radius: 8px;
            font-size: 2rem; font-weight: 600; padding: 10px 0;
            cursor: pointer; margin-top: 6px; transition: opacity 0.18s;
        }
        .btn:hover { opacity: 0.9; }
        .error-message {
            color: #ee4242;
            background: #fde7e7;
            padding: 6px 12px;
            border-radius: 7px;
            font-size: 2rem;
            margin-top: 4px;
            margin-bottom: -6px;
            border: 1px solid #f5b4b4;
        }
        /* Toast notification góc phải */
        .toast-success {
    position: fixed;
    left: 50%;
    bottom: 38px;
    transform: translateX(-50%);
    z-index: 9999;
    background: linear-gradient(90deg, #33e28d 50%, #79e5cb 100%);
    color: #173a1a;
    padding: 16px 44px 16px 20px;
    border-radius: 11px;
    box-shadow: 0 8px 32px #24a96842;
    font-size: 1.08rem;
    font-weight: 600;
    min-width: 240px;
    max-width: 90vw;
    display: flex;
    align-items: center;
    gap: 16px;
    animation: toastFadeIn 0.5s;
}
@media (max-width: 600px) {
    .toast-success { bottom: 14px; min-width: 120px; padding: 13px 30px 13px 12px; }
}
@keyframes toastFadeIn {
    from { opacity: 0; transform: translate(-50%, 50px);}
    to { opacity: 1; transform: translate(-50%, 0);}
}

        .close-toast {
            background: none;
            border: none;
            color: #173a1a;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0 4px;
            font-weight: bold;
            line-height: 1;
            margin-left: 10px;
            opacity: 0.7;
            transition: opacity 0.13s;
        }
        .close-toast:hover { opacity: 1; color: #077f38;}
        @media (max-width: 600px) {
            .toast-success { right: 10px; top: 14px; min-width: 120px; padding: 13px 30px 13px 12px; }
        }
    </style>
</head>
<body>
    <?php include '../components/admin_header.php' ?>

    <?php if (!empty($success)): ?>
        <div class="toast-success" id="toast-success">
            <span><?= $success ?></span>
            <button onclick="document.getElementById('toast-success').style.display='none'" class="close-toast">&times;</button>
        </div>
    <?php endif; ?>

    <section class="form-container">
        <form action="" method="POST" autocomplete="off">
            <h3>Đăng ký quản trị viên</h3>
            <input type="text" name="name" maxlength="20" required placeholder="Nhập tên người dùng của bạn" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')"
                value="<?= htmlspecialchars($name) ?>">
            <?php if (!empty($name_error)): ?>
                <div class="error-message"><?= $name_error ?></div>
            <?php endif; ?>

            <input type="email" name="email" maxlength="40" required placeholder="Nhập email của bạn" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')"
                value="<?= htmlspecialchars($email) ?>">
            <?php if (!empty($email_error)): ?>
                <div class="error-message"><?= $email_error ?></div>
            <?php endif; ?>

            <input type="password" name="pass" maxlength="20" required placeholder="Nhập mật khẩu của bạn" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="cpass" maxlength="20" required placeholder="Xác nhận mật khẩu của bạn"
                class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="Đăng ký ngay" name="submit" class="btn">
        </form>
    </section>
    <script src="../js/admin_script.js"></script>
    <script>
    window.onload = function() {
        var toast = document.getElementById('toast-success');
        if (toast) {
            setTimeout(function() {
                toast.style.display = 'none';
            }, 3400);
        }
    };
    </script>
</body>
</html>

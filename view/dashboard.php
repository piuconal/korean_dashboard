<?php
session_start();
include '../db/connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    setcookie(session_name(), '', time() - 3600, '/'); 
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

// Xử lý đổi mật khẩu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION['user'];
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    $sql = "SELECT password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($stored_password);
    $stmt->fetch();
    $stmt->close();

    // Kiểm tra mật khẩu cũ
    if (!password_verify($current_password, $stored_password)) {
        $error = "Current password is incorrect!";
    } elseif ($new_password !== $confirm_password) {
        $error = "New password does not match!";
    } else {
        // Cập nhật mật khẩu mới
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET password = ? WHERE username = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $new_hashed_password, $username);
        if ($update_stmt->execute()) {
            $success = "Password changed successfully!";
        } else {
            $error = "Có lỗi xảy ra!";
        }
        $update_stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-image: url("../img/bg.jpg");
            background-size: cover;
            background-repeat: no-repeat;
        }
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px gray;
            width: 350px;
            text-align: center;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
        }
        .custom-btn {
            width: 400px !important;
            padding: 40px; /* Tăng kích thước nút */
            font-size: 1.2rem; /* Tăng cỡ chữ */
            font-weight: bold;
        }
        #loadingMessage {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px gray;
            font-size: 18px;
            font-weight: bold;
            color: blue;
            z-index: 1000;
        }
        .nav-item button{
            height: 50px;
            font-size: 20px;
        }
        .nav-item a{
            height: 50px;
            font-size: 23px;
        }
    </style>
    <script>
        function openPopup() {
            document.getElementById("changePasswordPopup").style.display = "block";
            document.getElementById("overlay").style.display = "block";
        }
        function closePopup() {
            document.getElementById("changePasswordPopup").style.display = "none";
            document.getElementById("overlay").style.display = "none";
        }
    </script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="log.php" target="_blank" class="btn btn-info text-white">View Log</a>
                </li>
                <li class="nav-item ms-2">
                    <button class="btn btn-warning text-dark" onclick="openPopup()">Change password</button>
                </li>
                <li class="nav-item ms-2">
                    <a class="btn btn-danger text-white" href="?logout=true">Log out</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Nội dung chính -->
<div class="container text-center mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="row justify-content-between">
                <div class="col-6 mb-5">
                    <button class="btn btn-primary btn-lg w-100 custom-btn py-4 fs-3" onclick="openExcel()">Payment History</button>
                </div>
                <div class="col-6 mb-5">
                    <button class="btn btn-success btn-lg w-100 custom-btn py-4 fs-3" onclick="runSolvePy()">Date Calculator</button>
                </div>
                <div class="col-6">
                    <button class="btn btn-info btn-lg w-100 custom-btn py-4 fs-3" onclick="openIncomeManage()">Income Manage</button>
                </div>
                <div class="col-6">
                    <button class="btn btn-danger btn-lg w-100 custom-btn py-4 fs-3">Statics</button>
                </div>
            </div>
        </div>
    </div>
</div>
<p id="loadingMessage" style="display: none; color: blue;">Opening Excel file...</p>

<!-- Overlay -->
<div class="overlay" id="overlay"></div>

<!-- Popup đổi mật khẩu -->
<div id="changePasswordPopup" class="popup">
    <h3>Change password</h3>
    <?php if (!empty($error)) : ?>
    <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (!empty($success)) : ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="password" name="current_password" placeholder="Current Password" required class="form-control mb-2">
        <input type="password" name="new_password" placeholder="New Password" required class="form-control mb-2">
        <input type="password" name="confirm_password" placeholder="Re-enter new password" required class="form-control mb-2">
        <button type="submit" class="btn btn-success">Confirm</button>
        <button type="button" class="btn btn-secondary" onclick="closePopup()">Cancel</button>
    </form>
</div>

</body>

<script>
    window.onload = function () {
    var error = "<?php echo $error; ?>";
    var success = "<?php echo $success; ?>";

    if (error || success) {
        openPopup();
    }
    };
</script>
<script src="../view/js/dashboard.js"></script>
</html>

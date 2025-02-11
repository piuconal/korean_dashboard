<?php
session_start();
include '../db/connect.php';

if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($db_username, $db_password);
    $stmt->fetch();
    $stmt->close();

    if ($db_username && password_verify($password, $db_password)) {
        $_SESSION['user'] = $db_username;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Wrong account or password!";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("loginPopup").style.display = "block";
        });

        function closeLoginPopup() {
            document.getElementById("loginPopup").style.display = "none";
            document.getElementById("overlay").style.display = "none";
        }
    </script>
    <style>
        body {
            text-align: center;
            padding: 50px;
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
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px gray;
            width: 600px;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: block;
        }
        .close-btn {
            background: red;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            float: right;
        }
        input{
            height: 50px;
            font-size: 30px !important;
        }
        button{
            height: 50px;
            font-size: 20px !important;
            font-weight: bold !important;
        }
    </style>
</head>
<body>
    <div class="overlay" id="overlay"></div>
    <div id="loginPopup" class="popup">
        <h3>Log in</h3>
        <?php if ($error) echo "<p style='color:red;'>".htmlspecialchars($error)."</p>"; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required class="form-control mb-4 mt-3">
            <input type="password" name="password" placeholder="Password" required class="form-control mb-4">
            <button type="submit" class="btn btn-success w-100">Log in</button>
        </form>
    </div>
</body>
</html>

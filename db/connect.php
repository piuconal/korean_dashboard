<?php
$servername = "127.0.0.1";
$username = "root"; 
$password = "";
$dbname = "korean_dashboard";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>

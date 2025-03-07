<?php
include '../db/connect.php';

$ship_code = $_GET['ship_code'] ?? '';

if (!$ship_code) {
    echo json_encode(["success" => false, "error" => "Mã tàu không hợp lệ"]);
    exit;
}

$query = "SELECT registration_fee FROM ships WHERE ship_code = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $ship_code);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    echo json_encode(["success" => true, "registration_fee" => $row['registration_fee']]);
} else {
    echo json_encode(["success" => false, "error" => "Không tìm thấy tàu"]);
}

$stmt->close();
$conn->close();
?>

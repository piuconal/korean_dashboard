<?php
include '../db/connect.php'; // Kết nối DB

header('Content-Type: application/json');

// Bật hiển thị lỗi PHP để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents("php://input"), true);

$ship_code = $data["ship_code"] ?? '';
$fee = intval($data["fee"] ?? 0);
$status = intval($data["status"] ?? 0);

if (!$ship_code) {
    echo json_encode(["success" => false, "error" => "Mã tàu không hợp lệ"]);
    exit;
}

// Kiểm tra kết nối DB
if (!$conn) {
    echo json_encode(["success" => false, "error" => "Lỗi kết nối DB"]);
    exit;
}

// Cập nhật registration_fee và registration_fee_status
$query = "UPDATE ships SET registration_fee = ?, registration_fee_status = ? WHERE ship_code = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iis", $fee, $status, $ship_code);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>

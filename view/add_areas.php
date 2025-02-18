<?php
// Bật hiển thị lỗi để dễ dàng debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../db/connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['areas']) || !is_array($data['areas'])) {
    echo json_encode(["success" => false, "error" => "Dữ liệu không hợp lệ"]);
    exit();
}

// Chuẩn bị câu lệnh SQL
$stmt = $conn->prepare("INSERT INTO area (name, status) VALUES (?, 0) ON DUPLICATE KEY UPDATE name = VALUES(name)");
$stmt->bind_param("s", $name);

// Thực thi câu lệnh SQL cho mỗi khu vực
foreach ($data['areas'] as $area) {
    $name = $area;
    if (!$stmt->execute()) {
        echo json_encode(["success" => false, "error" => "Lỗi khi thực thi SQL: " . $stmt->error]);
        exit();
    }
}

$stmt->close();
$conn->close();

// Trả về phản hồi JSON
echo json_encode(["success" => true]);
?>

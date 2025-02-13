<?php
include '../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $type = $_POST['type'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $disembark_date = $_POST['disembark_date'] ?? null; // Cho phép NULL
    $moving_fee = $_POST['moving_fee'] ?? '';

    // Kiểm tra thông tin bắt buộc
    if (empty($id) || empty($type) || empty($start_date)) {
        echo json_encode(["success" => false, "message" => "Invalid data"]);
        exit;
    }

    // Kiểm tra type hợp lệ
    $valid_types = ['신규', '근변', '재입국'];
    if (!in_array($type, $valid_types)) {
        echo json_encode(["success" => false, "message" => "Invalid type"]);
        exit;
    }

    // Cập nhật cơ sở dữ liệu (cho phép `disembark_date` NULL nếu rỗng)
    $stmt = $conn->prepare("UPDATE crew_members SET type = ?, start_date = ?, disembark_date = ?, moving_fee = ? WHERE id = ?");
    $stmt->bind_param("sssii", $type, $start_date, $disembark_date, $moving_fee, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Update failed"]);
    }
}

?>

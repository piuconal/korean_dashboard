<?php
include '../db/connect.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $refund_amount = $_POST['refund_amount'] ?? '';

    if (empty($id) || !is_numeric($refund_amount)) {
        echo json_encode(["success" => false, "message" => "Dữ liệu không hợp lệ"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE crew_members SET refund_amount = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Lỗi prepare: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("ii", $refund_amount, $id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Lỗi SQL: " . $stmt->error]);
    }

    $stmt->close();
}
?>

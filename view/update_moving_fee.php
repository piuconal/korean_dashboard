<?php
include '../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $moving_fee = $_POST['moving_fee'] ?? '';

    if (empty($id) || !is_numeric($moving_fee)) {
        echo json_encode(["success" => false, "message" => "Dữ liệu không hợp lệ"]);
        exit();
    }

    // Cập nhật moving_fee
    $stmt = $conn->prepare("UPDATE crew_members SET moving_fee = ? WHERE id = ?");
    $stmt->bind_param("ii", $moving_fee, $id);

    if (!$stmt->execute()) {
        echo json_encode(["success" => false, "message" => "Cập nhật moving_fee thất bại"]);
        exit();
    }

    // Cập nhật outstanding_amount
    $stmt = $conn->prepare("UPDATE crew_members SET outstanding_amount = ship_fee + moving_fee WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $update_url = "http://$servername/korean_dashboard/view/update_outstanding.php";
    file_get_contents($update_url);

    echo json_encode(["success" => true]);
}
?>

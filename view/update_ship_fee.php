<?php
include '../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $ship_fee = $_POST['ship_fee'] ?? '';

    if (empty($id) || !is_numeric($ship_fee)) {
        echo json_encode(["success" => false, "message" => "Dữ liệu không hợp lệ"]);
        exit();
    }

    // Cập nhật ship_fee trước
    $stmt = $conn->prepare("UPDATE crew_members SET ship_fee = ? WHERE id = ?");
    $stmt->bind_param("ii", $ship_fee, $id);

    if (!$stmt->execute()) {
        echo json_encode(["success" => false, "message" => "Cập nhật ship_fee thất bại"]);
        exit();
    }

    // Tính toán lại outstanding_amount
    $stmt = $conn->prepare("UPDATE crew_members SET outstanding_amount = ship_fee + moving_fee WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Gọi update_outstanding.php để cập nhật trạng thái tàu
    $update_url = "http://$servername/korean_dashboard/view/update_outstanding.php";
    file_get_contents($update_url);

    echo json_encode(["success" => true]);
}
?>

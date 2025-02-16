<?php
include '../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $ship_fee = $_POST['ship_fee'] ?? '';
    $start_date = $_POST['start_date'] ?? '';

    if (empty($id) || !is_numeric($ship_fee)) {
        echo json_encode(["success" => false, "message" => "Dữ liệu không hợp lệ"]);
        exit();
    }

    // Cập nhật ship_fee
    $stmt = $conn->prepare("UPDATE crew_members SET ship_fee = ? WHERE id = ?");
    $stmt->bind_param("ii", $ship_fee, $id);

    if (!$stmt->execute()) {
        echo json_encode(["success" => false, "message" => "Cập nhật ship_fee thất bại"]);
        exit();
    }

    // Nếu ship_fee = 0, cập nhật note
    if ($ship_fee == 0 && !empty($start_date)) {
        $note = "$start_date paid";
        $stmt = $conn->prepare("UPDATE crew_members SET note = ? WHERE id = ?");
        $stmt->bind_param("si", $note, $id);
        $stmt->execute();
    }

    // Tính lại outstanding_amount
    $stmt = $conn->prepare("UPDATE crew_members SET outstanding_amount = ship_fee + moving_fee WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $update_url = "http://$servername/korean_dashboard/view/update_outstanding.php";
    file_get_contents($update_url);

    echo json_encode(["success" => true, "updated_note" => $ship_fee == 0 ? $note : "Không thay đổi"]);
}
?>

<?php
include '../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $ship_fee = $_POST['ship_fee'] ?? '';

    if (empty($id)) {
        echo json_encode(["success" => false, "message" => "Invalid ID"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE crew_members SET ship_fee = ? WHERE id = ?");
    $stmt->bind_param("ii", $ship_fee, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Update failed"]);
    }
}
?>

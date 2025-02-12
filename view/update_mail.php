<?php
include '../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $status = $_POST['status'] ?? '';

    if (!empty($id) && ($status === "0" || $status === "1")) {
        $stmt = $conn->prepare("UPDATE crew_members SET request_status = ? WHERE id = ?");
        $stmt->bind_param("ii", $status, $id);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Dữ liệu không hợp lệ"]);
    }
}
?>

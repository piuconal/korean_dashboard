<?php
include '../db/connect.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';

    // Kiểm tra id hợp lệ
    if (empty($id)) {
        echo json_encode(["success" => false, "message" => "Invalid data"]);
        exit;
    }

    // Lấy dữ liệu hiện tại của seaman
    $stmt = $conn->prepare("SELECT * FROM crew_members WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $seaman = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$seaman) {
        echo json_encode(["success" => false, "message" => "Seaman not found"]);
        exit;
    }

    // Tính toán năm tiếp theo
    $newYear = $seaman['year'] + 1;

    // Cập nhật năm trong cơ sở dữ liệu
    $stmt = $conn->prepare("UPDATE crew_members SET year = ? WHERE id = ?");
    $stmt->bind_param("ii", $newYear, $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["success" => true]);
}
?>

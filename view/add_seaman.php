<?php
include '../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $passport = $_POST['passport'] ?? '';
    $entry_date = $_POST['entry_date'] ?? '';
    $ship_name = $_POST['ship_name'] ?? '';

    if ($name && $passport && $entry_date && $ship_name) {
        // Lấy ship_id từ tên tàu
        $stmt = $conn->prepare("SELECT id FROM ships WHERE name = ? LIMIT 1");
        $stmt->bind_param("s", $ship_name);
        $stmt->execute();
        $result = $stmt->get_result();
        $ship = $result->fetch_assoc();

        if ($ship) {
            $ship_id = $ship['id'];

            // Thêm vào bảng crew_members
            $stmt = $conn->prepare("INSERT INTO crew_members (name, passport_number, entry_date, ship_id, type) VALUES (?, ?, ?, ?, '')");
            $stmt->bind_param("sssi", $name, $passport, $entry_date, $ship_id);
            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "error" => "Lỗi khi thêm dữ liệu"]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "Không tìm thấy tàu"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Dữ liệu không hợp lệ"]);
    }
}
?>

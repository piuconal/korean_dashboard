<?php
include '../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ship_name"])) {
    $ship_name = trim($_POST["ship_name"]);

    if (!empty($ship_name)) {
        // Kiểm tra xem tàu đã tồn tại chưa
        $stmt = $conn->prepare("SELECT id FROM ships WHERE name = ?");
        $stmt->bind_param("s", $ship_name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            // Nếu chưa có, thêm mới vào database
            $stmt = $conn->prepare("INSERT INTO ships (name) VALUES (?)");
            $stmt->bind_param("s", $ship_name);
            if ($stmt->execute()) {
                echo json_encode(["success" => true, "redirect" => "detail_ship.php?ship_name=" . urlencode($ship_name)]);
            } else {
                echo json_encode(["success" => false, "error" => "Lỗi khi thêm tàu"]);
            }
        } else {
            // Nếu tàu đã tồn tại, chuyển hướng luôn đến trang chi tiết tàu
            echo json_encode(["success" => true, "redirect" => "detail_ship.php?ship_name=" . urlencode($ship_name)]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Tên tàu không hợp lệ"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Yêu cầu không hợp lệ"]);
}
?>

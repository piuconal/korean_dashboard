<?php
include '../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ship_name"])) {
    $ship_name = trim($_POST["ship_name"]); // Xóa khoảng trắng đầu/cuối
    $ship_name = str_replace("❗", "", $ship_name);
    $ship_name = preg_replace('/\s+/', ' ', $ship_name); // Xóa khoảng trắng thừa

    if (!empty($ship_name)) {
        $stmt = $conn->prepare("SELECT id FROM ships WHERE name = ?");
        $stmt->bind_param("s", $ship_name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO ships (name) VALUES (?)");
            $stmt->bind_param("s", $ship_name);
            if ($stmt->execute()) {
                echo json_encode(["success" => true, "redirect" => "detail_ship.php?ship_name=" . rawurlencode($ship_name)]);
            } else {
                echo json_encode(["success" => false, "error" => "Lỗi khi thêm tàu"]);
            }
        } else {
            echo json_encode(["success" => true, "redirect" => "detail_ship.php?ship_name=" . rawurlencode($ship_name)]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Tên tàu không hợp lệ"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Yêu cầu không hợp lệ"]);
}

?>

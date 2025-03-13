<?php
require '../db/connect.php'; // Kết nối database

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Nhận dữ liệu JSON từ AJAX
$data = json_decode(file_get_contents("php://input"), true);
$ship_id = $data['ship_id'] ?? 0;
$outstanding_status = $data['outstanding_status'] ?? 0;

file_put_contents("log.txt", "Ship ID: $ship_id, Status: $outstanding_status\n", FILE_APPEND);

if ($ship_id > 0) {
    $sql = "UPDATE ships SET outstanding_status = $outstanding_status WHERE id = $ship_id";
    file_put_contents("log.txt", "SQL: $sql\n", FILE_APPEND);

    if ($conn->query($sql) === TRUE) {
        echo "Cập nhật trạng thái outstanding thành công!";
    } else {
        echo "Lỗi: " . $conn->error;
        file_put_contents("log.txt", "Lỗi: " . $conn->error . "\n", FILE_APPEND);
    }
} else {
    echo "Thiếu hoặc sai ID tàu.";
    file_put_contents("log.txt", "Thiếu hoặc sai ID tàu.\n", FILE_APPEND);
}

$conn->close();
?>

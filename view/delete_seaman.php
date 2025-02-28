<?php
// Bật chế độ báo lỗi để debug (có thể xóa sau khi xong)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Không hiển thị lỗi trực tiếp, chỉ log

include '../db/connect.php'; // Kết nối database

// Xóa bất kỳ output nào trước khi gửi JSON
ob_clean();

header('Content-Type: application/json'); // Đảm bảo header JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;

    // Kiểm tra dữ liệu đầu vào
    if (!$id || !is_numeric($id)) {
        echo json_encode(["success" => false, "error" => "Invalid data: ID is missing or invalid"]);
        exit;
    }

    // Kiểm tra kết nối database
    if (!$conn || $conn->connect_error) {
        echo json_encode(["success" => false, "error" => "Database connection failed: " . ($conn ? $conn->connect_error : 'No connection')]);
        exit;
    }

    // Chuẩn bị câu lệnh xóa
    $stmt = $conn->prepare("DELETE FROM crew_members WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "Prepare failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $id);

    // Thực thi và kiểm tra kết quả
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true, "message" => "Seaman deleted successfully"]);
        } else {
            echo json_encode(["success" => false, "error" => "No seaman found with this ID"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Database error: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
}

$conn->close();
?>
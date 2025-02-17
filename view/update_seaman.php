<?php
include '../db/connect.php';
include 'log_seaman_changes.php'; // Import hàm logSeamanChanges

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $type = $_POST['type'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $disembark_date = $_POST['disembark_date'] ?? null; // Cho phép NULL
    $moving_fee = $_POST['moving_fee'] ?? '';

    // Kiểm tra thông tin bắt buộc
    if (empty($id) || empty($type) || empty($start_date)) {
        echo json_encode(["success" => false, "message" => "Invalid data"]);
        exit;
    }

    // Kiểm tra type hợp lệ
    $valid_types = ['신규', '근변', '재입국'];
    if (!in_array($type, $valid_types)) {
        echo json_encode(["success" => false, "message" => "Invalid type"]);
        exit;
    }

    // Lấy dữ liệu hiện tại của seaman để so sánh
    $stmt = $conn->prepare("SELECT * FROM crew_members WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $old_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$old_data) {
        echo json_encode(["success" => false, "message" => "Seaman not found"]);
        exit;
    }

    // Chuẩn bị dữ liệu mới để cập nhật
    $new_data = [
        'type' => $type,
        'start_date' => $start_date,
        'disembark_date' => $disembark_date,
        'moving_fee' => $moving_fee
    ];

    // Ghi log lịch sử thay đổi trước khi cập nhật
    $updated_by = 'admin'; // Thay bằng user đăng nhập thực tế
    logSeamanChanges($id, $updated_by, $old_data, $new_data);

    // Lấy năm từ start_date
    $year = date('Y', strtotime($start_date));

    // Cập nhật dữ liệu mới và trường year
    $stmt = $conn->prepare("UPDATE crew_members SET type = ?, start_date = ?, disembark_date = ?, moving_fee = ?, year = ? WHERE id = ?");
    $stmt->bind_param("sssiii", $type, $start_date, $disembark_date, $moving_fee, $year, $id);
    $stmt->execute();
    $stmt->close();

    // Cập nhật outstanding_amount
    $stmt = $conn->prepare("UPDATE crew_members SET outstanding_amount = ship_fee + moving_fee WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Load lại trang cập nhật outstanding_amount
    $update_url = "http://$servername/korean_dashboard/view/update_outstanding.php";
    file_get_contents($update_url);

    echo json_encode(["success" => true]);
}
?>

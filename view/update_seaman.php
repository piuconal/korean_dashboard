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
    // Kiểm tra có thay đổi không
    $has_changes = false;
    foreach ($new_data as $key => $value) {
        if ($old_data[$key] != $value) {
            $has_changes = true;
            break;
        }
    }

    // Nếu có thay đổi, ghi lại vào JSON
    if ($has_changes) {
        logSeamanChangesFull($old_data, $new_data);
    }
    // Ghi log lịch sử thay đổi trước khi cập nhật
    $updated_by = 'admin'; // Thay bằng user đăng nhập thực tế
    logSeamanChanges($id, $updated_by, $old_data, $new_data);

    // Lấy năm từ start_date
    $year = date('Y', strtotime($start_date));

    // Kiểm tra xem year đã có hay chưa
    $check_stmt = $conn->prepare("SELECT year FROM crew_members WHERE id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_stmt->bind_result($existing_year);
    $check_stmt->fetch();
    $check_stmt->close();

    // Nếu year chưa có (hoặc bằng 0), thực hiện cập nhật
    if (empty($existing_year)) {
        // Cập nhật dữ liệu mới và trường year
        $stmt = $conn->prepare("UPDATE crew_members SET type = ?, start_date = ?, disembark_date = ?, moving_fee = ?, year = ? WHERE id = ?");
        $stmt->bind_param("sssiii", $type, $start_date, $disembark_date, $moving_fee, $year, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        // Cập nhật mà không thay đổi trường year
        $stmt = $conn->prepare("UPDATE crew_members SET type = ?, start_date = ?, disembark_date = ?, moving_fee = ? WHERE id = ?");
        $stmt->bind_param("sssii", $type, $start_date, $disembark_date, $moving_fee, $id);
        $stmt->execute();
        $stmt->close();
    }


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

// Hàm ghi log dữ liệu mới vào JSON, đánh dấu thay đổi với '*'
function logSeamanChangesFull($old_data, $new_data) {
    $logFile = '../logs/seaman_changes.json';

    // Tạo bản ghi log mới
    $logEntry = [
        'id' => $old_data['id'],
        'timestamp' => date('Y-m-d H:i:s'),
        'updated_row' => []
    ];

    // So sánh dữ liệu và thêm '*' vào cột thay đổi
    foreach ($old_data as $key => $value) {
        if (isset($new_data[$key]) && $new_data[$key] != $value) {
            $logEntry['updated_row'][$key] = '*' . $new_data[$key];
        } else {
            $logEntry['updated_row'][$key] = $value;
        }
    }

    // Đọc file JSON hiện có
    $logs = [];
    if (file_exists($logFile)) {
        $logs = json_decode(file_get_contents($logFile), true) ?? [];
    }

    // Thêm log mới
    $logs[] = $logEntry;

    // Ghi lại file JSON
    file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
?>

<?php
include '../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $ship_fee = $_POST['ship_fee'] ?? '';
    $start_date = $_POST['start_date'] ?? '';

    if (empty($id) || !is_numeric($ship_fee)) {
        echo json_encode(["success" => false, "message" => "Dữ liệu không hợp lệ"]);
        exit();
    }

    // Cập nhật ship_fee
    $stmt = $conn->prepare("UPDATE crew_members SET ship_fee = ? WHERE id = ?");
    $stmt->bind_param("ii", $ship_fee, $id);

    if (!$stmt->execute()) {
        echo json_encode(["success" => false, "message" => "Cập nhật ship_fee thất bại"]);
        exit();
    }

    // Nếu ship_fee = 0 và có start_date, kiểm tra và cập nhật note
    $new_note = "";
    if ($ship_fee == 0 && !empty($start_date)) {
        // Lấy giá trị cũ của note
        $stmt = $conn->prepare("SELECT note FROM crew_members WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $current_note = $row['note'] ?? '';

        // Kiểm tra nếu chuỗi "start_date paid" đã có trong note chưa
        $note_to_add = "$start_date paid, ";
        if (strpos($current_note, $note_to_add) === false) {
            // Nếu chưa có, nối thêm vào cuối note
            $new_note = $current_note . " " . $note_to_add;

            // Cập nhật lại note trong cơ sở dữ liệu
            $stmt = $conn->prepare("UPDATE crew_members SET note = ? WHERE id = ?");
            $stmt->bind_param("si", $new_note, $id);
            $stmt->execute();

            // Lưu lịch sử thay đổi vào bảng crew_history
            $updated_by = 'admin'; // Thay thế bằng tên người dùng hiện tại (có thể lấy từ session)
            $column_name = 'note';  // Cột đang cập nhật là 'note'
            $old_value = $current_note;  // Giá trị cũ của 'note'
            $new_value = $new_note;  // Giá trị mới của 'note'

            $stmt = $conn->prepare("INSERT INTO crew_history (crew_id, column_name, old_value, new_value, updated_by) 
                                    VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $id, $column_name, $old_value, $new_value, $updated_by);
            $stmt->execute();
        }
    }

    // Tính lại outstanding_amount
    $stmt = $conn->prepare("UPDATE crew_members SET outstanding_amount = ship_fee + moving_fee WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Cập nhật dữ liệu vào hệ thống, không cần ghi vào file JSON nữa
    $update_url = "http://$servername/korean_dashboard/view/update_outstanding.php";
    file_get_contents($update_url);

    echo json_encode(["success" => true, "updated_note" => $ship_fee == 0 ? $new_note : "Không thay đổi"]);
}
?>

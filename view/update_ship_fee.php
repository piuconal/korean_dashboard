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
    if ($ship_fee == 0 && !empty($start_date)) {
        // Lấy giá trị cũ của note
        $stmt = $conn->prepare("SELECT note FROM crew_members WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $current_note = $row['note'] ?? '';

        // Kiểm tra nếu chuỗi "start_date paid" đã có trong note chưa
        $note_to_add = "$start_date paid";
        if (strpos($current_note, $note_to_add) === false) {
            // Nếu chưa có, nối thêm vào cuối note
            $new_note = $current_note . " " . $note_to_add;

            // Cập nhật lại note trong cơ sở dữ liệu
            $stmt = $conn->prepare("UPDATE crew_members SET note = ? WHERE id = ?");
            $stmt->bind_param("si", $new_note, $id);
            $stmt->execute();
        }
    }

    // Tính lại outstanding_amount
    $stmt = $conn->prepare("UPDATE crew_members SET outstanding_amount = ship_fee + moving_fee WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Cập nhật thông tin vào file JSON
    $file = '../data/crew_members.json'; // Đường dẫn đến file JSON

    if (file_exists($file)) {
        $jsonData = json_decode(file_get_contents($file), true); // Đọc dữ liệu JSON vào mảng

        // Tìm và cập nhật đối tượng trong mảng theo id
        foreach ($jsonData as &$crewMember) {
            if ($crewMember['id'] == $id) {
                // Cập nhật lại note trong file JSON
                $crewMember['note'] = $new_note ?? $current_note;
                break;
            }
        }

        // Ghi lại dữ liệu JSON vào file
        file_put_contents($file, json_encode($jsonData, JSON_PRETTY_PRINT));

    } else {
        echo json_encode(["success" => false, "message" => "File JSON không tồn tại"]);
        exit();
    }

    $update_url = "http://$servername/korean_dashboard/view/update_outstanding.php";
    file_get_contents($update_url);

    echo json_encode(["success" => true, "updated_note" => $ship_fee == 0 ? $new_note : "Không thay đổi"]);
}
?>

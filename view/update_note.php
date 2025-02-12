<?php
include '../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Xử lý cập nhật ghi chú
    $id = $_POST['id'] ?? '';
    $note = $_POST['note'] ?? '';

    if ($id && isset($note)) {
        $stmt = $conn->prepare("UPDATE crew_members SET note = ? WHERE id = ?");
        $stmt->bind_param("si", $note, $id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false]);
        }
    } else {
        echo json_encode(["success" => false]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    // Lấy ghi chú từ database
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT note FROM crew_members WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(["success" => true, "note" => $row["note"]]);
    } else {
        echo json_encode(["success" => false]);
    }
} else {
    echo json_encode(["success" => false]);
}
?>

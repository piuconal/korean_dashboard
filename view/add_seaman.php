<?php
include '../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $passport = $_POST['passport'] ?? '';
    $entry_date = $_POST['entry_date'] ?? '';
    $ship_name = $_POST['ship_name'] ?? '';

    if (!$name || !$passport || !$entry_date || !$ship_name) {
        echo json_encode(["success" => false, "error" => "Invalid data: missing fields"]);
        exit;
    }

    // Tìm ship_id từ tên tàu
    $stmt = $conn->prepare("SELECT id FROM ships WHERE name = ? LIMIT 1");
    $stmt->bind_param("s", $ship_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $ship = $result->fetch_assoc();
    $ship_id = $ship ? $ship['id'] : NULL;

    // Kiểm tra trùng lặp
    $stmt = $conn->prepare("SELECT id FROM crew_members WHERE passport_number = ? LIMIT 1");
    $stmt->bind_param("s", $passport);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_crew = $result->fetch_assoc();

    if ($existing_crew) {
        echo json_encode(["success" => false, "error" => "Crew member already exists"]);
    } else {
        $stmt = $conn->prepare("INSERT INTO crew_members (name, passport_number, entry_date, ship_id, type, ship_fee) VALUES (?, ?, ?, ?, '', 132000)");
        $stmt->bind_param("sssi", $name, $passport, $entry_date, $ship_id);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Database error"]);
        }
    }
}
?>

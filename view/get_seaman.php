<?php
include '../db/connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT id, type, start_date, disembark_date, ship_fee, moving_fee FROM crew_members WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $seaman = $result->fetch_assoc();

    if ($seaman) {
        echo json_encode(["success" => true, "seaman" => $seaman]);
    } else {
        echo json_encode(["success" => false]);
    }
}
?>

<?php
require '../db/connect.php'; // Kết nối database

header("Content-Type: application/json");

$sql = "SELECT name, outstanding_status FROM ships";
$result = $conn->query($sql);

$outstandingData = [];
while ($row = $result->fetch_assoc()) {
    $outstandingData[$row['name']] = (int) $row['outstanding_status'];
}

echo json_encode($outstandingData);

$conn->close();
?>

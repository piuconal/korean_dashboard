<?php
include '../db/connect.php';

$area_id = isset($_GET['area_id']) ? intval($_GET['area_id']) : 0;

$query = "SELECT id, name, outstanding_status FROM ships WHERE area_id = $area_id";
$result = mysqli_query($conn, $query);

$ships = [];
while ($row = mysqli_fetch_assoc($result)) {
    $ships[] = $row;
}

echo json_encode($ships);
?>

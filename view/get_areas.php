<?php
include '../db/connect.php';

$query = "SELECT id, name, status FROM area";
$result = mysqli_query($conn, $query);

$areas = [];
while ($row = mysqli_fetch_assoc($result)) {
    $areas[] = $row;
}

echo json_encode($areas);
?>

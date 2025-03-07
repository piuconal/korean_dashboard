<?php
include '../db/connect.php';

$query = "SELECT name, passport_number, entry_date, employment_status FROM crew_members_additional WHERE employment_status IN ('근무', '하선(고용중단)')";
$result = mysqli_query($conn, $query);

$crew_members = [];
while ($row = mysqli_fetch_assoc($result)) {
    $crew_members[] = $row;
}

echo json_encode($crew_members);
?>

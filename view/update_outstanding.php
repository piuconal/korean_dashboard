<?php
require '../db/connect.php'; // Kết nối database

// Cập nhật outstanding_status trong bảng ships
$sql = "
    UPDATE ships s
    SET outstanding_status = (
        SELECT IF(SUM(cm.outstanding_amount) > 0, 1, 0)
        FROM crew_members cm
        WHERE cm.ship_id = s.id
    )
";

if ($conn->query($sql) === TRUE) {
    echo "Cập nhật trạng thái outstanding thành công!";
} else {
    echo "Lỗi: " . $conn->error;
}

$conn->close();
?>

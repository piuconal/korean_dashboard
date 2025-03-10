<?php
include '../db/connect.php'; // Kết nối cơ sở dữ liệu

$logFile = '../logs/seaman_changes.json'; // Đường dẫn file JSON

// Kiểm tra nếu file tồn tại
if (!file_exists($logFile)) {
    die("Log file not found.");
}

// Đọc nội dung file JSON
$jsonData = file_get_contents($logFile);
$logs = json_decode($jsonData, true);

// Kiểm tra nếu dữ liệu JSON rỗng hoặc không hợp lệ
if (!$logs) {
    die("No log data available.");
}

// Lấy tên tàu từ bảng ships
$ships = [];
$shipQuery = "SELECT id, name, area_id FROM ships";
$shipResult = mysqli_query($conn, $shipQuery);
while ($shipRow = mysqli_fetch_assoc($shipResult)) {
    $ships[$shipRow['id']] = [
        'name' => $shipRow['name'],
        'area_id' => $shipRow['area_id']
    ];
}

// Lấy tên khu vực từ bảng area
$areas = [];
$areaQuery = "SELECT id, name FROM area";
$areaResult = mysqli_query($conn, $areaQuery);
while ($areaRow = mysqli_fetch_assoc($areaResult)) {
    $areas[$areaRow['id']] = $areaRow['name'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seaman Changes Log</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .highlight {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Seaman Changes Log</h2>
<table>
    <tr>
        <!-- <th>ID</th> -->
        <th>Tên tàu</th>
        <th>Khu vực</th>
        <th>Name</th>
        <th>Passport Number</th>
        <th>Entry Date</th>
        <th>Type</th>
        <th>Start Date</th>
        <th>Disembark Date</th>
        <th>Ship Fee</th>
        <th>Moving Fee</th>
        <th>Outstanding Amount</th>
        <!-- <th>Refund Amount</th> -->
        <!-- <th>Request Status</th> -->
        <!-- <th>Note</th> -->
        <th>Created At</th>
        <th>Updated At</th>
        <!-- <th>Year</th> -->
        <!-- <th>Timestamp</th> -->
    </tr>

    <?php foreach ($logs as $log): ?>
        <tr>
            <!-- <td><?php echo $log['updated_row']['id'] ?? '-'; ?></td> -->
            <td><?php echo htmlspecialchars($ships[$log['updated_row']['ship_id']]['name'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($areas[$ships[$log['updated_row']['ship_id']]['area_id']] ?? '-'); ?></td>
            <td><?php echo $log['updated_row']['name'] ?? '-'; ?></td>
            <td><?php echo $log['updated_row']['passport_number'] ?? '-'; ?></td>
            <td><?php echo $log['updated_row']['entry_date'] ?? '-'; ?></td>
            <td><?php echo highlightChange($log['updated_row']['type'] ?? '-'); ?></td>
            <td><?php echo highlightChange($log['updated_row']['start_date'] ?? '-'); ?></td>
            <td><?php echo highlightChange($log['updated_row']['disembark_date'] ?? '-'); ?></td>
            <td><?php echo $log['updated_row']['ship_fee'] ?? '-'; ?></td>
            <td><?php echo highlightChange($log['updated_row']['moving_fee'] ?? '-'); ?></td>
            <td><?php echo $log['updated_row']['outstanding_amount'] ?? '-'; ?></td>
            <!-- <td><?php echo $log['updated_row']['refund_amount'] ?? '-'; ?></td> -->
            <!-- <td><?php echo $log['updated_row']['request_status'] ?? '-'; ?></td> -->
            <!-- <td><?php echo $log['updated_row']['note'] ?? '-'; ?></td> -->
            <td><?php echo $log['updated_row']['created_at'] ?? '-'; ?></td>
            <td><?php echo highlightChange($log['updated_row']['updated_at'] ?? '-'); ?></td>
            <!-- <td><?php echo $log['updated_row']['year'] ?? '-'; ?></td> -->
            <!-- <td><?php echo $log['timestamp'] ?? '-'; ?></td> -->
        </tr>
    <?php endforeach; ?>
</table>

<?php
// Hàm làm nổi bật dữ liệu thay đổi (có dấu * phía trước)
function highlightChange($value) {
    if (strpos($value, '*') === 0) {
        return '<span class="highlight">' . htmlspecialchars(substr($value, 1)) . '</span>';
    }
    return htmlspecialchars($value);
}
?>

</body>
</html>

<?php
session_start();
include '../db/connect.php';

// Xử lý đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['password'])) {
    if ($_POST['password'] === "123123") {
        $_SESSION['logged_in'] = true;
    } else {
        $error = "Incorrect password!";
    }
}

// Nếu chưa đăng nhập, hiển thị form nhập mật khẩu
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to View Log</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Enter Password to View Log</h2>
        <form method="POST" class="col-md-4 mx-auto mt-4">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</body>
</html>
<?php
exit;
endif;

// Lấy dữ liệu log nếu đã đăng nhập
$sql = "SELECT ch.id, ch.crew_id, cm.name AS crew_name, ch.column_name, 
               ch.old_value, ch.new_value, ch.updated_by, ch.updated_at 
        FROM crew_history ch
        JOIN crew_members cm ON ch.crew_id = cm.id
        ORDER BY ch.updated_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seaman Update Log</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Seaman Update Log</h2>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Seaman Name</th>
                    <th>Column Changed</th>
                    <th>Old Value</th>
                    <th>New Value</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['crew_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['column_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['old_value']); ?></td>
                        <td><?php echo htmlspecialchars($row['new_value']); ?></td>
                        <td><?php echo $row['updated_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
    </div>
</body>
</html>

<?php
session_start();
include '../db/connect.php';

// Kiểm tra nếu chưa đăng nhập thì chuyển hướng về index.php
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../view/css/income_manage.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Income Management</a>
    </div>
</nav>

<!-- Nội dung chính -->
<div class="content-container">
    <!-- Thanh tìm kiếm + Tổng số -->
    <div class="d-flex justify-content-between align-items-center search-container">
        <input type="text" id="searchInput" class="form-control" placeholder="Search...">
        <div id="totalBox" class="total-box">SUM: 0</div>
    </div>

    <!-- Danh sách -->
    <div class="table-container">
        <div id="container"></div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
                <div id="modalTitleContainer" class="modal-title-container">
                    <h5 id="modalTitle" class="modal-title">Thông tin khu vực</h5>
                </div>
                <span id="shipCount" class="badge bg-secondary ms-2">0</span>
                <input type="text" id="searchShip" class="form-control ms-3" placeholder="Search..." style="max-width: 240px;">
            </div>
            <div class="modal-body">
                <div id="modalBody"></div>
                <div class="ship-list-container">
                    <div id="shipList" class="ship-list"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="../view/js/income_manage.js"></script>
<script src="../view/js/listship.js"></script>
<script src="../view/js/detail_ship.js"></script>
</body>
</html>

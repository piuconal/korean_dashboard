<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-image: url("../img/bg.jpg");
            background-size: cover;
            background-repeat: no-repeat;
        }
        .search-container {
            margin: 20px auto;
            max-width: 600px;
        }
        .content-container {
            width: 100%;
            padding: 20px;
        }
        .table-container {
            max-height: 520px; /* Giới hạn chiều cao */
            overflow-y: auto; /* Thêm thanh cuộn */
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }
        .box {
            display: inline-block;
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            background-color: lightblue;
            font-weight: bold;
            white-space: nowrap;
        }
        .total-box {
            padding: 10px 15px;
            border-radius: 8px;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            min-width: 120px;
            text-align: center;
            margin-left: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
    </style>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="../view/js/income_manage.js"></script>
</body>
</html>

<?php
session_start();
include '../db/connect.php';

// Kiểm tra nếu chưa đăng nhập thì chuyển hướng về index.php
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Lấy tên tàu từ URL
$ship_name = isset($_GET['ship_name']) ? urldecode($_GET['ship_name']) : "Không xác định";

// Truy vấn danh sách thuyền viên của tàu này
$stmt = $conn->prepare("SELECT * FROM crew_members WHERE ship_id = (SELECT id FROM ships WHERE name = ? LIMIT 1)");
$stmt->bind_param("s", $ship_name);
$stmt->execute();
$result = $stmt->get_result();
$seamen = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($ship_name); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
            color: #fff !important;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px 20px;
            background: linear-gradient(45deg, #007bff, #00c8ff);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: 0.3s;
        }

        .btn-add {
            font-size: 1.2rem;
            font-weight: bold;
            color: white;
            background: linear-gradient(135deg, #28a745, #218838);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn-add:hover {
            color: yellow;
            transform: scale(1.05);
        }

        table {
            margin-top: 20px;
        }

        thead {
            background-color: #007bff;
            color: white;
        }
        
        tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><?php echo htmlspecialchars($ship_name); ?></a>
    </div>
</nav>

<div class="container-fluid mt-4">
    <!-- Nút thêm thuyền viên -->
    <div class="d-flex justify-content-between align-items-center">
        <h3>List Seaman</h3>
        <button class="btn btn-add" id="addSeamanBtn">
            <i class="fas fa-user-plus"></i> Add Seaman
        </button>
    </div>

    <!-- Bảng danh sách thuyền viên -->
    <table style="font-size: 22px; " class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>이름</th>
                <th>여권번호</th>
                <th>입국일</th>
                <th>형식</th>
                <th>시작일</th>
                <th>하선일</th>
                <th>선관비</th>
                <th>이동비</th>
                <th>미납금액</th>
                <th>환불금액</th>
                <th>청구</th>
                <th>비교</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($seamen)) : ?>
                <?php foreach ($seamen as $seaman) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($seaman['name']); ?></td>
                        <td><?php echo htmlspecialchars($seaman['passport_number']); ?></td>
                        <td><?php echo htmlspecialchars($seaman['entry_date']); ?></td>
                        <td><?php echo htmlspecialchars($seaman['type']); ?></td>
                        <td><?php echo htmlspecialchars($seaman['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($seaman['disembark_date']); ?></td>
                        <td><?php echo number_format($seaman['ship_fee']); ?> </td>
                        <td><?php echo number_format($seaman['moving_fee']); ?> </td>
                        <td style="color: red;"><?php echo number_format($seaman['outstanding_amount']); ?> </td>
                        <td style="color: green;"><?php echo number_format($seaman['refund_amount']); ?> </td>
                        <td><?php echo number_format($seaman['request_status']); ?> </td>
                        <td><?php echo htmlspecialchars($seaman['note']); ?>🧾</td>
                        <td>
                            <button style="font-size: 20px" class="btn btn-warning btn-sm editSeamanBtn" data-id="<?php echo $seaman['id']; ?>">
                                <i class="fas fa-edit"></i> 수정
                            </button>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="11" class="text-center">There are no crew members on board this ship.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Danh Sách Thuyền Viên -->
<div class="modal fade" id="addSeamanModal" tabindex="-1" aria-labelledby="addSeamanLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSeamanLabel">List from Excel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Input tìm kiếm -->
        <input style="height: 50px;" type="text" id="searchSeaman" class="form-control mb-3" placeholder="Search...">

        <!-- Bảng danh sách -->
        <table style="font-size: 25px; " class="table table-bordered">
          <thead>
            <tr>
              <th>외국인선원명</th>
              <th>여권번호</th>
              <th>입국일</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="seamanList">
            <tr><td colspan="4" class="text-center">Đang tải dữ liệu...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="../view/js/list_seaman.js"></script>

</body>
</html>

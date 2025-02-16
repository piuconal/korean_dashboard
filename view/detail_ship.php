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

// Lấy trạng thái outstanding_status của tàu
$stmt = $conn->prepare("SELECT outstanding_status FROM ships WHERE name = ?");
$stmt->bind_param("s", $ship_name);
$stmt->execute();
$stmt->bind_result($outstanding_status);
$stmt->fetch();
$stmt->close();

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
        
        th, td{
            text-align: center;
        }
        .shipFeeCheckbox, .moving-fee-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #28a745; /* Màu xanh lá cây */
            transform: scale(1.2); /* Tăng kích thước */
        }

    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <?php echo htmlspecialchars($ship_name); ?>
            <?php if (isset($outstanding_status) && $outstanding_status == 1): ?>
                <span>❗</span>
            <?php else: ?>
                <span></span>
            <?php endif; ?>
        </a>
    </div>
</nav>


<div class="container-fluid mt-4">
    <!-- Nút thêm thuyền viên -->
    <div class="d-flex justify-content-between align-items-center">
        <h3>
            <span class="text-danger" id="totalPendingAmount" style="font-size: 30px;"></span>
        </h3>
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
                        <td>
                            <?php echo ($seaman['disembark_date'] !== '0000-00-00') ? htmlspecialchars($seaman['disembark_date']) : ''; ?>
                        </td>

                        <td>
                            <?php if (!empty($seaman['start_date'])) : ?>
                                <?php echo date('Y', strtotime($seaman['start_date'])); ?>
                                <input type="checkbox" class="shipFeeCheckbox" data-id="<?php echo $seaman['id']; ?>"
                                    <?php echo ($seaman['ship_fee'] == 0) ? 'checked' : ''; ?>>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php echo number_format($seaman['moving_fee']); ?>
                            <?php if ($seaman['moving_fee'] != 0 || $seaman['moving_fee'] === 0): ?>
                                <input type="checkbox" class="moving-fee-checkbox" data-id="<?php echo $seaman['id']; ?>" data-fee="<?php echo $seaman['moving_fee']; ?>"
                                    <?php echo ($seaman['moving_fee'] == 0) ? 'checked' : ''; ?>>
                            <?php endif; ?>
                        </td>

                        <td class="outstanding-amount" style="color: red;" data-id="<?php echo $seaman['id']; ?>">
                            <?php echo number_format($seaman['outstanding_amount']); ?>
                        </td>

                        <td style="color: green;">
                            <?php
                            if ($seaman['ship_fee'] == 0 && !empty($seaman['start_date']) && !empty($seaman['disembark_date']) && $seaman['disembark_date'] !== '0000-00-00') {
                                $start_date = new DateTime($seaman['start_date']);
                                $disembark_date = new DateTime($seaman['disembark_date']);
                                $days = $disembark_date->diff($start_date)->days; // Số ngày chênh lệch

                                $refund_amount = 132000 - (132000 / 365 * $days);
                                echo number_format($refund_amount);
                            } else {
                                echo "";
                            }
                            ?>
                        </td>

                        <td>
                            <img style="width: 40px; cursor: pointer;" 
                                src="../img/mail<?php echo $seaman['request_status']; ?>.png" 
                                alt="Status <?php echo $seaman['request_status']; ?>" 
                                class="toggle-status" 
                                data-id="<?php echo $seaman['id']; ?>" 
                                data-status="<?php echo $seaman['request_status']; ?>">
                        </td>

                        <td>
                            <img class="edit-note" style="width: 35px; cursor: pointer;" src="../img/note.png" 
                                data-id="<?php echo $seaman['id']; ?>" 
                                alt="Edit Note">
                        </td>

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

<!-- Toast thông báo -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
    <div id="statusToast" class="toast hide bg-success text-white" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body">Cập nhật trạng thái thành công!</div>
    </div>
</div>

<!-- Modal nhập ghi chú -->
<div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Đổi từ modal-sm thành modal-lg -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="noteModalLabel">View & Edit Notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <textarea id="noteContent" class="form-control" rows="5" style="font-size: 1.4rem; padding: 10px;"></textarea>
                <input type="hidden" id="seamanId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-lg" id="saveNoteBtn">Save</button> <!-- Nút to hơn -->
            </div>
        </div>
    </div>
</div>

<!-- Modal chỉnh sửa thuyền viên -->
<div class="modal fade" id="editSeamanModal" tabindex="-1" aria-labelledby="editSeamanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Modal rộng hơn -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editSeamanModalLabel">Edit Seaman Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editSeamanForm">
                    <input type="hidden" id="editSeamanId">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editSeamanType" class="form-label">형식</label>
                            <select class="form-select" id="editSeamanType">
                                <option value="신규">신규</option>
                                <option value="근변">근변</option>
                                <option value="재입국">재입국</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="editSeamanStartDate" class="form-label">시작일</label>
                            <input type="date" class="form-control" id="editSeamanStartDate">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="editSeamanDisembarkDate" class="form-label">하선일</label>
                            <input type="date" class="form-control" id="editSeamanDisembarkDate">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="editSeamanMovingFee" class="form-label">이동비</label>
                            <input type="number" class="form-control" id="editSeamanMovingFee">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveEditSeaman">Save</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="../view/js/list_seaman.js"></script>
<script src="../view/js/update_mail.js"></script>
<script src="../view/js/update_note.js"></script>
<script src="../view/js/edit_seaman.js"></script>
<script src="../view/js/ship_fee.js"></script>
<script src="../view/js/out_amount.js"></script>
<script src="../view/js/moving_fee.js"></script>
<script src="../view/js/total_debt.js"></script>
<script src="../view/js/sort_colsix.js"></script>

</body>
</html>

<?php
session_start();
include '../db/connect.php';

// Kiểm tra nếu chưa đăng nhập thì chuyển hướng về index.php
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Lấy tên tàu từ URL
$ship_name = isset($_GET['ship_name']) ? urldecode($_GET['ship_name']) : '';

if (empty($ship_name)) {
    die("Lỗi: Không xác định được tên tàu.");
}

// Truy vấn lấy thông tin tàu, bao gồm registration_fee
$query = "SELECT id, ship_code, registration_fee, registration_fee_status, outstanding_status FROM ships WHERE name = ? LIMIT 1";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Lỗi truy vấn: " . $conn->error);
}

$stmt->bind_param("s", $ship_name);
$stmt->execute();
$stmt->bind_result($ship_id, $ship_code, $registration_fee, $status, $outstanding_status);
$stmt->fetch();
$stmt->close();

// Kiểm tra nếu không tìm thấy tàu
if (!$ship_id) {
    die("Lỗi: Không tìm thấy thông tin tàu.");
}

// Truy vấn danh sách thuyền viên của tàu này
$query = "SELECT * FROM crew_members WHERE ship_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Lỗi truy vấn lấy danh sách thuyền viên: " . $conn->error);
}

$stmt->bind_param("i", $ship_id);
$stmt->execute();
$result = $stmt->get_result();
$seamen = $result->fetch_all(MYSQLI_ASSOC);
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
    <!-- Add this to the head section of your HTML file -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        .shipFeeCheckbox, .moving-fee-checkbox, .refund-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #28a745; /* Màu xanh lá cây */
            transform: scale(1.2); /* Tăng kích thước */
        }
        
        .modal-body {
            max-height: 80vh; /* Giới hạn chiều cao tối đa là 70% chiều cao của viewport */
            overflow-y: auto; /* Bật thanh cuộn */
        }

    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#" data-ship-id="<?php echo htmlspecialchars($ship_id); ?>">
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
    <!-- Tiền đăng ký + Checkbox -->
    <div class="d-flex justify-content-between align-items-center">
        <h3>
            <span id="totalPendingAmount" style="font-size: 30px;"></span>
        </h3>
        <div style="font-size: 30px; margin-top: -8px;" class="d-flex align-items-center">
            <label for="registrationFee" class="me-2">Tiền đăng ký:</label>
            <input type="number" id="registrationFee" class="form-control me-2" 
                value="<?php echo htmlspecialchars($registration_fee); ?>" 
                style="width: 160px; font-size: 30px;">
            <!-- Nút Lưu (ẩn mặc định) -->
            <button id="saveButton" class="btn btn-success ms-2" style="margin-right: 10px;display: none;">Save</button>

            <input type="checkbox" id="confirmFee" class="form-check-input" 
                style="width: 30px; height: 30px;" 
                data-ship-code="<?php echo htmlspecialchars($ship_code); ?>" 
                <?php echo ($status == 1) ? "checked" : ""; ?>>
        </div>
        <button class="btn btn-add ms-3" id="addSeamanBtn">
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
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <!-- Year Text -->
                                    <span class="mr-3"><?php echo $seaman['year']; ?></span>
                                    
                                    <!-- "+" Button -->
                                    <button type="button" class="btn btn-sm btn-outline-primary updateYearBtn" data-id="<?php echo $seaman['id']; ?>">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    
                                    <!-- Checkbox -->
                                    <input type="checkbox" class="shipFeeCheckbox ml-3" data-id="<?php echo $seaman['id']; ?>"
                                        <?php echo ($seaman['ship_fee'] == 0) ? 'checked' : ''; ?>>
                                </div>
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
                            $refund_amount = 0;
                            $base_amount = 132000;

                            if ($seaman['ship_fee'] == 0 && !empty($seaman['start_date']) && !empty($seaman['disembark_date']) && $seaman['disembark_date'] !== '0000-00-00') {
                                $start_date = new DateTime($seaman['start_date']);
                                $disembark_date = new DateTime($seaman['disembark_date']);
                                
                                $interval = $start_date->diff($disembark_date);
                                $months = $interval->m; // Số tháng
                                $days = $interval->d;   // Số ngày

                                // Tính tiền hoàn lại
                                $refund_amount = $base_amount - ((11000 * $months) + (360 * ($days+1)));

                                echo number_format($refund_amount);
                            }
                            ?>
                            <?php if (!empty($seaman['disembark_date']) && $seaman['disembark_date'] !== '0000-00-00'): ?>
                                <input type="checkbox" class="refund-checkbox" data-id="<?= $seaman['id'] ?>" <?= ($seaman['refund_amount'] == 0) ? 'checked' : '' ?>>
                            <?php endif; ?>
                        </td>

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

                        <!-- Cột xóa mới -->
                        <td>
                            <button style="font-size: 20px" class="btn btn-danger btn-sm deleteSeamanBtn" data-id="<?php echo $seaman['id']; ?>">
                                <i class="fas fa-trash"></i>
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
        <h5 class="modal-title" id="addSeamanLabel">Danh sách thuyền viên</h5>
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
<script src="../view/js/update_refund.js"></script>
<script src="../view/js/update_year.js"></script>
<script src="../view/js/delete_seaman.js"></script>
<script src="../view/js/update_registration_fee.js"></script>
<script src="../view/js/update_registration_fee_status.js"></script>
</body>
</html>

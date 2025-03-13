function updateOutstandingStatus(shipId, status) {
  const data = { ship_id: shipId, outstanding_status: status };
  console.log("Dữ liệu gửi đi:", JSON.stringify(data)); // Thêm log để kiểm tra dữ liệu trước khi gửi

  fetch("update_outstanding_status.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) => response.text())
    .then((data) => console.log("Phản hồi từ server:", data))
    .catch((error) => console.error("Lỗi khi cập nhật trạng thái:", error));
}

function checkPendingAmount() {
  // Lấy shipname từ DOM
  const shipname = document.querySelector(".navbar-brand");
  if (!shipname) {
    console.log("Không tìm thấy phần tử .navbar-brand");
    return;
  }

  // Lấy số tiền từ thẻ span
  const amountElement = document.getElementById("totalPendingAmount");
  if (!amountElement) {
    console.log("Không tìm thấy phần tử #totalPendingAmount");
    return;
  }

  // Lấy giá trị số tiền từ span (loại bỏ phần chữ và ký tự "원")
  const amountText = amountElement.textContent;
  console.log("Chuỗi số tiền ban đầu:", amountText);

  // Sử dụng biểu thức chính quy để lấy số từ chuỗi
  const match = amountText.match(/[\d,.]+/);
  const amount = match ? parseFloat(match[0].replace(/[,.]/g, "")) : 0;
  console.log("Số tiền đã tìm thấy:", amount);

  // Lấy text từ shipname
  let text = shipname.textContent;
  console.log("Nội dung ban đầu:", text);

  // Lấy ID của tàu (có thể lấy từ thuộc tính data-attribute)
  const shipId = shipname.dataset.shipId;

  // Cập nhật nội dung và gọi API dựa trên số tiền
  if (amount > 0) {
    if (!text.includes("❗")) {
      shipname.textContent = text + " ❗";
      console.log("Cập nhật nội dung:", shipname.textContent);
      updateOutstandingStatus(shipId, 1); // Cập nhật trạng thái outstanding = 1
    }
  } else {
    if (text.includes("❗")) {
      shipname.textContent = text.replace(" ❗", "");
      console.log("Bỏ dấu ❗:", shipname.textContent);
      updateOutstandingStatus(shipId, 0); // Cập nhật trạng thái outstanding = 0
    }
  }
}

// Kiểm tra khi trang được load
document.addEventListener("DOMContentLoaded", checkPendingAmount);

// Kiểm tra khi có click bất kỳ trên trang
document.addEventListener("click", checkPendingAmount);

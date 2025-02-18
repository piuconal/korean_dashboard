document.addEventListener("DOMContentLoaded", function () {
  // Xử lý sự kiện thay đổi cho mỗi checkbox
  document.querySelectorAll(".shipFeeCheckbox").forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      let seamanId = this.dataset.id;
      let newShipFee = this.checked ? 0 : 132000;

      // Lấy start_date từ cột 7 (index 6 trong JS)
      let startDateElement = this.closest("tr").children[6];
      let startDate = startDateElement
        ? startDateElement.textContent.trim()
        : "";

      console.log("Gửi start_date:", startDate); // Debug kiểm tra dữ liệu

      fetch("update_ship_fee.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${seamanId}&ship_fee=${newShipFee}&start_date=${encodeURIComponent(
          startDate
        )}`,
      })
        .then((response) => response.json()) // Chờ nhận dữ liệu JSON
        .then((data) => {
          console.log("Server trả về:", data); // Debug phản hồi từ server

          if (data.success) {
            showToast(`Cập nhật thành công!`, "success");

            // Kiểm tra xem phần tử có tồn tại trước khi cập nhật nội dung
            let noteDisplayElement = document.getElementById("noteDisplay");
            if (noteDisplayElement) {
              noteDisplayElement.innerText = data.updated_note;
            }

            setTimeout(() => location.reload(), 1000);
          } else {
            showToast(`Cập nhật thất bại: ${data.message}`, "danger");
          }
        })
        .catch((error) => {
          console.error("Lỗi khi cập nhật:", error);
          showToast("Lỗi xảy ra khi cập nhật!", "danger");
          setTimeout(() => location.reload(), 1000);
        });
    });
  });
});

// Hàm hiển thị thông báo
function showToast(message, type) {
  const toast = document.createElement("div");
  toast.classList.add("toast", type);
  toast.innerText = message;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3000); // Xóa toast sau 3 giây
}

document.addEventListener("DOMContentLoaded", function () {
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
        .then((response) => response.json())
        .then((data) => {
          console.log("Server trả về:", data); // Debug phản hồi từ server
          if (data.success) {
            showToast(`Update successful!`, "success");
            setTimeout(() => location.reload(), 1000);
          } else {
            showToast(`Cập nhật thất bại: ${data.message}`, "danger");
          }
        })
        .catch((error) => {
          console.error("Lỗi khi cập nhật:", error);
          showToast("Hệ thống lỗi, thử lại!", "danger");
        });
    });
  });
});

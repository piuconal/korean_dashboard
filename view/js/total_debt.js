document.addEventListener("DOMContentLoaded", function () {
  function calculateTotal() {
    let totalOutstanding = 0;
    let totalRefunded = 0;

    document.querySelectorAll("tbody tr").forEach((row) => {
      let outstanding =
        parseFloat(row.cells[8]?.innerText.replace(/,/g, "")) || 0;
      let refundedText =
        parseFloat(row.cells[9]?.innerText.replace(/,/g, "")) || 0;

      // Tìm checkbox trong cùng hàng
      let checkbox = row.querySelector(".refund-checkbox");

      // Nếu checkbox chưa được chọn, cộng tổng tiền refunded
      if (checkbox && !checkbox.checked) {
        totalRefunded += refundedText;
      }

      totalOutstanding += outstanding;
    });

    let totalPending = totalOutstanding - totalRefunded;

    // Lấy giá trị tiền đăng ký
    let registrationFeeInput = document.getElementById("registrationFee");
    let confirmFeeCheckbox = document.getElementById("confirmFee");

    if (registrationFeeInput && confirmFeeCheckbox) {
      let registrationFee = parseFloat(registrationFeeInput.value) || 0;

      // Nếu checkbox được chọn, cộng thêm tiền đăng ký
      if (confirmFeeCheckbox.checked) {
        totalPending += registrationFee;
      }
    }

    let totalDisplay = document.getElementById("totalPendingAmount");

    if (totalDisplay) {
      if (totalPending >= 0) {
        totalDisplay.innerText =
          "미수금 총액: " +
          new Intl.NumberFormat().format(totalPending) +
          " 원";
        totalDisplay.style.color = "green";
      } else {
        totalDisplay.innerText =
          "총 환불 금액: " +
          new Intl.NumberFormat().format(Math.abs(totalPending)) +
          " 원";
        totalDisplay.style.color = "red";
      }
    }
  }

  calculateTotal(); // Gọi khi tải trang

  // Gắn sự kiện khi checkbox thay đổi hoặc nhập tiền đăng ký
  let confirmFeeCheckbox = document.getElementById("confirmFee");
  let registrationFeeInput = document.getElementById("registrationFee");

  if (confirmFeeCheckbox) {
    confirmFeeCheckbox.addEventListener("change", calculateTotal);
  }

  if (registrationFeeInput) {
    registrationFeeInput.addEventListener("input", calculateTotal);
  }
});

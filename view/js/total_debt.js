document.addEventListener("DOMContentLoaded", function () {
  function calculateTotal() {
    let totalOutstanding = 0;
    let totalRefunded = 0;

    document.querySelectorAll("tbody tr").forEach((row) => {
      let outstanding = parseFloat(
        row.cells[8]?.innerText.replace(/,/g, "") || 0
      );
      let refundedText = parseFloat(
        row.cells[9]?.innerText.replace(/,/g, "") || 0
      );

      // Tìm checkbox trong cùng hàng
      let checkbox = row.querySelector(".refund-checkbox");

      // Kiểm tra nếu checkbox tồn tại và được checked mới cộng giá trị cột 9
      if (checkbox && !checkbox.checked) {
        totalRefunded += refundedText;
      }

      totalOutstanding += outstanding;
    });

    let totalPending = totalOutstanding - totalRefunded;
    let totalDisplay = document.getElementById("totalPendingAmount");

    if (totalPending >= 0) {
      totalDisplay.innerText =
        "미수금 총액: " + new Intl.NumberFormat().format(totalPending) + " 원";
      totalDisplay.style.color = "green"; // Màu đỏ nếu chưa thu
    } else {
      totalDisplay.innerText =
        "총 환불 금액: " +
        new Intl.NumberFormat().format(Math.abs(totalPending)) +
        " 원";
      totalDisplay.style.color = "red"; // Màu xanh nếu phải trả
    }
  }

  calculateTotal(); // Gọi khi tải trang
});

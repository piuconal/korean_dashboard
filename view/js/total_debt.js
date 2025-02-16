document.addEventListener("DOMContentLoaded", function () {
  function calculateTotal() {
    let totalOutstanding = 0;
    let totalRefunded = 0;

    document.querySelectorAll("tbody tr").forEach((row) => {
      let outstanding = parseFloat(
        row.cells[8]?.innerText.replace(/,/g, "") || 0
      );
      let refunded = parseFloat(row.cells[9]?.innerText.replace(/,/g, "") || 0);

      totalOutstanding += outstanding;
      totalRefunded += refunded;
    });

    let totalPending = totalOutstanding - totalRefunded;

    document.getElementById("totalPendingAmount").innerText = " 미수금 총액: " +
      new Intl.NumberFormat().format(totalPending) + " 원";
  }

  calculateTotal(); // Gọi khi tải trang
});

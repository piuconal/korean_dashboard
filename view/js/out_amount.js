document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".moving-fee-checkbox").forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      let seamanId = this.dataset.id;
      let fee = parseInt(this.dataset.fee);
      let isChecked = this.checked;

      // Tìm ô hiển thị tổng số tiền của thuyền viên đó
      let totalAmountCell = document.querySelector(
        `.outstanding-amount[data-id='${seamanId}']`
      );
      let currentAmount = parseInt(totalAmountCell.innerText.replace(/,/g, "")); // Chuyển về số

      if (isChecked) {
        totalAmountCell.innerText = new Intl.NumberFormat().format(
          currentAmount - fee
        );
      } else {
        totalAmountCell.innerText = new Intl.NumberFormat().format(
          currentAmount + fee
        );
      }
    });
  });
});

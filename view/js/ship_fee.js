document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".shipFeeCheckbox").forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      let seamanId = this.dataset.id;
      let newShipFee = this.checked ? 0 : 132000; // Tick thì về 0, bỏ tick về 132000

      fetch("update_ship_fee.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${seamanId}&ship_fee=${newShipFee}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            showToast("Ship fee updated successfully!", "success");
            setTimeout(() => location.reload(), 1000); // Reload sau khi cập nhật
          } else {
            showToast(`Update failed: ${data.message}`, "danger");
          }
        })
        .catch((error) => {
          console.error("Error updating ship fee:", error);
          showToast("System error, please try again!", "danger");
        });
    });
  });

  function showToast(message, type) {
    let toast = document.createElement("div");
    toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = "1050";
    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    `;
    document.body.appendChild(toast);
    let bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    setTimeout(() => {
      toast.remove();
    }, 3000);
  }
});

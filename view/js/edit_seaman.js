document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".editSeamanBtn").forEach((button) => {
    button.addEventListener("click", function () {
      let seamanId = this.dataset.id;

      fetch(`get_seaman.php?id=${seamanId}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            document.getElementById("editSeamanId").value = data.seaman.id;
            document.getElementById("editSeamanType").value = data.seaman.type;
            document.getElementById("editSeamanStartDate").value =
              data.seaman.start_date;
            document.getElementById("editSeamanDisembarkDate").value =
              data.seaman.disembark_date;
            document.getElementById("editSeamanMovingFee").value =
              data.seaman.moving_fee;

            let editModal = new bootstrap.Modal(
              document.getElementById("editSeamanModal")
            );
            editModal.show();
          } else {
            alert("Seaman information not found!");
          }
        });
    });
  });

  // 🚀 Handle "Save" button click
  document
    .getElementById("saveEditSeaman")
    .addEventListener("click", function () {
      let seamanId = document.getElementById("editSeamanId").value;
      let type = document.getElementById("editSeamanType").value;
      let start_date = document.getElementById("editSeamanStartDate").value;
      let disembark_date =
        document.getElementById("editSeamanDisembarkDate").value || ""; // Cho phép rỗng
      let moving_fee = document.getElementById("editSeamanMovingFee").value;

      let formData = new FormData();
      formData.append("id", seamanId);
      formData.append("type", type);
      formData.append("start_date", start_date);
      formData.append("disembark_date", disembark_date);
      formData.append("moving_fee", moving_fee);

      fetch("update_seaman.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            showToast("Update successful!", "success");
            setTimeout(() => location.reload(), 1000);
          } else {
            showToast(`Update error: ${data.message}`, "danger");
          }
        })
        .catch((error) => {
          console.error("Error sending data:", error);
          showToast("System error, please try again!", "danger");
        });
    });

  // 🟢 Show Bootstrap toast notification
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

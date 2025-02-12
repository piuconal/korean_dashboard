document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".toggle-status").forEach((img) => {
    img.addEventListener("dblclick", function () {
      let seamanId = this.getAttribute("data-id");
      let currentStatus = this.getAttribute("data-status");
      let newStatus = currentStatus == "0" ? "1" : "0";
      let imgElement = this;

      // Gửi AJAX request để cập nhật database
      fetch("update_mail.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${seamanId}&status=${newStatus}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Cập nhật ảnh và data-status
            imgElement.src = `../img/mail${newStatus}.png`;
            imgElement.setAttribute("data-status", newStatus);

            // Hiển thị thông báo Bootstrap Toast
            showToast("Status update successful!", "success");
          } else {
            showToast("Update failed!", "danger");
          }
        })
        .catch((error) => {
          console.error("Lỗi cập nhật:", error);
          showToast("Lỗi kết nối!", "danger");
        });
    });
  });
});

// ✅ Hàm hiển thị Toast Bootstrap
function showToast(message, type) {
  let toastElement = document.getElementById("statusToast");
  let toastBody = toastElement.querySelector(".toast-body");

  // Cập nhật nội dung thông báo
  toastBody.textContent = message;

  // Cập nhật màu nền theo loại (success/danger)
  toastElement.classList.remove("bg-success", "bg-danger");
  toastElement.classList.add(`bg-${type}`);

  // Hiển thị Toast
  let toast = new bootstrap.Toast(toastElement);
  toast.show();
}

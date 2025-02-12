document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".edit-note").forEach((img) => {
    img.addEventListener("click", function () {
      let seamanId = this.getAttribute("data-id");

      // Gửi request để lấy ghi chú từ cùng một API
      fetch(`update_note.php?id=${seamanId}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            document.getElementById("noteContent").value = data.note || "";
            document.getElementById("seamanId").value = seamanId;

            let noteModal = new bootstrap.Modal(
              document.getElementById("noteModal")
            );
            noteModal.show();
          } else {
            showToast("Không thể tải ghi chú!", "danger");
          }
        })
        .catch((error) => {
          console.error("Lỗi khi lấy ghi chú:", error);
          showToast("Lỗi kết nối!", "danger");
        });
    });
  });

  // Cập nhật ghi chú vào DB khi nhấn "Cập nhật"
  document.getElementById("saveNoteBtn").addEventListener("click", function () {
    let seamanId = document.getElementById("seamanId").value;
    let newNote = document.getElementById("noteContent").value;

    fetch("update_note.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `id=${seamanId}&note=${encodeURIComponent(newNote)}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          let noteModal = bootstrap.Modal.getInstance(
            document.getElementById("noteModal")
          );
          noteModal.hide();
          showToast("Notes updated!", "success");
        } else {
          showToast("Error while updating!", "danger");
        }
      })
      .catch((error) => {
        console.error("Lỗi cập nhật:", error);
        showToast("Lỗi kết nối!", "danger");
      });
  });
});

// ✅ Hiển thị thông báo Bootstrap Toast
function showToast(message, type) {
  let toastElement = document.getElementById("statusToast");
  let toastBody = toastElement.querySelector(".toast-body");

  toastBody.textContent = message;
  toastElement.classList.remove("bg-success", "bg-danger");
  toastElement.classList.add(`bg-${type}`);

  let toast = new bootstrap.Toast(toastElement);
  toast.show();
}

document.addEventListener("DOMContentLoaded", function () {
  const registrationFeeInput = document.getElementById("registrationFee");
  const saveButton = document.getElementById("saveButton");
  const confirmFeeCheckbox = document.getElementById("confirmFee");

  // Sự kiện khi nhập giá trị vào ô input
  registrationFeeInput.addEventListener("input", function () {
    if (parseFloat(this.value) > 0) {
      saveButton.style.display = "inline-block"; // Hiện nút Lưu
    } else {
      saveButton.style.display = "none"; // Ẩn nút Lưu
    }
  });

  // Sự kiện khi nhấn vào checkbox
  confirmFeeCheckbox.addEventListener("click", function () {
    updateRegistrationFee();
  });

  // Sự kiện khi nhấn nút Lưu
  saveButton.addEventListener("click", function () {
    updateRegistrationFee();
  });

  // Hàm cập nhật phí đăng ký
  function updateRegistrationFee() {
    const shipCode = confirmFeeCheckbox.getAttribute("data-ship-code");
    const status = confirmFeeCheckbox.checked ? 1 : 0;
    const fee = registrationFeeInput.value;

    fetch("update_registration_fee.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ ship_code: shipCode, fee: fee, status: status }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Cập nhật thành công!");
          location.reload();
        } else {
          alert("Lỗi cập nhật!");
          console.error("Lỗi cập nhật:", data.error);
        }
      })
      .catch((error) => console.error("Lỗi kết nối:", error));
  }
});

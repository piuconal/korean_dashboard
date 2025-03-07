document.addEventListener("DOMContentLoaded", function () {
  const confirmFeeCheckbox = document.getElementById("confirmFee");

  confirmFeeCheckbox.addEventListener("change", function () {
    const shipCode = this.getAttribute("data-ship-code");
    const status = this.checked ? 1 : 0;
    const fee = document.getElementById("registrationFee").value;

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
          console.log("Cập nhật thành công!");
          location.reload();
        } else {
          console.error("Lỗi cập nhật:", data.error);
        }
      })
      .catch((error) => console.error("Lỗi kết nối:", error));
  });
});

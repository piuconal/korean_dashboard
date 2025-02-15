document.addEventListener("DOMContentLoaded", () => {
  document
    .getElementById("shipList")
    .addEventListener("click", function (event) {
      if (event.target.classList.contains("ship-box")) {
        let shipName = event.target.textContent.replace(/❗/g, "").trim();

        // Xóa tất cả khoảng trắng thừa giữa các từ (nếu có)
        shipName = shipName.replace(/\s+/g, " ");

        // Gửi request kiểm tra và thêm tàu vào DB
        fetch("add_ship.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `ship_name=${encodeURIComponent(shipName)}`,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success && data.redirect) {
              window.open(data.redirect, "_blank"); // Mở tab mới
            } else {
              alert("Lỗi khi thêm tàu vào database!");
            }
          })
          .catch((error) => console.error("Lỗi:", error));
      }
    });
});

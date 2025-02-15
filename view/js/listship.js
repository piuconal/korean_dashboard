async function loadExcel() {
  if (window.excelData) return; // Tránh tải lại dữ liệu nhiều lần

  try {
    const response = await fetch("../excel/mydata.xlsx");
    const data = await response.arrayBuffer();
    const workbook = XLSX.read(data, { type: "array" });

    const sheetName = workbook.SheetNames[0];
    const sheet = workbook.Sheets[sheetName];
    const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

    window.excelData = jsonData; // Lưu dữ liệu vào biến toàn cục
    console.log("Dữ liệu Excel đã tải:", jsonData); // Debug dữ liệu
  } catch (error) {
    console.error("Lỗi khi tải file Excel:", error);
  }
}

loadExcel();

document.addEventListener("DOMContentLoaded", async () => {
  // Tải dữ liệu trạng thái outstanding từ server
  const outstandingStatus = await fetchOutstandingStatus();

  document
    .getElementById("container")
    .addEventListener("click", function (event) {
      if (event.target.classList.contains("box")) {
        const value = event.target.textContent;
        const boxColor = window.getComputedStyle(event.target).backgroundColor;

        const modalTitleContainer = document.getElementById(
          "modalTitleContainer"
        );
        const modalTitle = document.getElementById("modalTitle");
        const shipListContainer = document.getElementById("shipList");

        modalTitle.textContent = value;
        modalTitleContainer.style.backgroundColor = boxColor; // Đặt màu nền theo box
        shipListContainer.innerHTML = ""; // Xóa dữ liệu cũ

        if (!window.excelData) {
          console.error("Dữ liệu Excel chưa được tải!");
          return;
        }

        let shipSet = new Set();
        for (let i = 1; i < window.excelData.length; i++) {
          if (window.excelData[i][0] === value) {
            shipSet.add(window.excelData[i][1]); // Cột 2 chứa tên tàu
          }
        }

        const shipList = Array.from(shipSet);

        if (shipList.length > 0) {
          shipList.forEach((ship) => {
            const shipBox = document.createElement("div");
            shipBox.classList.add("ship-box");
            shipBox.textContent = ship;

            // Kiểm tra nếu tàu có outstanding_status = 1 thì thêm dấu !
            if (outstandingStatus[ship] === 1) {
              shipBox.textContent += "❗";
            }

            shipListContainer.appendChild(shipBox);
          });
        } else {
          shipListContainer.innerHTML =
            "<em>Không có tàu nào trong khu vực này.</em>";
        }

        let modal = new bootstrap.Modal(document.getElementById("infoModal"));
        modal.show();
      }
    });

  // Lọc danh sách tàu theo ô tìm kiếm
  document.getElementById("searchShip").addEventListener("input", function () {
    const searchValue = this.value.toLowerCase();
    const ships = document.querySelectorAll(".ship-box");
    ships.forEach((ship) => {
      ship.style.display = ship.textContent.toLowerCase().includes(searchValue)
        ? "inline-block"
        : "none";
    });
  });
});

// Hàm fetch dữ liệu outstanding_status từ PHP API
async function fetchOutstandingStatus() {
  try {
    const response = await fetch("get_outstanding_status.php");
    const data = await response.json();
    return data;
  } catch (error) {
    console.error("Lỗi khi tải trạng thái outstanding:", error);
    return {};
  }
}

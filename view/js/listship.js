document.addEventListener("DOMContentLoaded", () => {
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

        let shipList = [];
        for (let i = 1; i < window.excelData.length; i++) {
          if (window.excelData[i][0] === value) {
            shipList.push(window.excelData[i][1]); // Cột 2 chứa tên tàu
          }
        }

        if (shipList.length > 0) {
          shipList.forEach((ship) => {
            const shipBox = document.createElement("div");
            shipBox.classList.add("ship-box");
            shipBox.textContent = ship;
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
      if (ship.textContent.toLowerCase().includes(searchValue)) {
        ship.style.display = "inline-block";
      } else {
        ship.style.display = "none";
      }
    });
  });
});

async function loadExcel() {
  try {
    const response = await fetch("../excel/mydata.xlsx");
    const data = await response.arrayBuffer();
    const workbook = XLSX.read(data, { type: "array" });

    const sheetName = workbook.SheetNames[0];
    const sheet = workbook.Sheets[sheetName];
    const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

    window.excelData = jsonData; // Lưu dữ liệu vào biến toàn cục
    displayData(jsonData);
  } catch (error) {
    console.error("Lỗi khi tải file Excel:", error);
  }
}

loadExcel();

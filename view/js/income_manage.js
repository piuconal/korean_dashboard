async function loadExcel() {
  try {
    const response = await fetch("../excel/mydata.xlsx"); // Đường dẫn cố định
    const data = await response.arrayBuffer();
    const workbook = XLSX.read(data, { type: "array" });

    const sheetName = workbook.SheetNames[0];
    const sheet = workbook.Sheets[sheetName];
    const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

    displayData(jsonData);
  } catch (error) {
    console.error("Lỗi khi tải file Excel:", error);
  }
}

function displayData(data) {
  const container = document.getElementById("container");
  const totalBox = document.getElementById("totalBox");
  container.innerHTML = "";

  const headerIndex = data[0].indexOf("조합명");
  if (headerIndex === -1) {
    alert("Không tìm thấy cột '조합명'");
    return;
  }

  const uniqueValues = new Set();

  for (let i = 1; i < data.length; i++) {
    if (data[i][headerIndex]) {
      uniqueValues.add(data[i][headerIndex]);
    }
  }

  uniqueValues.forEach((value) => {
    const box = document.createElement("div");
    box.classList.add("box");
    box.textContent = value;
    container.appendChild(box);
  });

  // Cập nhật tổng số lượng
  totalBox.textContent = `SUM: ${uniqueValues.size}`;
}

loadExcel(); // Gọi hàm khi trang tải xong

document.getElementById("searchInput").addEventListener("input", function () {
  let input = this.value.toLowerCase();
  let items = document.querySelectorAll("#container .box");

  items.forEach((item) => {
    let text = item.textContent.toLowerCase();
    item.style.display = text.includes(input) ? "" : "none";
  });
});

// Function để đếm và cập nhật số lượng ship-box
function updateShipCount() {
  const shipList = document.getElementById("shipList");
  const shipCount = document.getElementById("shipCount");
  const count = shipList.getElementsByClassName("ship-box").length;
  shipCount.textContent = count;
}

// Tạo MutationObserver để theo dõi thay đổi trong shipList
const observer = new MutationObserver((mutations) => {
  updateShipCount();
});

// Cấu hình observer để theo dõi thay đổi trong cây DOM
const config = {
  childList: true, // Theo dõi thêm/xóa phần tử con
  subtree: true, // Theo dõi toàn bộ cây con
};

// Bắt đầu quan sát shipList
const shipList = document.getElementById("shipList");
observer.observe(shipList, config);

// Cập nhật ban đầu khi trang được tải
updateShipCount();

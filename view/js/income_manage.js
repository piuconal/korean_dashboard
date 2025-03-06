async function loadAreas() {
  try {
    const response = await fetch("get_areas.php"); // API lấy danh sách khu vực
    const areas = await response.json();
    displayAreas(areas);
  } catch (error) {
    console.error("Lỗi khi tải danh sách khu vực:", error);
  }
}

function displayAreas(areas) {
  const container = document.getElementById("container");
  const totalBox = document.getElementById("totalBox");
  container.innerHTML = "";

  areas.forEach((area) => {
    const box = document.createElement("div");
    box.classList.add("box");
    box.textContent = area.name;
    box.dataset.areaId = area.id;

    box.addEventListener("click", () => loadShips(area.id, area.name));
    container.appendChild(box);
  });

  totalBox.textContent = `SUM: ${areas.length}`;
}

loadAreas(); // Gọi khi trang tải xong

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

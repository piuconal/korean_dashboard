async function loadShips(areaId, areaName) {
  try {
    const response = await fetch(`get_ships.php?area_id=${areaId}`);
    const ships = await response.json();

    const shipList = document.getElementById("shipList");
    shipList.innerHTML = "";
    document.getElementById("modalTitle").textContent = `${areaName}`;

    if (!ships.length) {
      shipList.innerHTML = "<em>Không có tàu nào trong khu vực này.</em>";
      return;
    }

    ships.forEach(({ name, outstanding_status }) => {
      const shipBox = document.createElement("div");
      shipBox.classList.add("ship-box");
      shipBox.textContent =
        name + (Number(outstanding_status) === 1 ? " ❗" : "");
      shipList.appendChild(shipBox);
    });

    updateShipCount();
    new bootstrap.Modal(document.getElementById("infoModal")).show();
  } catch (error) {
    console.error("Lỗi khi tải danh sách tàu:", error);
    document.getElementById("shipList").innerHTML =
      "<em>Lỗi khi tải dữ liệu.</em>";
  }
}

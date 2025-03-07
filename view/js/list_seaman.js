document.addEventListener("DOMContentLoaded", function () {
  const addSeamanBtn = document.getElementById("addSeamanBtn");
  const modalElement = document.getElementById("addSeamanModal");

  if (addSeamanBtn && modalElement) {
    addSeamanBtn.addEventListener("click", function () {
      const modal = new bootstrap.Modal(modalElement);
      modal.show();
      loadSeamanFromDB(); // Gọi API lấy dữ liệu từ database khi mở modal
    });
  } else {
    console.error("Không tìm thấy nút hoặc modal!");
  }

  async function loadSeamanFromDB() {
    try {
      const response = await fetch("get_crew_members.php");
      const data = await response.json();

      displaySeamanData(data);
    } catch (error) {
      console.error("Lỗi khi tải dữ liệu thuyền viên:", error);
    }
  }

  function setupSearch() {
    const searchInput = document.getElementById("searchSeaman");

    searchInput.addEventListener("input", function () {
      const searchValue = this.value.toLowerCase();
      const rows = document.querySelectorAll(".seaman-row");

      rows.forEach((row) => {
        row.style.display = row.textContent.toLowerCase().includes(searchValue)
          ? "table-row"
          : "none";
      });
    });
  }

  function displaySeamanData(data) {
    const tableBody = document.getElementById("seamanList");
    tableBody.innerHTML = ""; // Xóa dữ liệu cũ

    if (data.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="4" class="text-center">Không có thuyền viên phù hợp.</td></tr>`;
      return;
    }

    data.forEach((row) => {
      let tableRow = document.createElement("tr");
      tableRow.classList.add("seaman-row");

      tableRow.innerHTML = `
            <td>${row.name}</td>
            <td>${row.passport_number}</td>
            <td>${row.entry_date}</td>
            <td><button class="btn btn-primary btn-sm selectSeaman" 
                data-name="${row.name}" data-passport="${row.passport_number}" data-entry="${row.entry_date}">Select</button></td>
        `;

      tableBody.appendChild(tableRow);
    });

    setupSearch();

    // Xử lý sự kiện khi ấn "Select"
    document.querySelectorAll(".selectSeaman").forEach((button) => {
      button.addEventListener("click", function () {
        const name = this.getAttribute("data-name") || "";
        const passport = this.getAttribute("data-passport") || "";
        const entryDate = this.getAttribute("data-entry") || "";
        const shipName = document.title.trim();

        console.log("🚀 Debug gửi:", { name, passport, entryDate, shipName });

        fetch("add_seaman.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `name=${encodeURIComponent(name)}&passport=${encodeURIComponent(
            passport
          )}&entry_date=${encodeURIComponent(
            entryDate
          )}&ship_name=${encodeURIComponent(shipName)}`,
        })
          .then((response) => response.json())
          .then((data) => {
            console.log("📌 Server phản hồi:", data);
            if (data.success) {
              alert("Added crew successfully!");
              location.reload();
            } else {
              alert("❌ Error: " + data.error);
            }
          })
          .catch((error) => console.error("Lỗi gửi request:", error));
      });
    });
  }
});

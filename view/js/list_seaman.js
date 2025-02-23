document.addEventListener("DOMContentLoaded", function () {
  const addSeamanBtn = document.getElementById("addSeamanBtn");
  const modalElement = document.getElementById("addSeamanModal");

  if (addSeamanBtn && modalElement) {
    addSeamanBtn.addEventListener("click", function () {
      const modal = new bootstrap.Modal(modalElement);
      modal.show();
      loadExcel(); // Gọi hàm tải dữ liệu khi mở modal
    });
  } else {
    console.error("Không tìm thấy nút hoặc modal!");
  }

  async function loadExcel() {
    try {
      const response = await fetch("../excel/mydata.xlsx");
      const data = await response.arrayBuffer();
      const workbook = XLSX.read(data, { type: "array" });

      const sheetName = workbook.SheetNames[0];
      const sheet = workbook.Sheets[sheetName];
      const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

      displaySeamanData(jsonData);
    } catch (error) {
      console.error("Lỗi khi tải file Excel:", error);
    }
  }
  function setupSearch() {
    const searchInput = document.getElementById("searchSeaman");

    searchInput.addEventListener("input", function () {
      const searchValue = this.value.toLowerCase();
      const rows = document.querySelectorAll(".seaman-row"); // ✅ Đúng selector

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

    if (data.length <= 1) {
      tableBody.innerHTML = `<tr><td colspan="4" class="text-center">Không có dữ liệu thuyền viên.</td></tr>`;
      return;
    }

    // ⚡ Chỉ giữ lại dòng có "고용상태" CHÍNH XÁC là "근무" hoặc "하선(고용중단)"
    const filteredData = data.slice(1).filter((row) => {
      const status = (row[5] || "").trim(); // Cột "고용상태" (cột 6, index 5)
      return status === "근무" || status === "하선(고용중단)";
    });

    if (filteredData.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="4" class="text-center">Không có thuyền viên phù hợp.</td></tr>`;
      return;
    }

    filteredData.forEach((row) => {
      let tableRow = document.createElement("tr");
      tableRow.classList.add("seaman-row");

      tableRow.innerHTML = `
            <td>${row[2]}</td>  <!-- Tên thuyền viên (cột 3) -->
            <td>${row[3]}</td>  <!-- Hộ chiếu (cột 4) -->
            <td>${row[4]}</td>  <!-- Ngày nhập cảnh (cột 5) -->
            <td><button class="btn btn-primary btn-sm selectSeaman" 
                data-name="${row[2]}" data-passport="${row[3]}" data-entry="${row[4]}">Select</button></td>
        `;

      tableBody.appendChild(tableRow);
    });

    setupSearch(); // ✅ Gọi lại filter sau khi dữ liệu đã load

    // Xử lý sự kiện khi ấn "Select"
    document.querySelectorAll(".selectSeaman").forEach((button) => {
      button.addEventListener("click", function () {
        const name = this.getAttribute("data-name") || "";
        const passport = this.getAttribute("data-passport") || "";
        const entryDate = this.getAttribute("data-entry") || "";
        const shipName = document.title.trim(); // Kiểm tra lại nếu lấy từ title

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

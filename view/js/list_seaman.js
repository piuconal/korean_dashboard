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

    const shipname = document.title.trim().toLowerCase();

    const filteredData = data.filter((row, index) => {
      if (index === 0) return false; // Bỏ qua hàng tiêu đề
      return row[1] && row[1].toLowerCase() === shipname;
    });

    if (filteredData.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="4" class="text-center">Không có thuyền viên cho tàu này.</td></tr>`;
      return;
    }

    filteredData.forEach((row) => {
      let tableRow = document.createElement("tr");
      tableRow.classList.add("seaman-row"); // ✅ Thêm class để search hoạt động

      tableRow.innerHTML = `
            <td>${row[2]}</td>  <!-- Tên thuyền viên (cột 3) -->
            <td>${row[3]}</td>  <!-- Hộ chiếu (cột 4) -->
            <td>${row[4]}</td>  <!-- Ngày nhập cảnh (cột 5) -->
            <td><button class="btn btn-primary btn-sm selectSeaman" 
                data-name="${row[2]}" data-passport="${row[3]}" data-entry="${row[4]}">Select</button></td>
        `;

      tableBody.appendChild(tableRow);
    });

    // ✅ Gọi lại filter sau khi dữ liệu đã load
    setupSearch();

    // Xử lý sự kiện khi ấn "Select"
    document.querySelectorAll(".selectSeaman").forEach((button) => {
      button.addEventListener("click", function () {
        const name = this.getAttribute("data-name");
        const passport = this.getAttribute("data-passport");
        const entryDate = this.getAttribute("data-entry");

        // Gửi AJAX request để lưu vào DB
        fetch("add_seaman.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `name=${encodeURIComponent(name)}&passport=${encodeURIComponent(
            passport
          )}&entry_date=${encodeURIComponent(
            entryDate
          )}&ship_name=${encodeURIComponent(shipname)}`,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              alert("Added crew successfully!");
            } else {
              alert(data.error);
            }
          })
          .catch((error) => console.error("Lỗi:", error));
      });
    });
  }
});

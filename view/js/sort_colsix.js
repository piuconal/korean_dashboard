document.addEventListener("DOMContentLoaded", function () {
  function sortTableByDisembarkDate() {
    let table = document.querySelector("table tbody");
    let rows = Array.from(table.rows);

    rows.sort((a, b) => {
      let dateA = a.cells[5].innerText.trim();
      let dateB = b.cells[5].innerText.trim();

      // Nếu một trong hai giá trị trống, đưa lên đầu
      if (!dateA) return -1;
      if (!dateB) return 1;

      // Chuyển đổi chuỗi thành đối tượng Date để so sánh
      let dateObjA = new Date(dateA);
      let dateObjB = new Date(dateB);

      return dateObjA - dateObjB; // Sắp xếp tăng dần
    });

    // Ghi đè lại bảng với thứ tự đã sắp xếp
    rows.forEach((row) => table.appendChild(row));
  }

  sortTableByDisembarkDate(); // Gọi khi trang tải xong
});

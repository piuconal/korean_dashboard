function openExcel() {
  let loadingMessage = document.getElementById("loadingMessage");
  loadingMessage.style.display = "block"; // Hiện thông báo khi đang mở file

  fetch("http://localhost:5000/open-excel").finally(() => {
    loadingMessage.style.display = "none"; // Ẩn thông báo khi hoàn tất
  });
}

function runSolvePy() {
  fetch("../include/open_solve.php")
    .then((response) => response.text())
    .then((data) => {
      console.log("Python script executed:", data);
    })
    .catch((error) => console.error("Error:", error));
}

function openIncomeManage() {
  window.open("../view/IncomeManage.php", "_blank");
}

async function openIncomeManage() {
  try {
    // Lấy dữ liệu JSON từ tệp
    const response = await fetch("../excel/areas.json");
    const areas = await response.json();
    console.log("Dữ liệu JSON gửi đi:", JSON.stringify({ areas })); // Debug

    // Gửi dữ liệu đến server
    const insertResponse = await fetch("add_areas.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ areas }),
    });

    // Lấy phản hồi từ server
    const resultText = await insertResponse.text();
    console.log("Phản hồi từ server:", resultText); // Debug

    // Kiểm tra và xử lý phản hồi JSON
    try {
      const result = JSON.parse(resultText); // Chuyển đổi JSON
      if (!result.success) {
        console.error("Lỗi khi thêm khu vực:", result.error);
      }
    } catch (error) {
      console.error("Lỗi phân tích JSON:", error);
      alert("Lỗi phân tích phản hồi từ server.");
    }
  } catch (error) {
    console.error("Lỗi:", error);
    alert("Đã có lỗi xảy ra khi gửi dữ liệu.");
  }

  // Mở một tab mới với trang quản lý thu nhập
  window.open("IncomeManage.php", "_blank");
}

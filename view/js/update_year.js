$(document).ready(function () {
  // Khi nhấn vào nút "+"
  $(".updateYearBtn").click(function () {
    var seamanId = $(this).data("id"); // Lấy ID của seaman từ thuộc tính data-id

    // Gửi yêu cầu AJAX để cập nhật năm
    $.ajax({
      url: "update_year.php", // Đường dẫn tới PHP script cập nhật năm
      type: "POST",
      data: { id: seamanId },
      success: function (response) {
        var data = JSON.parse(response); // Phân tích dữ liệu trả về từ PHP
        if (data.success) {
          // Tự động kiểm tra checkbox sau khi cập nhật năm
          var checkbox = $("input.shipFeeCheckbox[data-id='" + seamanId + "']");
          checkbox.prop("checked", true); // Đánh dấu checkbox

          // Gửi yêu cầu AJAX để cập nhật ship_fee thành 132000 trong cơ sở dữ liệu
          $.ajax({
            url: "update_ship_fee.php", // Đường dẫn tới PHP script cập nhật ship_fee
            type: "POST",
            data: { id: seamanId, ship_fee: 132000 }, // Gửi ship_fee = 132000
            success: function (updateResponse) {
              console.log("Cập nhật ship_fee thành công với giá trị 132000");

              // Tải lại trang sau khi cập nhật thành công
              location.reload(); // Tải lại trang
            },
            error: function (xhr, status, error) {
              console.error(
                "Lỗi khi cập nhật ship_fee: " + status + ": " + error
              ); // Nếu có lỗi khi cập nhật ship_fee
            },
          });
        } else {
          console.error("Lỗi: " + data.message); // Nếu có lỗi từ PHP
        }
      },
      error: function (xhr, status, error) {
        // Ghi lại lỗi AJAX nếu có
        console.error("Lỗi AJAX: " + status + ": " + error);
      },
    });
  });
});

$(document).ready(function () {
  $(".deleteSeamanBtn").on("click", function () {
    const seamanId = $(this).data("id");
    const row = $(this).closest("tr");

    if (confirm("Are you sure you want to delete this seaman?")) {
      $.ajax({
        url: "delete_seaman.php",
        type: "POST",
        data: {
          id: seamanId,
        },
        success: function (response) {
          try {
            // Kiểm tra nếu response là chuỗi trước khi parse
            if (typeof response !== "string") {
              response = JSON.stringify(response); // Chuyển thành chuỗi nếu là object
            }
            const result = JSON.parse(response);
            if (result.success) {
              row.remove();
              alert(result.message || "Seaman deleted successfully!");
            } else {
              alert("Error: " + (result.error || "Unknown error occurred"));
            }
          } catch (e) {
            alert("Error: Failed to parse server response - " + e.message);
          }
        },
        error: function (xhr, status, error) {
          alert("Error: Unable to connect to server - " + status);
        },
      });
    }
  });
});

import pandas as pd
import json
import os
import subprocess
import platform
from flask import Flask, jsonify

# Phần xử lý dữ liệu Excel
def process_excel():
    try:
        # Đọc dữ liệu từ tệp Excel
        file_path = "excel/mydata.xlsx"
        df = pd.read_excel(file_path)  # Đọc với tiêu đề cột

        # Lấy dữ liệu từ cột 1 (bỏ qua tiêu đề) và loại bỏ trùng lặp
        unique_values = df.iloc[1:, 0].dropna().unique().tolist()

        # Ghi dữ liệu vào file JSON
        output_file = "excel/areas.json"
        with open(output_file, "w", encoding="utf-8") as f:
            json.dump(unique_values, f, ensure_ascii=False, indent=4)

        print(f"Dữ liệu đã được lưu vào {output_file}")
        return True
    except Exception as e:
        print(f"Lỗi khi xử lý Excel: {str(e)}")
        return False

# Phần Flask server
app = Flask(__name__)

# Route mở file Excel
@app.route('/open-excel')
def open_excel():
    excel_path = r"C:\xampp\htdocs\korean_dashboard\excel\mydata.xlsx"  # Đường dẫn file Excel

    try:
        if platform.system() == "Windows":  # Nếu chạy trên Windows
            os.startfile(excel_path)  # Mở file Excel trực tiếp
        elif platform.system() == "Darwin":  # Nếu chạy trên macOS
            subprocess.run(["open", excel_path])
        else:  # Nếu chạy trên Linux
            subprocess.run(["xdg-open", excel_path])

        return jsonify({"message": "Đã mở file Excel thành công!"}), 200

    except Exception as e:
        return jsonify({"error": str(e)}), 500

# Route xử lý dữ liệu Excel và lưu vào JSON
@app.route('/process-excel')
def process_excel_route():
    if process_excel():
        return jsonify({"message": "Dữ liệu đã được lưu vào excel/areas.json"}), 200
    else:
        return jsonify({"error": "Có lỗi khi xử lý dữ liệu Excel"}), 500

if __name__ == '__main__':
    # Xử lý Excel trước khi chạy Flask server
    process_excel()
    
    # Sau khi xử lý xong, chạy Flask server
    app.run(host="0.0.0.0", port=5000, debug=True)

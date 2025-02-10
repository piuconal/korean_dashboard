from flask import Flask, jsonify
import os
import subprocess
import platform

app = Flask(__name__)

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

if __name__ == '__main__':
    app.run(host="0.0.0.0", port=5000, debug=True)

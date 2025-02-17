import pandas as pd
import json

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

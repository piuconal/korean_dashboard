import tkinter as tk
from tkinter import messagebox
from datetime import datetime

def calculate_days():
    try:
        date_format = "%Y-%m-%d"
        start_date = datetime.strptime(start_date_entry.get(), date_format)
        end_date = datetime.strptime(end_date_entry.get(), date_format)
        
        days_difference = (end_date - start_date).days
        result_label.config(text=f"Số ngày: {days_difference} ngày")
    except ValueError:
        messagebox.showerror("Lỗi", "Vui lòng nhập đúng định dạng YYYY-MM-DD")

# Tạo cửa sổ chính
root = tk.Tk()
root.title("Date Calculator")
root.geometry("300x200")

# Nhãn và ô nhập ngày bắt đầu
tk.Label(root, text="Ngày bắt đầu (YYYY-MM-DD):").pack()
start_date_entry = tk.Entry(root)
start_date_entry.pack()

# Nhãn và ô nhập ngày kết thúc
tk.Label(root, text="Ngày kết thúc (YYYY-MM-DD):").pack()
end_date_entry = tk.Entry(root)
end_date_entry.pack()

# Nút tính toán
calculate_button = tk.Button(root, text="Date Cal", command=calculate_days)
calculate_button.pack(pady=10)

# Nhãn hiển thị kết quả
result_label = tk.Label(root, text="")
result_label.pack()

# Chạy vòng lặp chính
root.mainloop()

import tkinter as tk
from tkinter import messagebox
from datetime import datetime
from dateutil.relativedelta import relativedelta

# Hàm để định dạng ngày với dấu "."
def format_birthday(event):
    input_text = event.widget.get().replace(".", "").replace("-", "")
    formatted_text = ""
    
    # Định dạng thành yyyy.mm.dd
    if len(input_text) > 4:
        formatted_text += input_text[:4] + "."
        if len(input_text) > 6:
            formatted_text += input_text[4:6] + "."
            formatted_text += input_text[6:8]
        else:
            formatted_text += input_text[4:6]
    else:
        formatted_text += input_text

    event.widget.delete(0, tk.END)
    event.widget.insert(0, formatted_text)

def show_calculate_period():
    clear_widgets()
    root.geometry("400x300")
    # Xóa dữ liệu trong ô nhập
    start_date_entry.delete(0, tk.END)
    end_date_entry.delete(0, tk.END)

    tk.Label(root, text="시작 날짜 (YYYY.MM.DD):", font=("Arial", 14)).pack(pady=5)
    start_date_entry.pack(pady=5)
    
    tk.Label(root, text="종료 날짜 (YYYY.MM.DD):", font=("Arial", 14)).pack(pady=5)
    end_date_entry.pack(pady=5)

    button_frame_calc.pack(pady=10)
    calculate_button.config(text="계산", command=calculate_days)
    tax_button.grid(row=0, column=1, padx=5)  # Hiển thị nút tính thuế

    result_label.pack(pady=5)
    back_button.pack(pady=5)

def show_add_time():
    clear_widgets()
    root.geometry("400x300")
    # Xóa dữ liệu trong ô nhập
    start_date_entry.delete(0, tk.END)
    year_entry.delete(0, tk.END)
    month_entry.delete(0, tk.END)
    day_entry.delete(0, tk.END)

    tk.Label(root, text="시작 날짜 (YYYY.MM.DD):", font=("Arial", 14)).pack(pady=5)
    start_date_entry.pack(pady=5)

    tk.Label(root, text="추가할 시간:", font=("Arial", 14)).pack(pady=5)
    
    time_frame.pack()

    button_frame_calc.pack(pady=10)
    calculate_button.config(text="시간 추가", command=add_time)
    tax_button.grid_forget()  # Ẩn nút tính thuế trong chế độ "시간 추가"

    back_button.pack(pady=5)
    result_label.pack(pady=5)

def clear_widgets():
    for widget in root.winfo_children():
        widget.pack_forget()

def show_main_menu():
    clear_widgets()
    root.geometry("400x80")
    start_date_entry.delete(0, tk.END)
    end_date_entry.delete(0, tk.END)
    year_entry.delete(0, tk.END)
    month_entry.delete(0, tk.END)
    day_entry.delete(0, tk.END)

    button_frame.pack(pady=10)
    result_label.config(text="")

def calculate_days():
    start_date_str = start_date_entry.get()
    end_date_str = end_date_entry.get()

    try:
        start_date = datetime.strptime(start_date_str, '%Y.%m.%d')
        end_date = datetime.strptime(end_date_str, '%Y.%m.%d')
        
        if start_date > end_date:
            messagebox.showerror("오류", "시작 날짜는 종료 날짜보다 이전이어야 합니다.")
            return
        
        global delta
        delta = relativedelta(end_date + relativedelta(days=1), start_date)
        years = delta.years
        months = delta.months
        days = delta.days

        result_label.config(text=f"결과: {years}년, {months}개월, {days}일", font=("Arial", 14))
    except ValueError:
        messagebox.showerror("오류", "날짜를 YYYY.MM.DD 형식으로 입력하세요.")

def calculate_tax():
    if delta is None:
        messagebox.showerror("오류", "먼저 기간 계산을 수행하세요.")
        return

    months_total = delta.years * 12 + delta.months
    tax = months_total * 11000 + (delta.days / 30) * 11000
    #tax = round(tax , -3)
    result_label.config(text=result_label.cget("text") + f"\n선원 관리비: {tax:,.0f}원", font=("Arial", 14))

def add_time():
    start_date_str = start_date_entry.get()
    try:
        start_date = datetime.strptime(start_date_str, '%Y.%m.%d')

        years_to_add = int(year_entry.get() or 0)
        months_to_add = int(month_entry.get() or 0)
        days_to_add = int(day_entry.get() or 0)

        new_date = start_date + relativedelta(years=years_to_add, months=months_to_add, days=days_to_add)
        result_label.config(text=f"결과: {new_date.strftime('%Y.%m.%d')}", font=("Arial", 14))
    except ValueError:
        messagebox.showerror("오류", "시작 날짜와 추가할 시간을 올바르게 입력하세요.")

def copy_tax_to_clipboard(event):
    text = result_label.cget("text")
    if "선원 관리비:" in text:
        tax_amount = text.split("선원 관리비: ")[-1].split("원")[0].strip()
        text_amount = tax_amount[:3] + "," + tax_amount[4:]
        root.clipboard_clear()
        root.clipboard_append(tax_amount)
        messagebox.showinfo("정보", f"복사된 금액: {tax_amount}원")

root = tk.Tk()
root.title("기간 계산 또는 시간 추가")


delta = None

start_date_entry = tk.Entry(root, font=("Arial", 14), width=20)
end_date_entry = tk.Entry(root, font=("Arial", 14), width=20)

start_date_entry.bind("<KeyRelease>", format_birthday)
end_date_entry.bind("<KeyRelease>", format_birthday)

time_frame = tk.Frame(root)
year_entry = tk.Entry(time_frame, font=("Arial", 14), width=5)
month_entry = tk.Entry(time_frame, font=("Arial", 14), width=5)
day_entry = tk.Entry(time_frame, font=("Arial", 14), width=5)

year_label = tk.Label(time_frame, text="년", font=("Arial", 14))
month_label = tk.Label(time_frame, text="개월", font=("Arial", 14))
day_label = tk.Label(time_frame, text="일", font=("Arial", 14))

year_entry.grid(row=0, column=0, padx=5)
year_label.grid(row=0, column=1, padx=5)
month_entry.grid(row=0, column=2, padx=5)
month_label.grid(row=0, column=3, padx=5)
day_entry.grid(row=0, column=4, padx=5)
day_label.grid(row=0, column=5, padx=5)

button_frame = tk.Frame(root, width=700, height=200)
button_frame.pack_propagate(False)
button_frame.pack(pady=10)

calculate_period_button = tk.Button(button_frame, text="기간 계산", command=show_calculate_period, font=("Arial", 14), width=15)
calculate_period_button.grid(row=0, column=0, padx=10, pady=10)

add_time_button = tk.Button(button_frame, text="시간 추가", command=show_add_time, font=("Arial", 14), width=15)
add_time_button.grid(row=0, column=1, padx=10, pady=10)

button_frame_calc = tk.Frame(root)
calculate_button = tk.Button(button_frame_calc, text="계산", command=calculate_days, font=("Arial", 14))
tax_button = tk.Button(button_frame_calc, text="관리비 계산", command=calculate_tax, font=("Arial", 14))

calculate_button.grid(row=0, column=0, padx=5)

back_button = tk.Button(root, text="돌아가기", command=show_main_menu, font=("Arial", 14))
result_label = tk.Label(root, text="", font=("Arial", 14))

result_label.bind("<Button-1>", copy_tax_to_clipboard)

show_main_menu()  # Khởi động giao diện chính
root.mainloop()


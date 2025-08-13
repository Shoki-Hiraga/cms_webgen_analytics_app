from datetime import date, timedelta

start_year=2023
start_month=4

# 月末を求める関数
def get_month_end(d):
    next_month = d.replace(day=28) + timedelta(days=4)
    return next_month - timedelta(days=next_month.day)

# 実行月のデータを含めず、前月までの (start_date, end_date) を返す
def generate_monthly_date_ranges(year=start_year, month=start_month):
    today = date.today()
    current = date(year, month, 1)
    ranges = []

    while current.year < today.year or (current.year == today.year and current.month < today.month):
        start_date = current
        end_date = get_month_end(current)
        ranges.append((start_date, end_date))

        # 翌月へ
        if current.month == 12:
            current = date(current.year + 1, 1, 1)
        else:
            current = date(current.year, current.month + 1, 1)

    return ranges

import sys
import os
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '../..')))

from setting_file.header import *
from setting_file.setFunc import get_db_config
from datetime import date, timedelta
import mysql.connector
import traceback

def get_db_connection():
    try:
        config = get_db_config()
        conn = mysql.connector.connect(**config)
        return conn
    except Exception as e:
        print(f"[CRITICAL DB ERROR] DB接続失敗: {e}")
        traceback.print_exc()
        raise

def get_date_setting(target):
    """
    target: 'GA4' または 'GSC' を指定
    """
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        cursor.execute("""
            SELECT start_year, start_month 
            FROM dataseting 
            WHERE target = %s 
            ORDER BY id DESC 
            LIMIT 1
        """, (target,))
        row = cursor.fetchone()
        cursor.close()
        conn.close()

        if not row:
            raise ValueError(f"{target} の設定が dataseting テーブルに存在しません")

        return row['start_year'], row['start_month']

    except Exception as e:
        print(f"[ERROR] 設定取得失敗: {e}")
        traceback.print_exc()
        raise

# DBから取得
start_year, start_month = get_date_setting("GA4")

def get_month_end(d):
    next_month = d.replace(day=28) + timedelta(days=4)
    return next_month - timedelta(days=next_month.day)

def generate_monthly_date_ranges(year=start_year, month=start_month):
    today = date.today()
    current = date(year, month, 1)
    ranges = []

    while current.year < today.year or (current.year == today.year and current.month < today.month):
        start_date = current
        end_date = get_month_end(current)
        ranges.append((start_date, end_date))

        if current.month == 12:
            current = date(current.year + 1, 1, 1)
        else:
            current = date(current.year, current.month + 1, 1)

    return ranges

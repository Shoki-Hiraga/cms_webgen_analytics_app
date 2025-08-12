from setting_file.setFunc import get_db_config
import mysql.connector
import traceback

def record_exists(page_url, start_date, end_date):
    try:
        config = get_db_config()
        conn = mysql.connector.connect(**config)
        cursor = conn.cursor()

        query = """
            SELECT COUNT(*) FROM gsc_qsha_oh
            WHERE page_url = %s
              AND start_date = %s
              AND end_date = %s
        """

        cursor.execute(query, (page_url, start_date, end_date))
        result = cursor.fetchone()[0]

        cursor.close()
        conn.close()

        return result > 0

    except Exception as e:
        print(f"[DB ERROR] 重複チェック失敗: {e}")
        traceback.print_exc()
        return False  # 念のため False を返す

import sys
import os
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

from datetime import date, datetime, timedelta
from setting_file.header import *
from setting_file.setFunc import get_db_config
from setting_file.scraping_KW.organic_KW_pagenation import search_keywords_list

import mysql.connector
import time


#############################################
# ■ APIキー設定
#############################################
print("[INIT] Google Custom Search API 初期化中...")

request_count = 0
api_key_index = 1
api_keys = {
    1: gcp_api.custom_search_API_KEY_current,
    2: gcp_api.custom_search_API_KEY_332r,
    3: gcp_api.custom_search_API_KEY_idea,
    4: gcp_api.custom_search_API_KEY_shotest,
    5: gcp_api.custom_search_API_KEY_2sho,
    6: gcp_api.custom_search_API_KEY_seohira,
    7: gcp_api.custom_search_API_KEY_current_data_ana_p
}

google_api_key = api_keys[api_key_index]
CUSTOM_SEARCH_ENGINE_ID = gcp_api.custom_search_ENGINE_ID_current
delaytime = 4.000002


#############################################
# ■ DB接続
#############################################
def get_db_connection():
    try:
        config = get_db_config()
        conn = mysql.connector.connect(**config)
        print("[DEBUG] DB接続成功")
        return conn
    except Exception as e:
        print(f"[CRITICAL DB ERROR] DB接続失敗: {e}")
        raise


#############################################
# ■ 重複チェック（10日以内）
#############################################
def serp_exists_recent(keyword):
    try:
        conn = get_db_connection()
        cursor = conn.cursor()

        ten_days_ago = datetime.now() - timedelta(days=10)

        query = """
            SELECT COUNT(*)
            FROM serp_organic_results
            WHERE keyword = %s AND created_at >= %s
        """

        cursor.execute(query, (keyword, ten_days_ago))
        count = cursor.fetchone()[0]

        cursor.close()
        conn.close()

        if count > 0:
            print(f"[SKIP] {keyword} は過去10日以内に保存済み → スキップ")
        else:
            print(f"[INFO] {keyword} は保存対象（10日以内データなし）")

        return count > 0

    except Exception as e:
        print(f"[DB ERROR] 重複チェック失敗: {e}")
        return False


#############################################
# ■ INSERT
#############################################
def insert_serp_result(fetched_date, rank, keyword, url, title):
    try:
        conn = get_db_connection()
        cursor = conn.cursor()

        insert_query = """
            INSERT INTO serp_organic_results
            (fetched_date, rank, keyword, url, title, created_at, updated_at)
            VALUES (%s, %s, %s, %s, %s, NOW(), NOW())
        """

        values = (fetched_date, rank, keyword, url, title)

        cursor.execute(insert_query, values)
        conn.commit()

        cursor.close()
        conn.close()

        print(f"[DB] 保存成功：{keyword}（順位: {rank}）")

    except Exception as e:
        print(f"[DB ERROR] SERP保存失敗: {e}")


#############################################
# ■ Google API 準備
#############################################
service = build("customsearch", "v1", developerKey=google_api_key)

results_per_page = 10
max_pages_to_fetch = 1


#############################################
# ■ SERP取得メイン
#############################################
def search_and_save_to_db(keyword):
    global request_count
    today = date.today().isoformat()

    try:
        print(f"[PROCESS] {keyword} の SERP 取得開始")

        search_results = []

        for page in range(max_pages_to_fetch):
            start_index = page * results_per_page + 1
            print(f"[REQUEST] KW: {keyword} / Page: {page+1}")

            res = service.cse().list(
                q=keyword,
                cx=CUSTOM_SEARCH_ENGINE_ID,
                num=results_per_page,
                start=start_index
            ).execute()

            request_count += 1
            print(f"[API] {request_count} 回目リクエスト成功")

            items = res.get("items", [])
            if not items:
                print("[INFO] 検索結果0件 → 次ページなし")
                break

            for item in items:
                search_results.append([
                    keyword,
                    item.get("link"),
                    item.get("title"),
                ])

            time.sleep(delaytime)

        for index, row in enumerate(search_results, start=1):
            insert_serp_result(today, index, row[0], row[1], row[2])

        print(f"[DONE] {keyword}: {len(search_results)} 件 保存完了")

    except Exception as e:
        print(f"[ERROR] {keyword} 処理中エラー: {e}")


#############################################
# ■ メイン実行
#############################################
if __name__ == "__main__":
    print("===== SERP Organic API 実行開始 =====")

    for keyword in search_keywords_list:

        # ▼ 重複チェック（10日以内は保存不要）
        if serp_exists_recent(keyword):
            continue

        search_and_save_to_db(keyword)

    print("===== すべてのキーワード処理完了 =====")

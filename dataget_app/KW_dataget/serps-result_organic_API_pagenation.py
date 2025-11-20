# dataget_app/KW_dataget/serps-result_organic_API_pagenation.py

import sys
import os
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

from datetime import date
from setting_file.header import *
from setting_file.setFunc import get_db_config
from setting_file.scraping_KW.organic_KW_pagenation import search_keywords_list

import mysql.connector
import time


#############################################
# ■ APIキー設定
#############################################
request_count = 0  # APIリクエストカウンター

api_key_index = 1  # 使用APIキー設定
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

# Custom Search Engine ID
CUSTOM_SEARCH_ENGINE_ID = gcp_api.custom_search_ENGINE_ID_current

delaytime = 3.000002  # APIディレイ（Google推奨）


#############################################
# ■ DB接続関数（GSCスクリプトと統一仕様）
#############################################
def get_db_connection():
    try:
        config = get_db_config()
        conn = mysql.connector.connect(**config)
        print("[DEBUG] DB接続成功")
        return conn
    except Exception as e:
        print(f"[CRITICAL DB ERROR] DB接続に失敗しました: {e}")
        raise


#############################################
# ■ DB INSERT 関数（SERP結果保存）
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
        print(f"[DB ERROR] SERPデータ保存失敗: {e}")


#############################################
# ■ Google Custom Search API 準備
#############################################
service = build("customsearch", "v1", developerKey=google_api_key)

results_per_page = 10   # 1ページあたりの結果
max_pages_to_fetch = 1  # 最大ページ数


#############################################
# ■ SERP検索 → DB保存メイン関数
#############################################
def search_and_save_to_db(keyword):
    global request_count
    today = date.today().isoformat()

    try:
        search_results = []

        for page in range(max_pages_to_fetch):
            start_index = page * results_per_page + 1

            print(f"[REQUEST] KW: {keyword} / Page: {page+1}")

            # APIリクエスト
            res = service.cse().list(
                q=keyword,
                cx=CUSTOM_SEARCH_ENGINE_ID,
                num=results_per_page,
                start=start_index
            ).execute()

            request_count += 1
            print(f"[API REQUEST] {request_count} 回目成功")

            items = res.get("items", [])
            if not items:
                break

            for item in items:
                url = item.get("link")
                title = item.get("title")
                search_results.append([keyword, url, title])

            # Google APIレートリミット対策
            time.sleep(delaytime)
            print(f"{delaytime}秒遅延完了")

        # ▼ DB保存処理（順位付きで保存）
        for index, row in enumerate(search_results, start=1):
            insert_serp_result(
                fetched_date=today,
                rank=index,
                keyword=row[0],
                url=row[1],
                title=row[2]
            )

        print(f"[DONE] {keyword}: {len(search_results)}件保存完了")

    except Exception as e:
        print(f"[ERROR] KW '{keyword}' エラー発生: {e}")


#############################################
# ■ メイン処理
#############################################
if __name__ == "__main__":
    print("===== SERP Organic API 実行開始 =====")
    for keyword in search_keywords_list:
        search_and_save_to_db(keyword)
    print("===== すべてのキーワード処理完了 =====")


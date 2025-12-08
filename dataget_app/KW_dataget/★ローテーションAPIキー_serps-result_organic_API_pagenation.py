# dataget_app/KW_dataget/serps-result_organic_API_pagenation.py

import sys
import os
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

from datetime import date, datetime, timedelta
from setting_file.header import *
from setting_file.setFunc import get_db_config, get_keywords_from_db

import mysql.connector
import time
from googleapiclient.errors import HttpError
from googleapiclient.discovery import build


#############################################
# Google Custom Search API
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
    7: gcp_api.custom_search_API_KEY_332r_Paid,
    8: gcp_api.custom_search_API_KEY_Paid_current_data_ana_p,
}

google_api_key = api_keys[api_key_index]
CUSTOM_SEARCH_ENGINE_ID = gcp_api.custom_search_ENGINE_ID_current
delaytime = 4.0
results_per_page = 10
max_pages_to_fetch = 3


#############################################
# Google API 再構築
#############################################
def rebuild_service():
    global google_api_key, service
    service = build("customsearch", "v1", developerKey=google_api_key)
    print(f"[INFO] APIサービス再構築完了 → 使用キー index: {api_key_index}")


#############################################
# APIキー ローテーション
#############################################
def rotate_api_key():
    global api_key_index, google_api_key

    api_key_index += 1

    if api_key_index > len(api_keys):
        print("[CRITICAL] 全APIキーが使用不可 → 強制終了")
        raise RuntimeError("All Google API keys exhausted")

    google_api_key = api_keys[api_key_index]
    print(f"[WARN] APIキー切り替え → index: {api_key_index}")
    rebuild_service()


#############################################
# Google API 安全リクエスト
#############################################
def safe_google_request(original_keyword, start_index):
    max_retry = len(api_keys)

    for retry in range(max_retry):
        try:
            print(f"[REQUEST] KW: {original_keyword} / start: {start_index} / key: {api_key_index}")

            res = service.cse().list(
                q=original_keyword,
                cx=CUSTOM_SEARCH_ENGINE_ID,
                num=results_per_page,
                start=start_index
            ).execute()

            return res  # 成功時

        except HttpError as e:
            status = e.resp.status
            print(f"[WARN] Google API Error → Status: {status} / retry: {retry+1}")

            # ローテーション対象エラー
            if status in [403, 429, 500, 503]:
                rotate_api_key()
                time.sleep(2)
                continue
            else:
                raise e

    print("[CRITICAL] 全APIキーでリクエスト失敗")
    raise RuntimeError("All API keys failed")


#############################################
# 初回API構築
#############################################
service = build("customsearch", "v1", developerKey=google_api_key)


#############################################
# DB接続
#############################################
def get_db_connection():
    try:
        config = get_db_config()
        conn = mysql.connector.connect(**config)
        print(f"[DEBUG] DB接続成功 → Host: {config['host']} DB: {config['database']}")
        return conn
    except Exception as e:
        print(f"[CRITICAL] DB接続失敗: {e}")
        raise


#############################################
# 過去10日以内チェック
#############################################
def serp_exists_recent(original_keyword):
    print(f"[CHECK] 重複チェック開始: {original_keyword}")

    conn = get_db_connection()
    cursor = conn.cursor()

    ten_days_ago = datetime.now() - timedelta(days=10)

    query = """
        SELECT COUNT(*)
        FROM serp_organic_results
        WHERE original_keyword = %s AND created_at >= %s
    """

    cursor.execute(query, (original_keyword, ten_days_ago))
    count = cursor.fetchone()[0]

    cursor.close()
    conn.close()

    if count > 0:
        print(f"[SKIP] {original_keyword} → 過去10日以内に取得済み")
    else:
        print(f"[INFO] {original_keyword} → 取得対象です")

    return count > 0


#############################################
# INSERT
#############################################
def insert_serp_result(fetched_date, rank, original_keyword,
                       product, priority, keyword, url, title):

    print(f"[DB] 保存開始 → {keyword} (Rank: {rank})")

    conn = get_db_connection()
    cursor = conn.cursor()

    insert_query = """
        INSERT INTO serp_organic_results
        (fetched_date, rank, original_keyword, product, priority,
         keyword, url, title, created_at, updated_at)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, NOW(), NOW())
    """

    values = (
        fetched_date,
        rank,
        original_keyword,
        product,
        priority,
        keyword,
        url,
        title
    )

    cursor.execute(insert_query, values)
    conn.commit()

    cursor.close()
    conn.close()

    print(f"[DB] 保存成功：{keyword}（{rank}位）")


#############################################
# SERP取得メイン（APIローテーション対応）
#############################################
def search_and_save_to_db(original_keyword, product, priority):
    global request_count
    today = date.today().isoformat()

    print("\n===========================================")
    print(f"[PROCESS] {original_keyword} の SERP 取得開始")
    print("===========================================\n")

    search_results = []

    try:
        for page in range(max_pages_to_fetch):
            start_index = page * results_per_page + 1

            try:
                res = safe_google_request(original_keyword, start_index)
            except Exception as e:
                print(f"[ERROR] API取得失敗（全キー枯渇）: {e}")
                break

            request_count += 1
            print(f"[API] {request_count} 回目リクエスト成功")

            items = res.get("items", [])
            if not items:
                print("[INFO] 検索結果0件 → 終了")
                break

            for item in items:
                search_results.append([
                    original_keyword,
                    item.get("link"),
                    item.get("title"),
                ])

            time.sleep(delaytime)

        # 保存処理
        for index, row in enumerate(search_results, start=1):
            insert_serp_result(
                today,
                index,
                original_keyword,
                product,
                priority,
                row[0],
                row[1],
                row[2]
            )

        print(f"[DONE] {original_keyword}: {len(search_results)} 件 保存完了\n")

    except Exception as e:
        print(f"[ERROR] {original_keyword} 処理中エラー: {e}")


#############################################
# メイン実行
#############################################
if __name__ == "__main__":
    print("===== SERP Organic API 実行開始 =====")

    search_keywords = get_keywords_from_db("organic_keywords")

    print(f"[INFO] organic_keywords 取得数: {len(search_keywords)}")

    if not search_keywords:
        print("[ERROR] organic_keywords にキーワードがありません")
        exit()

    for row in search_keywords:
        original_keyword = row["keyword"]
        product = row["product"]
        priority = row["priority"]

        if serp_exists_recent(original_keyword):
            continue

        search_and_save_to_db(original_keyword, product, priority)

    print("===== すべてのキーワード処理完了 =====")

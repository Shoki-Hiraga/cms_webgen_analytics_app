import sys
import os
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
from datetime import date
from setting_file.header import *

request_count = 0  # クエリカウンター

# APIキーの選択
api_key_index = 1 # 使用するAPIキーのインデックス番号
api_keys = {
    1: gcp_api.custom_search_API_KEY_current,
    2: gcp_api.custom_search_API_KEY_332r,
    3: gcp_api.custom_search_API_KEY_idea,
    4: gcp_api.custom_search_API_KEY_shotest,
    5: gcp_api.custom_search_API_KEY_2sho,
    6: gcp_api.custom_search_API_KEY_seohira,
    7: gcp_api.custom_search_API_KEY_current_data_ana_p
}

google_api_key = api_keys[api_key_index] # APIキーの取得

# カスタムサーチエンジンID
# CUSTOM_SEARCH_ENGINE_ID = gcp_api.custom_search_ENGINE_ID_332r
CUSTOM_SEARCH_ENGINE_ID = gcp_api.custom_search_ENGINE_ID_current

# リクエストの遅延時間を定義
delaytime = 3.000002


# ファイルパス
file_directory = file_path.file_directory # file_path.py で定義したファイルディレクトリを指定
file_name = "site_search_results_pagenation5.csv"
output_file = os.path.join(file_directory, file_name)

# KWリスト
from setting_file.scraping_KW.organic_KW_pagenation import search_keywords_list

# 1ページあたりの検索件数
results_per_page = 10
# ページネーションを何ページ取得するか指定
max_pages_to_fetch = 3

# Google Custom Search APIサービスの構築
service = build("customsearch", "v1", developerKey=google_api_key)

# CSVファイルの初期化
if not os.path.exists(file_directory):
    os.makedirs(file_directory)

with open(output_file, mode='w', newline='', encoding='utf-8') as file:
    writer = csv.writer(file)
    writer.writerow(["取得日", "順位", "Keyword", "URL", "Meta Title"])

# 検索とCSV出力
def search_and_write_to_csv(keyword):
    global request_count
    today = date.today().isoformat()  # '2025-01-15' のような形式

    try:
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
            print(f"[API REQUEST] {request_count} 回目のリクエストに成功しました")

            items = res.get("items", [])
            if not items:
                break

            for item in items:
                url = item.get("link")
                title = item.get("title")
                search_results.append([keyword, url, title])

            time.sleep(delaytime)
            print(f"{delaytime}秒の遅延が完了しました")

        # ▼ CSV 書き込み（取得日 → 順位 → その他）
        with open(output_file, mode='a', newline='', encoding='utf-8') as file:
            writer = csv.writer(file)
            for index, row in enumerate(search_results, start=1):
                writer.writerow([today, index] + row)

        print(f"Keyword '{keyword}' processed successfully. Found {len(search_results)} results.")

    except Exception as e:
        print(f"このKWでエラーが発生しました '{keyword}': {e}")

# 各キーワードに対して検索を実行
for keyword in search_keywords_list:
    search_and_write_to_csv(keyword)

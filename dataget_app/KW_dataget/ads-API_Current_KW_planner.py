import sys
import os
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

from setting_file.header import *
from setting_file.setFunc import get_db_config
from setting_file.scraping_KW.ads_api_KW import search_keywords_list

import mysql.connector
from google.ads.googleads.client import GoogleAdsClient
import time
from datetime import datetime, timedelta


#############################################
# ■ Google Ads API クライアント
#############################################
print("[INIT] Google Ads API 初期化中...")
client = GoogleAdsClient.load_from_storage(api_yaml.current)
customer_id = "2973188677"
delay_time = 3.4


#############################################
# ■ DB接続（GSC/SERP と同仕様）
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
def record_exists_recent(keyword):
    try:
        conn = get_db_connection()
        cursor = conn.cursor()

        ten_days_ago = datetime.now() - timedelta(days=10)

        query = """
            SELECT COUNT(*)
            FROM ads_keyword_planner_results
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
# ■ DB INSERT
#############################################
def insert_ads_keyword_data(keyword, search_volume, competition_level,
                            competition_index, low_cpc, high_cpc):

    try:
        conn = get_db_connection()
        cursor = conn.cursor()

        insert_query = """
            INSERT INTO ads_keyword_planner_results
            (keyword, avg_monthly_search_volume, competition_level,
             competition_index, low_cpc, high_cpc, created_at, updated_at)
            VALUES (%s, %s, %s, %s, %s, %s, NOW(), NOW())
        """

        values = (
            keyword, search_volume, competition_level,
            competition_index, low_cpc, high_cpc
        )

        cursor.execute(insert_query, values)
        conn.commit()

        cursor.close()
        conn.close()

        print(f"[DB] 保存成功: {keyword}")

    except Exception as e:
        print(f"[DB ERROR] 保存失敗: {e}")


#############################################
# ■ Keyword Planner API 実行
#############################################
def get_keyword_ideas(client, customer_id, keyword_texts):
    print(f"[API] Ads API リクエスト開始: {keyword_texts}")

    keyword_plan_idea_service = client.get_service("KeywordPlanIdeaService")
    language_id = 1000  # 日本語

    request = client.get_type("GenerateKeywordIdeasRequest")
    request.customer_id = customer_id
    request.language = client.get_service("GoogleAdsService").language_constant_path(language_id)

    request.keyword_seed.keywords.extend(keyword_texts)

    try:
        response = keyword_plan_idea_service.generate_keyword_ideas(request=request)
        print("[API] Keyword Ideas 成功")
    except Exception as e:
        print(f"[API ERROR] Keyword Ideas エラー: {e}")
        return []

    keyword_data = []

    for result in response.results:
        metrics = result.keyword_idea_metrics

        avg_monthly_searches = metrics.avg_monthly_searches or '-'
        competition_level = metrics.competition.name or '-'
        competition_index = metrics.competition_index or '-'

        low_top_of_page_bid = (
            metrics.low_top_of_page_bid_micros / 1_000_000
            if metrics.low_top_of_page_bid_micros else None
        )
        high_top_of_page_bid = (
            metrics.high_top_of_page_bid_micros / 1_000_000
            if metrics.high_top_of_page_bid_micros else None
        )

        keyword_data.append({
            'keyword': result.text,
            'avg_monthly_search_volume': avg_monthly_searches,
            'low_cpc': low_top_of_page_bid,
            'high_cpc': high_top_of_page_bid,
            'competition_level': competition_level,
            'competition_index': competition_index,
        })

    return keyword_data


#############################################
# ■ メイン処理（DB 保存）
#############################################
if __name__ == "__main__":
    print("========== Google Ads Keyword Planner 実行開始 ==========")

    for keyword in search_keywords_list:

        # ▼ 重複チェック（10日以内ならスキップ）
        if record_exists_recent(keyword):
            continue

        results = get_keyword_ideas(client, customer_id, [keyword])

        for item in results:
            print(
                f"[RESULT] KW: {item['keyword']} | 月間検索数: {item['avg_monthly_search_volume']} "
                f"| CPC低: {item['low_cpc']} | CPC高: {item['high_cpc']} "
                f"| 競合レベル: {item['competition_level']} | 指標: {item['competition_index']}"
            )

            insert_ads_keyword_data(
                keyword=item["keyword"],
                search_volume=item["avg_monthly_search_volume"],
                competition_level=item["competition_level"],
                competition_index=item["competition_index"],
                low_cpc=item["low_cpc"],
                high_cpc=item["high_cpc"]
            )

        print(f"[DELAY] {delay_time} 秒待機")
        time.sleep(delay_time)

    print("========== すべてのキーワード処理完了 ==========")

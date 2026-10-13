# dataget_app/KW_dataget/ads-API_Current_KW_planner.py

import sys
import os
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

from setting_file.header import *
from setting_file.setFunc import get_db_config, get_keywords_from_db

import mysql.connector
from google.ads.googleads.client import GoogleAdsClient
import time
from datetime import datetime, timedelta


#############################################
# Google Ads API クライアント
#############################################
print("[INIT] Google Ads API 初期化中...")
client = GoogleAdsClient.load_from_storage(api_yaml.current)
customer_id = "2973188677"
delay_time = 3.4


#############################################
# DB接続
#############################################
def get_db_connection():
    try:
        config = get_db_config()
        conn = mysql.connector.connect(**config)
        print("[DEBUG] DB接続成功:", config["host"], config["database"])
        return conn
    except Exception as e:
        print(f"[CRITICAL DB ERROR] DB接続失敗: {e}")
        raise


#############################################
# 過去10日以内の重複チェック（original_keyword）
#############################################
def record_exists_recent(original_keyword):
    print(f"[CHECK] 重複チェック開始: {original_keyword}")

    conn = get_db_connection()
    cursor = conn.cursor()

    ten_days_ago = datetime.now() - timedelta(days=10)

    query = """
        SELECT COUNT(*)
        FROM area_ads_keyword_planner_results
        WHERE original_keyword = %s AND created_at >= %s
    """

    cursor.execute(query, (original_keyword, ten_days_ago))
    count = cursor.fetchone()[0]

    cursor.close()
    conn.close()

    if count > 0:
        print(f"[SKIP] {original_keyword} は過去10日以内に取得済み → スキップ")
    else:
        print(f"[INFO] {original_keyword} は取得対象です")

    return count > 0


#############################################
# Ads キーワード保存
#############################################
def insert_ads_keyword_data(original_keyword, product, priority,
                            keyword, search_volume, competition_level,
                            competition_index, low_cpc, high_cpc):

    print(f"[DB] 保存開始: {keyword}")

    conn = get_db_connection()
    cursor = conn.cursor()

    insert_query = """
        INSERT INTO area_ads_keyword_planner_results
        (original_keyword, product, priority, keyword,
         avg_monthly_search_volume, competition_level,
         competition_index, low_cpc, high_cpc,
         created_at, updated_at)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, NOW(), NOW())
    """

    values = (
        original_keyword,
        product,
        priority,
        keyword,
        search_volume,
        competition_level,
        competition_index,
        low_cpc,
        high_cpc
    )

    cursor.execute(insert_query, values)
    conn.commit()

    cursor.close()
    conn.close()

    print(f"[DB] 保存成功: {keyword}")


#############################################
# Keyword Planner API
#############################################
def get_keyword_ideas(client, customer_id, keyword_texts):
    print(f"[API] Ads API リクエスト開始: {keyword_texts}")

    keyword_plan_idea_service = client.get_service("KeywordPlanIdeaService")

    request = client.get_type("GenerateKeywordIdeasRequest")
    request.customer_id = customer_id
    request.language = client.get_service("GoogleAdsService").language_constant_path(1000)
    request.keyword_seed.keywords.extend(keyword_texts)

    response = keyword_plan_idea_service.generate_keyword_ideas(request=request)

    print("[API] Keyword Ideas 成功")

    keyword_data = []

    for result in response.results:
        metrics = result.keyword_idea_metrics

        print(f"[RESULT] {result.text} | Search: {metrics.avg_monthly_searches}")

        keyword_data.append({
            "keyword": result.text,
            "avg_monthly_search_volume": metrics.avg_monthly_searches,
            "low_cpc": (metrics.low_top_of_page_bid_micros / 1_000_000
                        if metrics.low_top_of_page_bid_micros else None),
            "high_cpc": (metrics.high_top_of_page_bid_micros / 1_000_000
                        if metrics.high_top_of_page_bid_micros else None),
            "competition_level": metrics.competition.name,
            "competition_index": metrics.competition_index,
        })

    return keyword_data


#############################################
# メイン処理
#############################################
if __name__ == "__main__":
    print("========== Google Ads Keyword Planner 実行開始 ==========")

    search_keywords_list = get_keywords_from_db("area_ads_keywords")

    print(f"[INFO] area_ads_keywords 取得数: {len(search_keywords_list)}")

    if not search_keywords_list:
        print("[ERROR] area_ads_keywords にキーワードがありません")
        exit()

    for row in search_keywords_list:
        original_keyword = row["keyword"]
        product = row["product"]
        priority = row["priority"]

        print("\n-----------------------------------------")
        print(f"[START] {original_keyword} | {product} | {priority}")
        print("-----------------------------------------\n")

        if record_exists_recent(original_keyword):
            continue

        results = get_keyword_ideas(client, customer_id, [original_keyword])

        for item in results:
            insert_ads_keyword_data(
                original_keyword=original_keyword,
                product=product,
                priority=priority,
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

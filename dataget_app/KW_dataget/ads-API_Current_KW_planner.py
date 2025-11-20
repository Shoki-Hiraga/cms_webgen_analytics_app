import sys
import os
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

from setting_file.header import *
from setting_file.setFunc import get_db_config
from setting_file.scraping_KW.ads_api_KW import search_keywords_list

import mysql.connector
from google.ads.googleads.client import GoogleAdsClient
import time


#############################################
# ■ Google Ads API クライアント
#############################################
client = GoogleAdsClient.load_from_storage(api_yaml.current)
customer_id = "2973188677"

delay_time = 3.4


#############################################
# ■ DB接続（GSC/SERPと統一）
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
# ■ INSERT（Ads Keyword Planner結果保存）
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

        values = (keyword, search_volume, competition_level,
                  competition_index, low_cpc, high_cpc)

        cursor.execute(insert_query, values)
        conn.commit()
        cursor.close()
        conn.close()

        print(f"[DB] 保存完了: {keyword}")

    except Exception as e:
        print(f"[DB ERROR] 保存失敗: {e}")


#############################################
# ■ Keyword Planner API 実行
#############################################
def get_keyword_ideas(client, customer_id, keyword_texts):
    keyword_plan_idea_service = client.get_service("KeywordPlanIdeaService")
    language_id = 1000  # 日本語

    request = client.get_type("GenerateKeywordIdeasRequest")
    request.customer_id = customer_id
    request.language = client.get_service("GoogleAdsService").language_constant_path(language_id)
    request.keyword_seed.keywords.extend(keyword_texts)

    try:
        response = keyword_plan_idea_service.generate_keyword_ideas(request=request)
        print("[API] Keyword ideas retrieved 成功")
    except Exception as e:
        print(f"[API ERROR] Keyword ideas retrieved エラー: {e}")
        return []

    keyword_data = []

    for result in response.results:
        metrics = result.keyword_idea_metrics

        avg_monthly_searches = metrics.avg_monthly_searches or '-'
        competition_level = metrics.competition.name or '-'
        competition_index = metrics.competition_index or '-'

        low_top_of_page_bid = metrics.low_top_of_page_bid_micros / 1_000_000 \
                                if metrics.low_top_of_page_bid_micros else None
        high_top_of_page_bid = metrics.high_top_of_page_bid_micros / 1_000_000 \
                                if metrics.high_top_of_page_bid_micros else None

        keyword_data.append({
            'keyword': result.text,
            'avg_monthly_search_volume': avg_monthly_searches,
            'low_cpc': low_top_of_page_bid,
            'high_cpc': high_top_of_page_bid,
            'competition_level': competition_level,
            'competition_index': competition_index
        })

    return keyword_data


#############################################
# ■ メイン処理（DB保存）
#############################################
if __name__ == "__main__":
    print("========== Google Ads Keyword Planner 実行開始 ==========")

    for keyword in search_keywords_list:

        results = get_keyword_ideas(client, customer_id, [keyword])

        for item in results:
            print(
                f"[RESULT] KW: {item['keyword']} | "
                f"検索数: {item['avg_monthly_search_volume']} | "
                f"低額CPC: {item['low_cpc']} | 高額CPC: {item['high_cpc']} | "
                f"競合レベル: {item['competition_level']} | "
                f"競合指標: {item['competition_index']}"
            )

            # DB保存
            insert_ads_keyword_data(
                keyword=item["keyword"],
                search_volume=item["avg_monthly_search_volume"],
                competition_level=item["competition_level"],
                competition_index=item["competition_index"],
                low_cpc=item["low_cpc"],
                high_cpc=item["high_cpc"]
            )

        # 遅延
        print(f"遅延処理: {delay_time}秒")
        time.sleep(delay_time)

    print("========== すべてのキーワード処理完了 ==========")


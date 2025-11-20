import sys
import os
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
from setting_file.header import *

# 設定方法
# https://docs.google.com/document/d/1U4HWFDTHMAbTCl6SZuajYDMqsog5DUf7Ytpe1Acm0mE/edit?tab=t.0

# 個別URLリストインスタンス
from setting_file.scraping_KW.ads_api_KW import search_keywords_list

# Google Adsクライアントの初期化
client = GoogleAdsClient.load_from_storage(api_yaml.current)
client_id = "2973188677"
# 遅延処理
deley_time = 3.4

# ファイルパス
file_directory = file_path.file_directory # file_path.py で定義したファイルディレクトリを指定
file_name = "kw_plan_data.csv"
output_file = os.path.join(file_directory, file_name)

# CSVのヘッダーを更新
header_row = [
    'Keyword', '月間平均ボリューム', '競合指標', '競合レベル', '低額CPC', '高額CPC'
]

def get_keyword_ideas(client, customer_id, keyword_texts):
    keyword_plan_idea_service = client.get_service("KeywordPlanIdeaService")
    language_id = 1000  # 日本語

    request = client.get_type("GenerateKeywordIdeasRequest")
    request.customer_id = customer_id
    request.language = client.get_service("GoogleAdsService").language_constant_path(language_id)
    request.keyword_seed.keywords.extend(keyword_texts)

    try:
        response = keyword_plan_idea_service.generate_keyword_ideas(request=request)
        print("Keyword ideas retrieved 成功.")
    except Exception as e:
        print(f"Keyword ideas retrievedエラー: {e}")
        return []

    keyword_data = []
    for result in response.results:
        # データの取得、0やnullの場合は '-' に置き換え
        avg_monthly_searches = result.keyword_idea_metrics.avg_monthly_searches or '-'
        competition_level = result.keyword_idea_metrics.competition.name or '-'
        competition_index = result.keyword_idea_metrics.competition_index or '-'

        # クリック単価がnullの場合も '-' に置き換え
        low_top_of_page_bid = result.keyword_idea_metrics.low_top_of_page_bid_micros / 1_000_000 if result.keyword_idea_metrics.low_top_of_page_bid_micros else '-'
        high_top_of_page_bid = result.keyword_idea_metrics.high_top_of_page_bid_micros / 1_000_000 if result.keyword_idea_metrics.high_top_of_page_bid_micros else '-'

        keyword_data.append({
            'keyword': result.text,
            'avg_monthly_search_volume': avg_monthly_searches,
            'low_top_of_page_bid': low_top_of_page_bid,
            'high_top_of_page_bid': high_top_of_page_bid,
            'competition_level': competition_level,
            'competition_index': competition_index,
        })

    return keyword_data

# CSVファイルを開いて、逐次データを書き込む
with open(output_file, mode='w', newline='', encoding='utf-8') as csvfile:
    fieldnames = header_row
    writer = csv.DictWriter(csvfile, fieldnames=fieldnames)

    # ヘッダーの書き込み
    writer.writeheader()

    # キーワードリストを元にリクエストを送信し、3秒の遅延を追加
    for keyword in search_keywords_list:
        data = get_keyword_ideas(client, client_id, [keyword])

        # データの書き込み
        for item in data:
            writer.writerow({
                'Keyword': item['keyword'], 
                '月間平均ボリューム': item['avg_monthly_search_volume'], 
                '低額CPC': item['low_top_of_page_bid'],
                '高額CPC': item['high_top_of_page_bid'],
                '競合レベル': item['competition_level'], 
                '競合指標': item['competition_index']
            })

            # ターミナルに結果を表示
            print(f"Keyword: {item['keyword']}, 月間平均ボリューム: {item['avg_monthly_search_volume']}, "
                  f"低額CPC: {item['low_top_of_page_bid']}, 高額CPC: {item['high_top_of_page_bid']}, "
                  f"競合レベル: {item['competition_level']}, 競合指標: {item['competition_index']}")

        # バッファの内容を即時書き込み
        csvfile.flush()

        # 遅延処理
        time.sleep(deley_time)  
        print(f"遅延処理：{deley_time}秒")

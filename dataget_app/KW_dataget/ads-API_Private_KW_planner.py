import sys
import os
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
from setting_file.header import *

# 設定方法
# https://docs.google.com/document/d/1U4HWFDTHMAbTCl6SZuajYDMqsog5DUf7Ytpe1Acm0mE/edit?tab=t.0

# Google Adsクライアントの初期化
client = GoogleAdsClient.load_from_storage("C:/Users/hiraga/Desktop/Python-dev/setting_file/ads_api_private_google-ads.yaml")
client_id = "4507358446"
# 遅延処理
deley_time = 3

# ファイルパス
file_directory = file_path.file_directory # file_path.py で定義したファイルディレクトリを指定
file_name = "kw_plan_data.csv"
output_file = os.path.join(file_directory, file_name)

# CSVのヘッダーを更新
header_row = [
    'Keyword', '月間平均ボリューム', '競合指標', '競合レベル', '低額CPC', '高額CPC'
    ]

# キーワードリストの例
keyword_list = [
    "車 買取", 
    "車 売却"
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
        # 月間平均検索ボリューム
        avg_monthly_searches = result.keyword_idea_metrics.avg_monthly_searches

        # 競合レベルと競合指標
        competition_level = result.keyword_idea_metrics.competition.name
        competition_index = result.keyword_idea_metrics.competition_index

        # クリック単価（低いページ上部の推定入札単価と高いページ上部の推定入札単価）
        low_top_of_page_bid = result.keyword_idea_metrics.low_top_of_page_bid_micros / 1_000_000  # マイクロ単位からドルへ変換
        high_top_of_page_bid = result.keyword_idea_metrics.high_top_of_page_bid_micros / 1_000_000  # マイクロ単位からドルへ変換

        keyword_data.append({
            'keyword': result.text,
            'avg_monthly_search_volume': avg_monthly_searches,
            'low_top_of_page_bid': low_top_of_page_bid,
            'high_top_of_page_bid': high_top_of_page_bid,
            'competition_level': competition_level,
            'competition_index': competition_index,
        })

    return keyword_data

# キーワードリストを元にリクエストを送信し、3秒の遅延を追加
keyword_data = []
for keyword in keyword_list:
    data = get_keyword_ideas(client, client_id, [keyword])
    keyword_data.extend(data)
    time.sleep(deley_time)  
    print(f"遅延処理：{deley_time}秒")

# 結果の表示
for item in keyword_data:
    print(f"Keyword: {item['keyword']}, 月間平均ボリューム: {item['avg_monthly_search_volume']}, "
          f"低額CPC: {item['low_top_of_page_bid']}, 高額CPC: {item['high_top_of_page_bid']}"
          f"競合レベル: {item['competition_level']}, 競合指標: {item['competition_index']}, "
          )

# CSVファイルへの出力
with open(output_file, mode='w', newline='', encoding='utf-8') as csvfile:
    fieldnames = header_row
    writer = csv.DictWriter(csvfile, fieldnames=fieldnames)

    # ヘッダーの書き込み
    writer.writeheader()

    # データの書き込み
    for item in keyword_data:
        writer.writerow({
            'Keyword': item['keyword'], 
            '月間平均ボリューム': item['avg_monthly_search_volume'], 
            '低額CPC': item['low_top_of_page_bid'],
            '高額CPC': item['high_top_of_page_bid'],
            '競合レベル': item['competition_level'], 
            '競合指標': item['competition_index']
        })

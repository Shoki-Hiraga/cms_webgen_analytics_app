import sys
import os
import json

sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
from setting_file.header import *
from setting_file.setFunc import get_db_config
from setting_file.Search_Console_set.GSC_dateSet import generate_monthly_date_ranges
from setting_file.Search_Console_set.GSC_Query_date_Duplicate import record_exists
# from setting_file.Search_Console_set.GSC_QshQuery import QUERIES

# .env 読み込み
load_dotenv(dotenv_path=os.path.join(os.path.dirname(__file__), '..', 'config', '.env'))

# DB接続関数
def get_db_connection():
    try:
        config = get_db_config()
        conn = mysql.connector.connect(**config)
        print("[DEBUG] DB接続成功")
        return conn
    except Exception as e:
        print(f"[CRITICAL DB ERROR] DB接続に失敗しました: {e}")
        traceback.print_exc()
        raise  # エラーを再スローしてメイン処理も止める

def get_gsc_settings():
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)

        query = "SELECT site_url, service_account_json FROM gsc_setting LIMIT 1"
        cursor.execute(query)
        setting = cursor.fetchone()

        cursor.close()
        conn.close()

        if not setting:
            raise ValueError("GSC設定がテーブルに存在しません")

        service_account_info = json.loads(setting['service_account_json'])

        return {
            'SITE_URL': setting['site_url'],
            'SERVICE_ACCOUNT_INFO': service_account_info
        }
    except Exception as e:
        print(f"[CRITICAL DB ERROR] GSC設定取得に失敗しました: {e}")
        traceback.print_exc()
        raise

# GSC設定をDBから取得
gsc_settings = get_gsc_settings()

site_url = gsc_settings['SITE_URL']

credentials = service_account.Credentials.from_service_account_info(
    gsc_settings['SERVICE_ACCOUNT_INFO'],
    scopes=['https://www.googleapis.com/auth/webmasters.readonly']
)

# Search Console APIのバージョン
api_version = 'v3'
service = build('webmasters', api_version, credentials=credentials)

# クエリデータ取得
def get_queries_from_db():
    try:
        conn = get_db_connection()
        cursor = conn.cursor()

        query = "SELECT query FROM gsc_query_listqueries WHERE is_active = TRUE"
        cursor.execute(query)

        queries = [row[0] for row in cursor.fetchall()]
        print(f"[DEBUG] {len(queries)} 件のクエリを取得")

        cursor.close()
        conn.close()
        return queries

    except Exception as e:
        print(f"[DB ERROR] クエリ取得中にエラー: {e}")
        traceback.print_exc()
        return []

# 検索クエリに基づくSearch Consoleデータ取得関数
def get_search_query_data(site_url, keyword):
    request = {
        'startDate': set_start_date,
        'endDate': set_end_date,
        'dimensions': ['query'],
        'searchType': 'web',
        'dimensionFilterGroups': [{
            'filters': [{
                'dimension': 'query',
                'operator': 'contains',  # 完全一致なら 'equals'
                'expression': keyword
            }]
        }]
    }

    try:
        response = service.searchanalytics().query(siteUrl=site_url, body=request).execute()
        return response.get('rows', []), keyword
    except Exception as e:
        print(f'Error retrieving data for keyword {keyword}: {e}')
        return [], keyword

def insert_gsc_data(query_keyword, total_impressions, total_clicks, avg_ctr, avg_position, start_date, end_date):
    try:
        print(f"[DEBUG] Insert: {query_keyword}, Impr={total_impressions}, Clicks={total_clicks}, CTR={avg_ctr}, Pos={avg_position}")

        conn = get_db_connection()
        cursor = conn.cursor()

        insert_query = """
            INSERT INTO gsc_query
            (query_keyword, total_impressions, total_clicks, avg_ctr, avg_position, start_date, end_date, created_at, updated_at)
            VALUES (%s, %s, %s, %s, %s, %s, %s, NOW(), NOW())
        """

        values = (
            query_keyword,
            int(total_impressions),
            int(total_clicks),
            float(avg_ctr),
            float(avg_position),
            start_date,
            end_date
        )

        cursor.execute(insert_query, values)
        conn.commit()
        print("[DEBUG] DB保存成功")

        cursor.close()
        conn.close()

    except Exception as e:
        print(f"[DB ERROR] GSCデータ保存に失敗しました: {e}")
        traceback.print_exc()

try:
    queries = get_queries_from_db()
    date_ranges = generate_monthly_date_ranges()

    for start_date, end_date in date_ranges:
        set_start_date = start_date.strftime("%Y-%m-%d")
        set_end_date = end_date.strftime("%Y-%m-%d")
        db_start_date = start_date
        db_end_date = end_date

        print(f"\n[INFO] 期間: {set_start_date} ～ {set_end_date}")

        for keyword in queries:
            # 重複チェック（クエリ対応に変更が必要）
            if record_exists(keyword, db_start_date, db_end_date):
                print(f"[SKIP] 既に登録済み: {keyword}, {db_start_date} - {db_end_date}")
                continue

            search_query_data, original_query = get_search_query_data(site_url, keyword)

            delay = random.uniform(5.0, 7.5)
            print(f'遅延処理 {delay:.2f} 秒')
            time.sleep(delay)

            total_impressions = 0
            total_clicks = 0
            total_ctr = 0
            total_position = 0
            count = 0

            for row in search_query_data:
                impressions = row.get('impressions', 0)
                clicks = row.get('clicks', 0)
                ctr = row.get('ctr', 0)
                position = row.get('position', 0)

                total_impressions += impressions
                total_clicks += clicks
                total_ctr += ctr
                total_position += position
                count += 1

            if count > 0:
                avg_ctr = total_ctr / count
                avg_position = total_position / count
            else:
                avg_ctr = 0
                avg_position = 0

            print(f'URL: {original_query}, 合計表示回数: {total_impressions}, 合計クリック数: {total_clicks}, 平均CTR: {avg_ctr:.4f}, 平均掲載順位: {avg_position:.2f}')

            # DBに保存
            insert_gsc_data(
                query_keyword=original_query,
                total_impressions=total_impressions,
                total_clicks=total_clicks,
                avg_ctr=avg_ctr,
                avg_position=avg_position,
                start_date=db_start_date,
                end_date=db_end_date
            )

except Exception as err:
    print(f'[CRITICAL ERROR] エラーが発生しました: {err}')

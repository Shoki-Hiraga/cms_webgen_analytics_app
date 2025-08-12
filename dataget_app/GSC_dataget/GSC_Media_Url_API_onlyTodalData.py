import sys
import os
import json

sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
from setting_file.header import *
from setting_file.setFunc import get_db_config
from setting_file.Search_Console_set.GSC_dateSet import generate_monthly_date_ranges
from setting_file.Search_Console_set.GSC_date_Duplicate_Media_Url import record_exists
# from setting_file.Search_Console_set.GSC_QshURL_Historia import URLS

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

def get_urls_from_db():
    try:
        conn = get_db_connection()
        cursor = conn.cursor()

        query = "SELECT url FROM gsc_media_url_listurls WHERE is_active = TRUE"
        cursor.execute(query)

        urls = [row[0] for row in cursor.fetchall()]
        print(f"[DEBUG] {len(urls)} 件のURLを取得")

        cursor.close()
        conn.close()
        return urls

    except Exception as e:
        print(f"[DB ERROR] URL取得中にエラー: {e}")
        traceback.print_exc()
        return []

# 指定したURLに一致したデータを取得する関数を定義します
def get_search_url_data(site_url, page_url):
    request = {
        'startDate': set_start_date,
        'endDate': set_end_date,
        'dimensions': ['page'],
        'searchType': 'web',
        'dimensionFilterGroups': [{
            'filters': [{
                'dimension': 'page',
                # 完全一致
                # 'operator': 'equals',
                # 部分一致
                'operator': 'contains',
                'expression': page_url
            }]
        }]
    }

    try:
        response = service.searchanalytics().query(siteUrl=site_url, body=request).execute()
        return response.get('rows', []), page_url
    except Exception as e:
        print(f'Error retrieving data for URL {page_url}: {e}')
        return [], page_url

def insert_gsc_data(page_url, total_impressions, total_clicks, avg_ctr, avg_position, start_date, end_date):
    try:
        print(f"[DEBUG] Insert: {page_url}, Impr={total_impressions}, Clicks={total_clicks}, CTR={avg_ctr}, Pos={avg_position}")

        conn = get_db_connection()
        cursor = conn.cursor()

        insert_query = """
            INSERT INTO gsc_media_url
            (page_url, total_impressions, total_clicks, avg_ctr, avg_position, start_date, end_date, created_at, updated_at)
            VALUES (%s, %s, %s, %s, %s, %s, %s, NOW(), NOW())
        """

        values = (
            page_url,
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
    date_ranges = generate_monthly_date_ranges()
    urls = get_urls_from_db()
    for start_date, end_date in date_ranges:
        set_start_date = start_date.strftime("%Y-%m-%d")
        set_end_date = end_date.strftime("%Y-%m-%d")
        db_start_date = start_date
        db_end_date = end_date

        print(f"\n[INFO] 期間: {set_start_date} ～ {set_end_date}")

        for url in urls:
            # 重複チェック
            if record_exists(url, db_start_date, db_end_date):
                print(f"[SKIP] 既に登録済み: {url}, {db_start_date} - {db_end_date}")
                continue
            search_url_data, original_url = get_search_url_data(site_url, url)

            delay = random.uniform(5.0, 7.5)
            print(f'遅延処理 {delay:.2f} 秒')
            time.sleep(delay)

            total_impressions = 0
            total_clicks = 0
            total_ctr = 0
            total_position = 0
            count = 0

            for row in search_url_data:
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

            print(f'URL: {original_url}, 合計表示回数: {total_impressions}, 合計クリック数: {total_clicks}, 平均CTR: {avg_ctr:.4f}, 平均掲載順位: {avg_position:.2f}')

            # DBに保存
            insert_gsc_data(
                page_url=original_url,
                total_impressions=total_impressions,
                total_clicks=total_clicks,
                avg_ctr=avg_ctr,
                avg_position=avg_position,
                start_date=db_start_date,
                end_date=db_end_date
            )

except Exception as err:
    print(f'[CRITICAL ERROR] エラーが発生しました: {err}')

import os
from dotenv import load_dotenv

# Laravel プロジェクトの .env を読み込む（2階層上のルートにある）
dotenv_path = os.path.join(os.path.dirname(__file__), '..', '..', '.env')
dotenv_path = os.path.abspath(dotenv_path)
load_dotenv(dotenv_path=dotenv_path)

def get_db_config():
    app_url = os.getenv('APP_URL')

    if app_url in ['http://localhost', '127.0.0.1']:
        print('[INFO] ローカルDB設定を使用します')
        db_config = {
            'host': os.getenv('DB_HOST'),
            'user': os.getenv('DB_USERNAME'),
            'password': os.getenv('DB_PASSWORD'),
            'database': os.getenv('DB_DATABASE'),
            'port': int(os.getenv('DB_PORT', 3306)),
            'charset': 'utf8mb4'
        }
    else:
        print('[INFO] 本番DB設定を使用します')
        db_config = {
            'host': 'mysql8004.xserver.jp',
            'user': 'chasercb750_anal',
            'password': '78195090Cb',
            'database': 'chasercb750_webanalytics',
            'port': 3306,
            'charset': 'utf8mb4',
            'ssl_disabled': True
        }

    return db_config

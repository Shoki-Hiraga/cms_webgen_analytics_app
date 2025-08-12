import os
import time
import random
from googleapiclient.discovery import build
from setting_file import api_json 
from google.oauth2 import service_account
from google.analytics.data_v1beta import BetaAnalyticsDataClient
from google.analytics.data_v1beta.types import (
    DateRange, Metric, Dimension, RunReportRequest,
    FilterExpression, FilterExpressionList, Filter
)
from datetime import datetime
import time
import random
import mysql.connector
from dotenv import load_dotenv
import traceback

os.chdir(os.path.dirname(os.path.abspath(__file__)))# スクリプトが存在するディレクトリを作業ディレクトリとして設定

set_start_date = "2025-05-01"
set_end_date = "2025-06-01"

# DB用：文字列を datetime.date に変換
db_start_date = datetime.strptime(set_start_date, "%Y-%m-%d").date()
db_end_date = datetime.strptime(set_end_date, "%Y-%m-%d").date()

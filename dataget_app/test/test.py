import time

duration = 15.0      # 実行時間（秒）
interval = 0.001     # 1ms = 0.001秒
start = time.time()

count = 0
while True:
    now = time.time()
    if now - start >= duration:
        break

    print(f"{count}: {now - start:.6f} sec")
    count += 1
    time.sleep(interval)

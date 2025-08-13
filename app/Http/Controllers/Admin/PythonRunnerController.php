<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PythonRunnerController extends Controller
{
    private $scripts = [
        'Directory_API' => 'dataget_app/GA4_dataget/Directory_API.py',
        'FullUrl_API'   => 'dataget_app/GA4_dataget/FullUrl_API.py',
        'Media_Url_API' => 'dataget_app/GA4_dataget/Media_Url_API.py',
        'GSC_FullUrl'   => 'dataget_app/GSC_dataget/GSC_FullUrl_API_onlyTodalData.py',
        'GSC_Media'     => 'dataget_app/GSC_dataget/GSC_Media_Url_API_onlyTodalData.py',
        'GSC_Query'     => 'dataget_app/GSC_dataget/GSC_Query_API_onlyTodalData.py',
        'Qsh_MK_RS_UV'  => 'dataget_app/GSC_dataget/Qsh_MK_RS_UV_GSC_API_onlyTodalData.py',
    ];

    /**
     * 一覧画面
     */
    public function index()
    {
        return view('admin.python.button_index', [
            'scripts' => array_keys($this->scripts)
        ]);
    }

    /**
     * Pythonスクリプト実行（OS判定あり）
     */
    public function run($script)
    {
        if (!isset($this->scripts[$script])) {
            abort(404);
        }

        $scriptPath = base_path($this->scripts[$script]);
        $logPath = storage_path("logs/{$script}.log");

        // 実行環境判定
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        // Pythonパス設定（本番用とローカル用）
        if ($isWindows) {
            // Windows（ローカル）
            $pythonPath = 'C:\\Users\\K39_sho\\AppData\\Local\\Programs\\Python\\Python312\\python.exe';
        } else {
            // Linux（Xサーバーなど）
            $pythonPath = '~/anaconda3/bin/python';
        }

        // 古いログ削除はしない（上書きモードで作成されるので不要）
        if (file_exists($logPath)) {
            @unlink($logPath);
        }

        // OSごとの非同期実行コマンド
        if ($isWindows) {
            // Windows: start /B でバックグラウンド実行
            $cmd = "start /B \"\" \"{$pythonPath}\" -u \"{$scriptPath}\" > \"{$logPath}\" 2>&1";
            pclose(popen($cmd, "r"));
        } else {
            // Linux: nohup + & でバックグラウンド実行
            $cmd = "nohup {$pythonPath} -u {$scriptPath} > {$logPath} 2>&1 & echo $!";
            exec($cmd, $output);
            // PID保存（停止用）
            file_put_contents(storage_path("logs/{$script}.pid"), $output[0] ?? '');
        }

        return redirect()->route('admin.python.log', ['script' => $script]);
    }

    /**
     * ログ表示
     */
    public function showLog($script)
    {
        $logPath = storage_path("logs/{$script}.log");
        $pidPath = storage_path("logs/{$script}.pid");

        $logContent = file_exists($logPath) ? file_get_contents($logPath) : '';
        // UTF-8 以外で出てくる場合を考慮して変換
        $logContent = mb_convert_encoding($logContent, 'UTF-8', 'auto');

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $isRunning = false;

        if (!$isWindows && file_exists($pidPath)) {
            $pid = trim(file_get_contents($pidPath));
            if ($pid) {
                exec("ps -p {$pid}", $statusOutput);
                $isRunning = count($statusOutput) > 1;
            }
        }
        // Windows側のプロセス監視は省略（常にfalse）

        return view('admin.python.log', [
            'script' => $script,
            'log' => $logContent,
            'isRunning' => $isRunning
        ]);
    }

    /**
     * 停止機能（Linuxのみ）
     */
    public function stop($script)
    {
        $pidPath = storage_path("logs/{$script}.pid");

        if (file_exists($pidPath)) {
            $pid = trim(file_get_contents($pidPath));
            if ($pid) {
                exec("kill {$pid}");
            }
            unlink($pidPath);
        }

        return redirect()->route('admin.python.log', ['script' => $script]);
    }
}

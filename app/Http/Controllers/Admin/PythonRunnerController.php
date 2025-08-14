<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;

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

    public function index()
    {
        return view('admin.python.button_index', ['scripts' => array_keys($this->scripts)]);
    }

    public function run($script)
    {
        if (!isset($this->scripts[$script])) {
            abort(404);
        }

        $scriptPath = base_path($this->scripts[$script]);
        $logPath = storage_path("logs/{$script}.log");
        $pidPath = storage_path("logs/{$script}.pid");

        // 二重実行チェック
        if (file_exists($pidPath)) {
            $oldPid = trim(file_get_contents($pidPath));
            if ($this->isProcessRunning($oldPid)) {
                return back()->with('error', "このスクリプトは既に実行中です (PID: {$oldPid})。停止してから再実行してください。");
            }
        }

        // 古いログは空にする
        file_put_contents($logPath, '');

        // 実行コマンド
        $os = strtoupper(substr(PHP_OS, 0, 3));
        if ($os === 'WIN') {
            // Windows
            $pythonPath = 'C:\\Users\\hiraga\\AppData\\Local\\Programs\\Python\\Python312\\python.exe';
            $cmd = "start /B \"\" \"{$pythonPath}\" -u \"{$scriptPath}\" >> \"{$logPath}\" 2>&1";
            pclose(popen($cmd, "r"));
            // WindowsではPID取得が難しいのでダミー値を入れる（停止はWindows非対応）
            file_put_contents($pidPath, 'WIN_NO_PID');
        } else {
            // Linux
            $pythonPath = '~/anaconda3/bin/python';
            $cmd = "nohup {$pythonPath} -u {$scriptPath} >> {$logPath} 2>&1 & echo $!";
            $pid = shell_exec($cmd);
            file_put_contents($pidPath, trim($pid));
        }

        return redirect()->route('admin.python.log', ['script' => $script]);
    }

    public function showLog($script)
    {
        $logPath = storage_path("logs/{$script}.log");
        $pidPath = storage_path("logs/{$script}.pid");

        $logContent = file_exists($logPath) ? file_get_contents($logPath) : '';
        $isRunning = false;

        if (file_exists($pidPath)) {
            $pid = trim(file_get_contents($pidPath));
            $isRunning = $this->isProcessRunning($pid);
        }

        return view('admin.python.log', [
            'script' => $script,
            'log' => $logContent,
            'isRunning' => $isRunning
        ]);
    }

    public function stop($script)
    {
        $pidPath = storage_path("logs/{$script}.pid");
        if (!file_exists($pidPath)) {
            return back()->with('error', 'PIDファイルが存在しません。');
        }

        $pid = trim(file_get_contents($pidPath));
        $os = strtoupper(substr(PHP_OS, 0, 3));

        if ($os === 'WIN') {
            if ($pid === 'WIN_NO_PID') {
                return back()->with('error', 'Windowsではこの方法で停止はサポートされていません。');
            }
            exec("taskkill /PID {$pid} /F");
        } else {
            exec("kill -9 {$pid}");
        }

        unlink($pidPath);
        return back()->with('success', "スクリプト {$script} を停止しました。");
    }

    private function isProcessRunning($pid)
    {
        if (!$pid || $pid === 'WIN_NO_PID') {
            return false;
        }
        $os = strtoupper(substr(PHP_OS, 0, 3));
        if ($os === 'WIN') {
            exec("tasklist /FI \"PID eq {$pid}\"", $output);
            return count($output) > 1 && !str_contains($output[1], '情報:');
        } else {
            return trim(shell_exec("ps -p {$pid} -o pid=")) == $pid;
        }
    }
}

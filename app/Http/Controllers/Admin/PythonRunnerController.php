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

    private function getPythonPath()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windowsローカル
            return 'C:\\Users\\K39_sho\\AppData\\Local\\Programs\\Python\\Python312\\python.exe';
        } else {
            // 本番(Xサーバー)
            return '/home/chasercb750/anaconda3/bin/python';
        }
    }

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
        $logPath    = storage_path("logs/{$script}.log");
        $pythonPath = $this->getPythonPath();
        $pidDir     = storage_path("app/python_pids");

        // 古いログ削除
        if (file_exists($logPath)) {
            unlink($logPath);
        }

        if (!is_dir($pidDir)) {
            mkdir($pidDir, 0777, true);
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows: popenで非同期実行（PIDは取得しない）
            pclose(popen("\"{$pythonPath}\" -u \"{$scriptPath}\" > \"{$logPath}\" 2>&1", "r"));
        } else {
            // Linux: PID取得して保存（STDIN切り離し）
            $cmd = "{$pythonPath} -u \"{$scriptPath}\" > \"{$logPath}\" 2>&1 < /dev/null & echo $!";
            exec($cmd, $output);
            if (!empty($output[0])) {
                file_put_contents("{$pidDir}/{$script}.pid", trim($output[0]));
            }
        }

        return redirect()->route('admin.python.log', ['script' => $script]);
    }

    public function stop($script)
    {
        $pidFile = storage_path("app/python_pids/{$script}.pid");

        if (file_exists($pidFile)) {
            $pid = trim(file_get_contents($pidFile));

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // WindowsはPID管理なし → 全プロセスkillは危険なので無効
                return back()->with('error', 'Windows環境では停止機能は利用できません');
            } else {
                // Linux: killで停止
                exec("kill {$pid}");
                unlink($pidFile);
            }
        }

        return redirect()->route('admin.python.log', ['script' => $script]);
    }

    public function showLog($script)
    {
        $logPath   = storage_path("logs/{$script}.log");
        $pidFile   = storage_path("app/python_pids/{$script}.pid");

        $logContent = file_exists($logPath) ? file_get_contents($logPath) : '';
        $isRunning  = file_exists($pidFile);

        return view('admin.python.log', [
            'script'    => $script,
            'log'       => $logContent,
            'isRunning' => $isRunning
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GscSetting;
use Illuminate\Http\Request;

class GscSettingController extends Controller
{
    /**
     * 設定一覧（基本は1件だけ）
     */
    public function index()
    {
        $setting = GscSetting::first();
        return view('admin.gsc_settings.gsc_set_index', compact('setting'));
    }

    /**
     * 新規作成画面
     */
    public function create()
    {
        if (GscSetting::count() > 0) {
            return redirect()
                ->route('admin.gsc_settings.index')
                ->with('error', '設定は既に存在しています。新規作成できません。');
        }

        return view('admin.gsc_settings.gsc_set_create');
    }

    /**
     * 新規保存
     */
    public function store(Request $request)
    {
        if (GscSetting::count() > 0) {
            return redirect()
                ->route('admin.gsc_settings.index')
                ->with('error', '設定は既に存在しています。');
        }

        $validated = $request->validate([
            'site_url' => 'required|string|max:255',
            'service_account_json' => 'required|json',
        ]);

        // idを1固定で登録
        $validated['id'] = 1;

        GscSetting::create($validated);

        return redirect()
            ->route('admin.gsc_settings.index')
            ->with('success', 'GSC設定を作成しました。');
    }

    /**
     * 編集画面
     */
    public function edit(GscSetting $gsc_setting)
    {
        return view('admin.gsc_settings.gsc_set_edit', compact('gsc_setting'));
    }

    /**
     * 更新処理
     */
    public function update(Request $request, GscSetting $gsc_setting)
    {
        $validated = $request->validate([
            'site_url' => 'required|string|max:255',
            'service_account_json' => 'required|json',
        ]);

        $gsc_setting->update($validated);

        return redirect()
            ->route('admin.gsc_settings.index')
            ->with('success', 'GSC設定を更新しました。');
    }

    /**
     * 削除処理
     */
    public function destroy(GscSetting $gsc_setting)
    {
        $gsc_setting->delete();

        return redirect()
            ->route('admin.gsc_settings.index')
            ->with('success', 'GSC設定を削除しました。');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ga4Setting;
use Illuminate\Http\Request;

class Ga4SettingController extends Controller
{
    /**
     * 設定一覧（基本は1件だけ）
     */
    public function index()
    {
        // 1件だけ取得（存在しなければnull）
        $setting = Ga4Setting::first();
        return view('admin.ga4_settings.ga4_set_index', compact('setting'));
    }

    /**
     * 新規作成画面
     */
    public function create()
    {
        if (Ga4Setting::count() > 0) {
            return redirect()
                ->route('admin.ga4_settings.index')
                ->with('error', '設定は既に存在しています。新規作成できません。');
        }

        return view('admin.ga4_settings.ga4_set_create');
    }

    /**
     * 新規保存
     */
    public function store(Request $request)
    {
        if (Ga4Setting::count() > 0) {
            return redirect()
                ->route('admin.ga4_settings.index')
                ->with('error', '設定は既に存在しています。');
        }

        $validated = $request->validate([
            'service_account_json' => 'required|json',
            'property_id' => 'required|string|max:255',
            'session_medium_filter' => 'required|string|max:255', // 一時的にここで取得
        ]);

        $validated['id'] = 1;

        $setting = Ga4Setting::create([
            'id' => $validated['id'],
            'service_account_json' => $validated['service_account_json'],
            'property_id' => $validated['property_id'],
        ]);

        // フィルタ作成
        $setting->filter()->create([
            'session_medium_filter' => $validated['session_medium_filter'],
        ]);

        return redirect()
            ->route('admin.ga4_settings.index')
            ->with('success', 'GA4設定を作成しました。');
    }

    /**
     * 編集画面
     */
    public function edit(Ga4Setting $ga4_setting)
    {
        return view('admin.ga4_settings.ga4_set_edit', compact('ga4_setting'));
    }

    /**
     * 更新処理
     */
    public function update(Request $request, Ga4Setting $ga4_setting)
    {
        $validated = $request->validate([
            'service_account_json' => 'required|json',
            'property_id' => 'required|string|max:255',
            'session_medium_filter' => 'required|string|max:255',
        ]);

        $ga4_setting->update([
            'service_account_json' => $validated['service_account_json'],
            'property_id' => $validated['property_id'],
        ]);

        // フィルタ更新（存在しない場合は作成）
        $ga4_setting->filter()->updateOrCreate(
            ['ga4_setting_id' => $ga4_setting->id],
            ['session_medium_filter' => $validated['session_medium_filter']]
        );

        return redirect()
            ->route('admin.ga4_settings.index')
            ->with('success', 'GA4設定を更新しました。');
    }

    /**
     * 削除処理
     */
    public function destroy(Ga4Setting $ga4_setting)
    {
        $ga4_setting->delete();

        return redirect()
            ->route('admin.ga4_settings.index')
            ->with('success', 'GA4設定を削除しました。');
    }
}

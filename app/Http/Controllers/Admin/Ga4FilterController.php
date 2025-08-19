<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ga4Setting;
use App\Models\Ga4Filter;
use Illuminate\Http\Request;

class Ga4FilterController extends Controller
{
    /**
     * フィルター一覧画面
     */
    public function index()
    {
        $filters = Ga4Filter::with('setting')->get();

        return view('admin.ga4_filter.ga4_filter_index', compact('filters'));
    }

    /**
     * フィルター新規作成画面
     */
    public function create(Request $request)
    {
        $ga4_setting_id = Ga4Setting::first()?->id; // 1件だけならこれでもOK
        return view('admin.ga4_filter.ga4_filter_create', compact('ga4_setting_id'));
    }

    /**
     * フィルター保存処理（新規作成）
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ga4_setting_id' => 'required|exists:ga4_setting,id|unique:ga4_filter,ga4_setting_id',
            'session_medium_filter' => 'required|string|max:255',
        ]);

        Ga4Filter::create($validated);

        return redirect()
            ->route('admin.ga4_filters.index')
            ->with('success', 'フィルターを作成しました。');
    }

    /**
     * フィルター編集画面
     */
    public function edit(Ga4Setting $ga4_setting)
    {
        $setting = $ga4_setting;
        $filter = $setting->filter;

        return view('admin.ga4_filter.ga4_filter_edit', compact('setting', 'filter'));
    }

    /**
     * フィルター更新処理
     */
    public function update(Request $request, Ga4Setting $ga4_setting)
    {
        $validated = $request->validate([
            'session_medium_filter' => 'required|string|max:255',
        ]);

        $ga4_setting->filter()->updateOrCreate(
            ['ga4_setting_id' => $ga4_setting->id],
            ['session_medium_filter' => $validated['session_medium_filter']]
        );

        return redirect()
            ->route('admin.ga4_settings.index')
            ->with('success', 'セッションメディアフィルターを更新しました。');
    }
}

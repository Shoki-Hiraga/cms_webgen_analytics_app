<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dataseting;
use Illuminate\Http\Request;

class DatasetingController extends Controller
{
    /**
     * 設定一覧
     */
    public function index()
    {
        // 全てのターゲット（GA4, GSCなど）を取得
        $settings = Dataseting::all();
        return view('admin.dataseting.dataseting_index', compact('settings'));
    }

    /**
     * 新規作成画面
     */
    public function create()
    {
        return view('admin.dataseting.dataseting_create');
    }

    /**
     * 新規保存
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'target'      => 'required|string|max:50', // 'GA4' or 'GSC'
            'start_year'  => 'required|integer|min:2000|max:2100',
            'start_month' => 'required|integer|min:1|max:12',
        ]);

        // 同じtargetが既に存在している場合はエラー
        if (Dataseting::where('target', $validated['target'])->exists()) {
            return redirect()
                ->route('admin.dataseting.index')
                ->with('error', $validated['target'] . ' の設定は既に存在しています。');
        }

        Dataseting::create($validated);

        return redirect()
            ->route('admin.dataseting.index')
            ->with('success', '設定を作成しました。');
    }

    /**
     * 編集画面
     */
    public function edit(Dataseting $dataseting)
    {
        return view('admin.dataseting.dataseting_edit', compact('dataseting'));
    }

    /**
     * 更新処理
     */
    public function update(Request $request, Dataseting $dataseting)
    {
        $validated = $request->validate([
            'target'      => 'required|string|max:50',
            'start_year'  => 'required|integer|min:2000|max:2100',
            'start_month' => 'required|integer|min:1|max:12',
        ]);

        // 同じtargetの重複チェック（自分以外）
        if (Dataseting::where('target', $validated['target'])
                      ->where('id', '<>', $dataseting->id)
                      ->exists()) {
            return redirect()
                ->route('admin.dataseting.index')
                ->with('error', $validated['target'] . ' の設定は既に存在しています。');
        }

        $dataseting->update($validated);

        return redirect()
            ->route('admin.dataseting.index')
            ->with('success', '設定を更新しました。');
    }

    /**
     * 削除処理
     */
    public function destroy(Dataseting $dataseting)
    {
        $dataseting->delete();

        return redirect()
            ->route('admin.dataseting.index')
            ->with('success', '設定を削除しました。');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrganicKeyword;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Validator;

class OrganicKeywordController extends Controller
{
    public function index()
    {
        $keywords = OrganicKeyword::orderBy('id', 'asc')->paginate(20);

        return view('admin.organic_keywords.index', compact('keywords'));
    }

    public function create()
    {
        return view('admin.organic_keywords.create');
    }

    public function edit($id)
    {
        $keyword = OrganicKeyword::findOrFail($id);
        return view('admin.organic_keywords.edit', compact('keyword'));
    }

    public function update(Request $request, $id)
    {
        $keyword = OrganicKeyword::findOrFail($id);

        $request->validate([
            'keyword' => 'required|string|max:255|unique:organic_keywords,keyword,' . $keyword->id,
            'product' => 'required|string|max:255',
            'priority' => 'required|string|max:255',
        ]);

        $keyword->update([
            'keyword' => $request->keyword,
            'product' => $request->product,
            'priority' => $request->priority,
        ]);

        return redirect()
            ->route('admin.organic_keywords.index')
            ->with('success', 'キーワードを更新しました！');
    }

    public function show($id)
    {
        $keyword = OrganicKeyword::findOrFail($id);
        return view('admin.organic_keywords.show', compact('keyword'));
    }

    public function destroy($id)
    {
        $keyword = OrganicKeyword::findOrFail($id);
        $keyword->delete();

        return redirect()
            ->route('admin.organic_keywords.index')
            ->with('success', 'キーワードを削除しました。');
    }

    /**
     * 保存処理
     */
    public function store(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|max:255|unique:organic_keywords,keyword',
            'product' => 'required|string|max:255',
            'priority' => 'required|string|max:255',
        ]);


        OrganicKeyword::create([
            'keyword' => $request->keyword,
            'product' => $request->product,
            'priority' => $request->priority,
        ]);

        return redirect()
            ->route('admin.organic_keywords.index')
            ->with('success', 'キーワードを追加しました！');
    }


    public function export(): StreamedResponse
    {
        $filename = 'organic_keywords_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Keyword', 'Product', 'Priority', 'Created At']);

            \App\Models\OrganicKeyword::cursor()->each(function ($keyword) use ($handle) {
                fputcsv($handle, [
                    $keyword->id,
                    $keyword->keyword,
                    $keyword->product,
                    $keyword->priority,
                    $keyword->created_at,
                ]);
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $file = fopen($path, 'r');
        $header = fgetcsv($file); // ヘッダー行をスキップ

        while ($row = fgetcsv($file)) {
            $data = [
                'keyword'  => $row[1] ?? null,
                'product'  => $row[2] ?? null,
                'priority' => $row[3] ?? null,
            ];

            $validator = Validator::make($data, [
                'keyword'  => 'required|string|max:255|unique:organic_keywords,keyword',
                'product'  => 'required|string|max:255',
                'priority' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                continue; // 無効な行はスキップ
            }

            \App\Models\OrganicKeyword::create($data);
        }

        fclose($file);

        return redirect()
            ->route('admin.organic_keywords.index')
            ->with('success', 'CSVをインポートしました');
    }
}

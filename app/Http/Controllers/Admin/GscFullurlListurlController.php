<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GscFullurlListurl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GscFullurlListurlController extends Controller
{
    public function index()
    {
        $urls = GscFullurlListurl::orderByDesc('created_at')->paginate(10);
        return view('admin.gsc_fullurl_listurls.gsc_full_index', compact('urls'));
    }

    public function show($id)
    {
        $url = GscFullurlListurl::findOrFail($id);
        return view('admin.gsc_fullurl_listurls.gsc_full_show', compact('url'));
    }

    public function create()
    {
        return view('admin.gsc_fullurl_listurls.gsc_full_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url|unique:gsc_fullurl_listurls,url',
            'is_active' => 'boolean',
        ]);

        GscFullurlListurl::create($validated);
        return redirect()->route('admin.gsc_fullurl_listurls.index')->with('success', 'URLを追加しました');
    }

    public function edit($id)
    {
        $url = GscFullurlListurl::findOrFail($id);
        return view('admin.gsc_fullurl_listurls.gsc_full_edit', compact('url'));
    }

    public function update(Request $request, $id)
    {
        $url = GscFullurlListurl::findOrFail($id);

        $validated = $request->validate([
            'url' => 'required|url|unique:gsc_fullurl_listurls,url,' . $url->id,
            'is_active' => 'boolean',
        ]);

        $url->update($validated);
        return redirect()->route('admin.gsc_fullurl_listurls.index')->with('success', 'URLを更新しました');
    }

    public function destroy($id)
    {
        $url = GscFullurlListurl::findOrFail($id);
        $url->delete();
        return redirect()->route('admin.gsc_fullurl_listurls.index')->with('success', 'URLを削除しました');
    }

    // ✅ CSVエクスポート
    public function export(): StreamedResponse
    {
        $filename = 'gsc_fullurl_list_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'URL', 'Is Active', 'Created At']);

            GscFullurlListurl::cursor()->each(function ($url) use ($handle) {
                fputcsv($handle, [
                    $url->id,
                    $url->url,
                    $url->is_active ? 1 : 0,
                    $url->created_at,
                ]);
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ✅ CSVインポート
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $file = fopen($path, 'r');
        $header = fgetcsv($file); // ヘッダー行読み飛ばし

        while ($row = fgetcsv($file)) {
            $data = [
                'url' => $row[1] ?? null,
                'is_active' => isset($row[2]) && $row[2] == 1,
            ];

            $validator = Validator::make($data, [
                'url' => 'required|url|unique:gsc_fullurl_listurls,url',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                continue; // エラーがあってもスキップして次の行へ
            }

            GscFullurlListurl::create($data);
        }

        fclose($file);

        return redirect()->route('admin.gsc_fullurl_listurls.index')->with('success', 'CSVをインポートしました');
    }

}

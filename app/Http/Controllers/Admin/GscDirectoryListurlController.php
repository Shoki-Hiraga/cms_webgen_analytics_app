<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GscDirectoryListurl;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Validator;

class GscDirectoryListurlController extends Controller
{
    public function index()
    {
        $urls = GscDirectoryListurl::orderByDesc('created_at')->paginate(10);
        return view('admin.gsc_directory_listurls.gsc_d_index', compact('urls'));
    }

    public function show($id)
    {
        $url = GscDirectoryListurl::findOrFail($id);
        return view('admin.gsc_directory_listurls.gsc_d_show', compact('url'));
    }

        public function create()
    {
        return view('admin.ga4_directory_listurls.ga4_d_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url|unique:ga4_directory_listurls,url',
            'is_active' => 'boolean',
        ]);

        GscDirectoryListurl::create($validated);
        return redirect()->route('admin.ga4_directory_listurls.index')->with('success', 'URLを追加しました');
    }

    public function edit($id)
    {
        $url = GscDirectoryListurl::findOrFail($id);
        return view('admin.ga4_directory_listurls.ga4_d_edit', compact('url'));
    }

    public function update(Request $request, $id)
    {
        $url = GscDirectoryListurl::findOrFail($id);

        $validated = $request->validate([
            'url' => 'required|url|unique:ga4_directory_listurls,url,' . $url->id,
            'is_active' => 'boolean',
        ]);

        $url->update($validated);
        return redirect()->route('admin.ga4_directory_listurls.index')->with('success', 'URLを更新しました');
    }

    public function destroy($id)
    {
        $url = GscDirectoryListurl::findOrFail($id);
        $url->delete();
        return redirect()->route('admin.ga4_directory_listurls.index')->with('success', 'URLを削除しました');
    }

    // CSV エクスポート
    public function export(): StreamedResponse
    {
        $filename = 'gsc_directory_list_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'URL', 'Is Active', 'Created At']);

            GscDirectoryListurl::cursor()->each(function ($url) use ($handle) {
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

    // CSV インポート
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $file = fopen($path, 'r');
        $header = fgetcsv($file); // skip header

        while ($row = fgetcsv($file)) {
            $data = [
                'url' => $row[1] ?? null,
                'is_active' => isset($row[2]) && $row[2] == 1,
            ];

            $validator = Validator::make($data, [
                'url' => 'required|url|unique:gsc_directory_listurls,url',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                continue;
            }

            GscDirectoryListurl::create($data);
        }

        fclose($file);

        return redirect()->route('admin.gsc_directory_listurls.index')->with('success', 'CSVをインポートしました');
    }

}

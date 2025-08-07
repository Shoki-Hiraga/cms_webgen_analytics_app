<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ga4FullurlListurl;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Validator;

class Ga4FullurlListurlController extends Controller
{
    public function index()
    {
        $urls = Ga4FullurlListurl::orderByDesc('created_at')->paginate(10);
        return view('admin.ga4_fullurl_listurls.ga4_full_index', compact('urls'));
    }

    public function show($id)
    {
        $url = Ga4FullurlListurl::findOrFail($id);
        return view('admin.ga4_fullurl_listurls.ga4_full_show', compact('url'));
    }

    public function create()
    {
        return view('admin.ga4_fullurl_listurls.ga4_full_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url|unique:ga4_fullurl_listurls,url',
            'is_active' => 'boolean',
        ]);

        Ga4FullurlListurl::create($validated);
        return redirect()->route('admin.ga4_fullurl_listurls.index')->with('success', 'URLを追加しました');
    }

    public function edit($id)
    {
        $url = Ga4FullurlListurl::findOrFail($id);
        return view('admin.ga4_fullurl_listurls.ga4_full_edit', compact('url'));
    }

    public function update(Request $request, $id)
    {
        $url = Ga4FullurlListurl::findOrFail($id);

        $validated = $request->validate([
            'url' => 'required|url|unique:ga4_fullurl_listurls,url,' . $url->id,
            'is_active' => 'boolean',
        ]);

        $url->update($validated);
        return redirect()->route('admin.ga4_fullurl_listurls.index')->with('success', 'URLを更新しました');
    }

    public function destroy($id)
    {
        $url = Ga4FullurlListurl::findOrFail($id);
        $url->delete();
        return redirect()->route('admin.ga4_fullurl_listurls.index')->with('success', 'URLを削除しました');
    }

    public function export(): StreamedResponse
    {
        $filename = 'ga4_fullurl_list_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'URL', 'Is Active', 'Created At']);

            Ga4FullurlListurl::cursor()->each(function ($url) use ($handle) {
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
                'url' => 'required|url|unique:ga4_fullurl_listurls,url',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                continue;
            }

            Ga4FullurlListurl::create($data);
        }

        fclose($file);

        return redirect()->route('admin.ga4_fullurl_listurls.index')->with('success', 'CSVをインポートしました');
    }


}

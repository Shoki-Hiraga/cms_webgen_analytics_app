<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GscQueryListquery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Validator;

class GscQueryListqueryController extends Controller
{
    public function index()
    {
        $queries = GscQueryListquery::orderByDesc('created_at')->paginate(10);
        return view('admin.gsc_query_listqueries.gsc_query_index', compact('queries'));
    }

    public function show($id)
    {
        $query = GscQueryListquery::findOrFail($id);
        return view('admin.gsc_query_listqueries.gsc_query_show', compact('query'));
    }

    public function create()
    {
        return view('admin.gsc_query_listqueries.gsc_query_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|unique:gsc_query_listqueries,query',
            'is_active' => 'boolean',
        ]);

        GscQueryListquery::create($validated);
        return redirect()->route('admin.gsc_query_listqueries.index')->with('success', 'クエリを追加しました');
    }

    public function edit($id)
    {
        $query = GscQueryListquery::findOrFail($id);
        return view('admin.gsc_query_listqueries.gsc_query_edit', compact('query'));
    }

    public function update(Request $request, $id)
    {
        $query = GscQueryListquery::findOrFail($id);

        $validated = $request->validate([
            'query' => 'required|string|unique:gsc_query_listqueries,query,' . $query->id,
            'is_active' => 'boolean',
        ]);

        $query->update($validated);
        return redirect()->route('admin.gsc_query_listqueries.index')->with('success', 'クエリを更新しました');
    }

    public function destroy($id)
    {
        $query = GscQueryListquery::findOrFail($id);
        $query->delete();
        return redirect()->route('admin.gsc_query_listqueries.index')->with('success', 'クエリを削除しました');
    }

    public function export(): StreamedResponse
    {
        $filename = 'gsc_queries_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Query', 'Is Active', 'Created At']);

            GscQueryListquery::cursor()->each(function ($query) use ($handle) {
                fputcsv($handle, [
                    $query->id,
                    $query->query,
                    $query->is_active ? 1 : 0,
                    $query->created_at,
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
                'query' => $row[1] ?? null,
                'is_active' => isset($row[2]) && $row[2] == 1,
            ];

            $validator = Validator::make($data, [
                'query' => 'required|unique:gsc_query_listqueries,query',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                continue; // スキップして次へ
            }

            GscQueryListquery::create($data);
        }

        fclose($file);

        return redirect()->route('admin.gsc_query_listqueries.index')->with('success', 'CSVをインポートしました');
    }

}

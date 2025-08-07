<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GscQueryListquery;
use Illuminate\Http\Request;

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
}

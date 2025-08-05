<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GscDirectoryListurl;

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

}

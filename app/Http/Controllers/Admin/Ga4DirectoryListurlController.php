<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ga4DirectoryListurl;
use Illuminate\Http\Request;

class Ga4DirectoryListurlController extends Controller
{
    public function index()
    {
        $urls = Ga4DirectoryListurl::orderByDesc('created_at')->paginate(10);
        return view('admin.ga4_directory_listurls.ga4_d_index', compact('urls'));
    }

    public function show($id)
    {
        $url = Ga4DirectoryListurl::findOrFail($id);
        return view('admin.ga4_directory_listurls.ga4_d_show', compact('url'));
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

        Ga4DirectoryListurl::create($validated);
        return redirect()->route('admin.ga4_directory_listurls.index')->with('success', 'URLを追加しました');
    }

    public function edit($id)
    {
        $url = Ga4DirectoryListurl::findOrFail($id);
        return view('admin.ga4_directory_listurls.ga4_d_edit', compact('url'));
    }

    public function update(Request $request, $id)
    {
        $url = Ga4DirectoryListurl::findOrFail($id);

        $validated = $request->validate([
            'url' => 'required|url|unique:ga4_directory_listurls,url,' . $url->id,
            'is_active' => 'boolean',
        ]);

        $url->update($validated);
        return redirect()->route('admin.ga4_directory_listurls.index')->with('success', 'URLを更新しました');
    }

    public function destroy($id)
    {
        $url = Ga4DirectoryListurl::findOrFail($id);
        $url->delete();
        return redirect()->route('admin.ga4_directory_listurls.index')->with('success', 'URLを削除しました');
    }

}

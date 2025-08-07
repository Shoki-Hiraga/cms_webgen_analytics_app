<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GscMediaUrlListurl;
use Illuminate\Http\Request;

class GscMediaUrlListurlController extends Controller
{
    public function index()
    {
        $urls = GscMediaUrlListurl::orderByDesc('created_at')->paginate(10);
        return view('admin.gsc_media_url_listurls.gsc_media_index', compact('urls'));
    }

    public function show($id)
    {
        $url = GscMediaUrlListurl::findOrFail($id);
        return view('admin.gsc_media_url_listurls.gsc_media_show', compact('url'));
    }

    public function create()
    {
        return view('admin.gsc_media_url_listurls.gsc_media_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url|unique:gsc_media_url_listurls,url',
            'is_active' => 'boolean',
        ]);

        GscMediaUrlListurl::create($validated);
        return redirect()->route('admin.gsc_media_url_listurls.index')->with('success', 'URLを追加しました');
    }

    public function edit($id)
    {
        $url = GscMediaUrlListurl::findOrFail($id);
        return view('admin.gsc_media_url_listurls.gsc_media_edit', compact('url'));
    }

    public function update(Request $request, $id)
    {
        $url = GscMediaUrlListurl::findOrFail($id);

        $validated = $request->validate([
            'url' => 'required|url|unique:gsc_media_url_listurls,url,' . $url->id,
            'is_active' => 'boolean',
        ]);

        $url->update($validated);
        return redirect()->route('admin.gsc_media_url_listurls.index')->with('success', 'URLを更新しました');
    }

    public function destroy($id)
    {
        $url = GscMediaUrlListurl::findOrFail($id);
        $url->delete();
        return redirect()->route('admin.gsc_media_url_listurls.index')->with('success', 'URLを削除しました');
    }
}

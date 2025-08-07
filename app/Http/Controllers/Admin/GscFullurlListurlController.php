<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GscFullurlListurl;
use Illuminate\Http\Request;

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
}

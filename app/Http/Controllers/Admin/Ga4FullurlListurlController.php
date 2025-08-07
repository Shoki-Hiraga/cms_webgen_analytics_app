<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ga4FullurlListurl;
use Illuminate\Http\Request;

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
}

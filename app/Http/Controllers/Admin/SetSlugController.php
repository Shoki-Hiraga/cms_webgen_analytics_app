<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SetSlug;
use Illuminate\Http\Request;

class SetSlugController extends Controller
{
    public function index()
    {
        $slugs = SetSlug::with('parent')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.set_slugs.index', compact('slugs'));
    }

    public function create()
    {
        $parents = SetSlug::orderBy('label')->get();
        return view('admin.set_slugs.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug'       => 'required|string|unique:set_slug,slug',
            'label'      => 'required|string',
            'type'       => 'required|in:ga4,gsc',
            'handler'    => 'nullable|string',
            'parent_id'  => 'nullable|exists:set_slug,id',
            'active'     => 'boolean',
            'sort_order' => 'integer',
        ]);

        SetSlug::create($validated);

        return redirect()
            ->route('admin.set_slugs.index')
            ->with('success', '登録しました');
    }

    public function edit(SetSlug $set_slug)
    {
        $parents = SetSlug::where('id', '!=', $set_slug->id)
            ->orderBy('label')
            ->get();

        return view('admin.set_slugs.edit', compact('set_slug', 'parents'));
    }

    public function update(Request $request, SetSlug $set_slug)
    {
        $validated = $request->validate([
            'slug'       => 'required|string|unique:set_slug,slug,' . $set_slug->id,
            'label'      => 'required|string',
            'type'       => 'required|in:ga4,gsc',
            'handler'    => 'nullable|string',
            'parent_id'  => 'nullable|exists:set_slug,id',
            'active'     => 'boolean',
            'sort_order' => 'integer',
        ]);

        $set_slug->update($validated);

        return redirect()
            ->route('admin.set_slugs.index')
            ->with('success', '更新しました');
    }

    public function destroy(SetSlug $set_slug)
    {
        $set_slug->delete();

        return redirect()
            ->route('admin.set_slugs.index')
            ->with('success', '削除しました');
    }
}

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
}

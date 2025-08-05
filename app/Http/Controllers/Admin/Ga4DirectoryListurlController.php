<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ga4DirectoryListurl;

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
}

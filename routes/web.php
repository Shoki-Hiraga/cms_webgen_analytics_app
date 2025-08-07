<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Ga4DirectoryListurlController;
use App\Http\Controllers\Admin\GscDirectoryListurlController;
use App\Http\Controllers\Admin\Ga4FullurlListurlController;
use App\Http\Controllers\Admin\GscFullurlListurlController;
use App\Http\Controllers\Admin\Ga4MediaUrlListurlController;
use App\Http\Controllers\Admin\GscMediaUrlListurlController;
use App\Http\Controllers\Admin\GscQueryListqueryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('ga4-dir-urls', Ga4DirectoryListurlController::class)
        ->names('ga4_directory_listurls');
    Route::resource('gsc-dir-urls', GscDirectoryListurlController::class)
        ->names('gsc_directory_listurls');

    Route::resource('ga4-fullurl-urls', Ga4FullurlListurlController::class)
        ->names('ga4_fullurl_listurls');
    Route::resource('gsc-fullurl-urls', GscFullurlListurlController::class)
        ->names('gsc_fullurl_listurls');

    Route::resource('ga4-media-urls', Ga4MediaUrlListurlController::class)
        ->names('ga4_media_url_listurls');
    Route::resource('gsc-media-urls', GscMediaUrlListurlController::class)
        ->names('gsc_media_url_listurls');

    Route::resource('gsc-query-queries', GscQueryListqueryController::class)
        ->names('gsc_query_listqueries');

});

require __DIR__.'/auth.php';

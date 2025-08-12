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
use App\Http\Controllers\Admin\Ga4SettingController;
use App\Http\Controllers\Admin\GscSettingController;


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
    return view('index');
})->name('top');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('gsc-query-queries/export', [GscQueryListqueryController::class, 'export'])->name('gsc_query_listqueries.export');
    Route::post('gsc-query-queries/import', [GscQueryListqueryController::class, 'import'])->name('gsc_query_listqueries.import');

    Route::get('gsc-media-urls/export', [GscMediaUrlListurlController::class, 'export'])->name('gsc_media_url_listurls.export');
    Route::post('gsc-media-urls/import', [GscMediaUrlListurlController::class, 'import'])->name('gsc_media_url_listurls.import');

    Route::get('gsc-fullurl-urls/export', [GscFullurlListurlController::class, 'export'])->name('gsc_fullurl_listurls.export');
    Route::post('gsc-fullurl-urls/import', [GscFullurlListurlController::class, 'import'])->name('gsc_fullurl_listurls.import');

    Route::get('gsc-dir-urls/export', [GscDirectoryListurlController::class, 'export'])->name('gsc_directory_listurls.export');
    Route::post('gsc-dir-urls/import', [GscDirectoryListurlController::class, 'import'])->name('gsc_directory_listurls.import');

    Route::get('ga4-media-urls/export', [Ga4MediaUrlListurlController::class, 'export'])->name('ga4_media_url_listurls.export');
    Route::post('ga4-media-urls/import', [Ga4MediaUrlListurlController::class, 'import'])->name('ga4_media_url_listurls.import');

    Route::get('ga4-fullurl-urls/export', [Ga4FullurlListurlController::class, 'export'])->name('ga4_fullurl_listurls.export');
    Route::post('ga4-fullurl-urls/import', [Ga4FullurlListurlController::class, 'import'])->name('ga4_fullurl_listurls.import');

    Route::get('ga4-dir-urls/export', [Ga4DirectoryListurlController::class, 'export'])->name('ga4_directory_listurls.export');
    Route::post('ga4-dir-urls/import', [Ga4DirectoryListurlController::class, 'import'])->name('ga4_directory_listurls.import');



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



    Route::resource('ga4-settings', Ga4SettingController::class)
        ->names('ga4_settings')
        ->except(['show']);

    Route::resource('gsc-settings', GscSettingController::class)
        ->names('gsc_settings')
        ->except(['show']);

});

require __DIR__.'/auth.php';

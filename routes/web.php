<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Ga4DirectoryListurlController;
use App\Http\Controllers\Admin\GscDirectoryListurlController;

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
    Route::get('ga4-dir-urls', [Ga4DirectoryListurlController::class, 'index'])->name('urls.index');
    Route::get('ga4-dir-urls/{id}', [Ga4DirectoryListurlController::class, 'show'])->name('urls.show');
});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('gsc-dir-urls', [GscDirectoryListurlController::class, 'index'])->name('admin.gsc_directory_listurls.index');
    Route::get('gsc-dir-urls/{id}', [GscDirectoryListurlController::class, 'show'])->name('admin.gsc_directory_listurls.show');
});

require __DIR__.'/auth.php';

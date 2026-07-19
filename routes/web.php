<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\InvitationPublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/sitemap.xml', [InvitationPublicController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [InvitationPublicController::class, 'robots'])->name('robots');

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::prefix('admin')->name('admin.')->middleware('demo.admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/katalog', [CatalogController::class, 'index'])->name('katalog.index');
    Route::put('/katalog', [CatalogController::class, 'update'])->name('katalog.update');
    Route::get('/undangan', [InvitationController::class, 'index'])->name('undangan.index');
    Route::get('/undangan/buat', [InvitationController::class, 'create'])->name('undangan.create');
    Route::post('/undangan/pilih-template', [InvitationController::class, 'pilihTemplate'])->name('undangan.pilih-template');
    Route::get('/undangan/form', [InvitationController::class, 'form'])->name('undangan.form');
    Route::post('/undangan', [InvitationController::class, 'store'])->name('undangan.store');
    Route::post('/undangan/purge-expired', [InvitationController::class, 'purgeExpired'])->name('undangan.purge-expired');
    Route::get('/undangan/{id}/edit', [InvitationController::class, 'edit'])->name('undangan.edit');
    Route::put('/undangan/{id}', [InvitationController::class, 'update'])->name('undangan.update');
    Route::delete('/undangan/{id}', [InvitationController::class, 'destroy'])->name('undangan.destroy');
    Route::get('/undangan/{id}/laporan', [InvitationController::class, 'laporan'])->name('undangan.laporan');
});

Route::get('/{slug}', [InvitationPublicController::class, 'show'])
    ->where('slug', '^(?!admin$).*$')
    ->name('undangan.show');

Route::post('/{slug}/ucapan', [InvitationPublicController::class, 'storeUcapan'])
    ->where('slug', '^(?!admin$).*$')
    ->name('undangan.ucapan');

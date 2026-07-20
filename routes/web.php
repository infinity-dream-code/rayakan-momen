<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\InvitationPublicController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/sitemap.xml', [InvitationPublicController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [InvitationPublicController::class, 'robots'])->name('robots');

/*
| Login rahasia: /SmartLoginAdmin
| Panel: /panel/...
*/
Route::get('/SmartLoginAdmin', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/SmartLoginAdmin', [AuthController::class, 'login'])->name('admin.login.submit');
Route::get('/SmartLoginAdmin/pin', [AuthController::class, 'showPin'])->name('admin.pin');
Route::post('/SmartLoginAdmin/pin', [AuthController::class, 'verifyPin'])->name('admin.pin.submit');
Route::post('/panel/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Redirect lama → login baru
Route::redirect('/panel/login', '/SmartLoginAdmin', 301);

Route::prefix('panel')->name('admin.')->middleware('demo.admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/campaign', [CampaignController::class, 'index'])->name('campaign.index');
    Route::match(['put', 'post'], '/campaign', [CampaignController::class, 'update'])->name('campaign.update');
    Route::get('/katalog', [CatalogController::class, 'index'])->name('katalog.index');
    Route::match(['put', 'post'], '/katalog', [CatalogController::class, 'update'])->name('katalog.update');
    Route::get('/undangan', [InvitationController::class, 'index'])->name('undangan.index');
    Route::get('/undangan/buat', [InvitationController::class, 'create'])->name('undangan.create');
    Route::post('/undangan/pilih-template', [InvitationController::class, 'pilihTemplate'])->name('undangan.pilih-template');
    Route::get('/undangan/form', [InvitationController::class, 'form'])->name('undangan.form');
    Route::post('/undangan', [InvitationController::class, 'store'])->name('undangan.store');
    Route::post('/undangan/purge-expired', [InvitationController::class, 'purgeExpired'])->name('undangan.purge-expired');
    Route::get('/undangan/{id}/edit', [InvitationController::class, 'edit'])->name('undangan.edit');
    Route::match(['put', 'post'], '/undangan/{id}', [InvitationController::class, 'update'])->name('undangan.update');
    Route::delete('/undangan/{id}', [InvitationController::class, 'destroy'])->name('undangan.destroy');
    Route::get('/undangan/{id}/laporan', [InvitationController::class, 'laporan'])->name('undangan.laporan');
});

Route::get('/{slug}', [InvitationPublicController::class, 'show'])
    ->where('slug', '^(?!admin$|panel$|SmartLoginAdmin$).*$')
    ->name('undangan.show');

Route::post('/{slug}/ucapan', [InvitationPublicController::class, 'storeUcapan'])
    ->where('slug', '^(?!admin$|panel$|SmartLoginAdmin$).*$')
    ->name('undangan.ucapan');

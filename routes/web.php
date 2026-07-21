<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\InvitationPublicController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\RsvpDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/katalog', [LandingController::class, 'katalog'])->name('katalog');

Route::get('/sitemap.xml', [InvitationPublicController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [InvitationPublicController::class, 'robots'])->name('robots');

Route::get('/dashboard-rsvp/{token}', [RsvpDashboardController::class, 'show'])
    ->where('token', '[A-Za-z0-9_-]+')
    ->name('rsvp.dashboard');

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
    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');
    Route::post('/setting/{key}/gambar', [SettingController::class, 'updateImage'])->name('setting.image');
    Route::redirect('/katalog', '/panel/setting', 301);
    Route::redirect('/gambar-template', '/panel/setting', 301);
    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/export', [TransactionController::class, 'export'])->name('transaksi.export');
    Route::delete('/transaksi/{id}', [TransactionController::class, 'destroy'])->name('transaksi.destroy');
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
    ->where('slug', '^(?!admin$|panel$|SmartLoginAdmin$|dashboard-rsvp$|katalog$).*$')
    ->name('undangan.show');

Route::post('/{slug}/ucapan', [InvitationPublicController::class, 'storeUcapan'])
    ->where('slug', '^(?!admin$|panel$|SmartLoginAdmin$|dashboard-rsvp$|katalog$).*$')
    ->name('undangan.ucapan');

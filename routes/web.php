<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoodcourtController;
use App\Http\Controllers\ImportRiwayatController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\KasBankController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\QmasController;
use App\Http\Controllers\AsetTetapController;
use App\Http\Controllers\StokController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/qmas/login', [AuthenticatedSessionController::class, 'createPortal'])->defaults('portal', 'qmas')->name('qmas.login');
Route::get('/foodcourt/login', [AuthenticatedSessionController::class, 'createPortal'])->defaults('portal', 'foodcourt')->name('foodcourt.login');
Route::get('/suara-muhammadiyah/login', [AuthenticatedSessionController::class, 'createPortal'])->defaults('portal', 'sm')->name('sm.login');
Route::get('/admin/login', [AuthenticatedSessionController::class, 'createPortal'])->defaults('portal', null)->name('admin.login');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('portal:qmas')->group(function () {
        Route::get('/qmas', [QmasController::class, 'index'])->name('qmas.index');
        Route::get('/qmas/stok', [StokController::class, 'index'])->name('qmas.stok.index');
        Route::post('/qmas/import-kartu-kontrol', [QmasController::class, 'importKartuKontrol'])->name('qmas.importKartuKontrol');
        Route::post('/qmas/stok/adjust', [StokController::class, 'adjust'])->name('qmas.stok.adjust');
        Route::post('/qmas/stok/transfer', [StokController::class, 'transfer'])->name('qmas.stok.transfer');
        Route::post('/qmas/stok/inline', [StokController::class, 'inline'])->name('qmas.stok.inline');
        Route::get('/qmas/dashboard', [QmasController::class, 'dashboard'])->name('qmas.dashboard');
        Route::get('/qmas/produk', [QmasController::class, 'produk'])->name('qmas.produk');
        Route::resource('qmas/penjualan', PenjualanController::class)->names('qmas.penjualan')->except(['show','create','edit']);
        Route::resource('qmas/pembelian', PembelianController::class)->names('qmas.pembelian')->except(['show','create','edit','update']);
        Route::post('/qmas/pembelian/import-stok-masuk', [PembelianController::class, 'importStokMasuk'])->name('qmas.pembelian.importStokMasuk');
        Route::get('/qmas/kas-bank', [KasBankController::class, 'index'])->name('qmas.kasBank.index');
        Route::post('/qmas/kas-bank/akun', [KasBankController::class, 'storeAkun'])->name('qmas.kasBank.storeAkun');
        Route::post('/qmas/kas-bank/transaksi', [KasBankController::class, 'storeTransaksi'])->name('qmas.kasBank.storeTransaksi');
        Route::delete('/qmas/kas-bank/transaksi/{transaksi}', [KasBankController::class, 'destroyTransaksi'])->name('qmas.kasBank.destroyTransaksi');
        Route::get('/qmas/laporan', [LaporanController::class, 'index'])->name('qmas.laporan');
        Route::get('/qmas/impor-riwayat', [ImportRiwayatController::class, 'index'])->name('qmas.imporRiwayat');
        Route::post('/qmas/impor-riwayat', [ImportRiwayatController::class, 'prosesImport'])->name('qmas.imporRiwayat.proses');
        Route::delete('/qmas/impor-riwayat/{import_riwayat}', [ImportRiwayatController::class, 'destroy'])->name('qmas.imporRiwayat.destroy');
        Route::get('/qmas/kontak', [KontakController::class, 'index'])->name('qmas.kontak');
        Route::post('/qmas/kontak', [KontakController::class, 'store'])->name('qmas.kontak.store');
        Route::put('/qmas/kontak/{kontak}', [KontakController::class, 'update'])->name('qmas.kontak.update');
        Route::delete('/qmas/kontak/{kontak}', [KontakController::class, 'destroy'])->name('qmas.kontak.destroy');
        Route::get('/qmas/aset-tetap', [AsetTetapController::class, 'index'])->name('qmas.asetTetap');
        Route::post('/qmas/aset-tetap', [AsetTetapController::class, 'store'])->name('qmas.asetTetap.store');
        Route::delete('/qmas/aset-tetap/{aset_tetap}', [AsetTetapController::class, 'destroy'])->name('qmas.asetTetap.destroy');
        Route::get('/qmas/marketplace', [QmasController::class, 'marketplace'])->name('qmas.marketplace');
        Route::get('/qmas/kasir', [KasirController::class, 'index'])->name('qmas.kasir');
        Route::post('/qmas/kasir', [KasirController::class, 'store'])->name('qmas.kasir.store');
        Route::resource('qmas/produk', ProdukController::class)->names('qmas.produk')->except(['show', 'create', 'edit']);
        Route::get('/qmas/create', [QmasController::class, 'create'])->name('qmas.create');
        Route::post('/qmas', [QmasController::class, 'store'])->name('qmas.store');
    });

    Route::middleware('portal:foodcourt')->group(function () {
        Route::get('/foodcourt', [FoodcourtController::class, 'index'])->name('foodcourt.index');
        Route::post('/foodcourt', [FoodcourtController::class, 'store'])->name('foodcourt.store');
        Route::get('/foodcourt/dashboard', [FoodcourtController::class, 'dashboard'])->name('foodcourt.dashboard');
        Route::get('/foodcourt/laporan', [FoodcourtController::class, 'laporan'])->name('foodcourt.laporan');
    });

    Route::middleware('portal:sm')->group(function () {
        Route::get('/suara-muhammadiyah', [\App\Http\Controllers\SuaraMuhammadiyahController::class, 'index'])->name('sm.index');
        Route::get('/suara-muhammadiyah/dashboard', [\App\Http\Controllers\SuaraMuhammadiyahController::class, 'dashboard'])->name('sm.dashboard');
        Route::get('/suara-muhammadiyah/laporan', [\App\Http\Controllers\SuaraMuhammadiyahController::class, 'laporan'])->name('sm.laporan');
        Route::get('/suara-muhammadiyah/katalog', [\App\Http\Controllers\SuaraMuhammadiyahController::class, 'katalog'])->name('sm.katalog');
        Route::get('/suara-muhammadiyah/kasir', [\App\Http\Controllers\SuaraMuhammadiyahController::class, 'kasir'])->name('sm.kasir');
        Route::get('/suara-muhammadiyah/stok', [\App\Http\Controllers\SuaraMuhammadiyahController::class, 'stok'])->name('sm.stok');
        Route::get('/suara-muhammadiyah/scan', [\App\Http\Controllers\SuaraMuhammadiyahController::class, 'scan'])->name('sm.scan');
    });

    Route::middleware('master_admin')->group(function () {
        Route::get('/admin/portal', [\App\Http\Controllers\AdminPortalController::class, 'index'])->name('admin.portal');
        Route::post('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
    });

    Route::resource('menus', MenuController::class)->except(['show']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/midtrans/notification', [\App\Http\Controllers\MidtransController::class, 'notification'])->name('midtrans.notification');
Route::get('/midtrans/finish', [\App\Http\Controllers\MidtransController::class, 'finish'])->name('midtrans.finish');

require __DIR__.'/auth.php';

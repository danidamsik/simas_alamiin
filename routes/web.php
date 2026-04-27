<?php

use App\Http\Controllers\Admin\UserPasswordController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\PeriodeController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AbsensiEditController;
use App\Http\Controllers\AbsensiHistoryController;
use App\Http\Controllers\AbsensiInputController;
use App\Http\Controllers\ProfileController;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(RouteServiceProvider::HOME);
})->middleware('auth');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::put('/users/{user}/password', [UserPasswordController::class, 'update'])
        ->name('users.password.update');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::patch('/periode/{periode}/set-active', [PeriodeController::class, 'setActive'])
        ->name('periode.set-active');

    Route::resource('periode', PeriodeController::class)->except(['show']);
    Route::resource('kelas', KelasController::class)->except(['show'])->parameters(['kelas' => 'kelas']);
    Route::resource('siswa', SiswaController::class)->except(['show']);
    Route::resource('guru', GuruController::class)->except(['show']);
    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('sessions', SessionController::class)->except(['show']);
    Route::resource('jadwal', JadwalController::class)->except(['show']);
});

Route::middleware(['auth', 'role:admin,guru'])->group(function () {
    Route::get('/absensi/input', [AbsensiInputController::class, 'index'])->name('absensi.input.index');
    Route::get('/absensi/input/{jadwal}', [AbsensiInputController::class, 'create'])->name('absensi.input.create');
    Route::post('/absensi/input/{jadwal}', [AbsensiInputController::class, 'store'])->name('absensi.input.store');
    Route::get('/absensi/riwayat', [AbsensiHistoryController::class, 'index'])->name('absensi.riwayat');
    Route::get('/absensi/{absensi}/edit', [AbsensiEditController::class, 'edit'])->name('absensi.edit');
    Route::put('/absensi/{absensi}', [AbsensiEditController::class, 'update'])->name('absensi.update');
});

Route::middleware(['auth', 'role:admin,kepala_sekolah'])->group(function () {
    Route::get('/absensi', [AbsensiHistoryController::class, 'index'])->name('absensi.index');
});

require __DIR__.'/auth.php';

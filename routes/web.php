<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Backend\LogActivity\LogActivityController;
use App\Http\Controllers\Backend\MyProfile\AccountController;
use App\Http\Controllers\Backend\MyProfile\ProfileController;
use App\Http\Controllers\Backend\MyProfile\ActivityController;
use App\Http\Controllers\Backend\MyProfile\LoginSessionController;
use App\Http\Controllers\Backend\MyProfile\SecurityController;


//MASTER WILAYAH
use App\Http\Controllers\Backend\Master\Wilayah\WilayahProvinsiController;
use App\Http\Controllers\Backend\Master\Wilayah\WilayahKabupatenController;
use App\Http\Controllers\Backend\Master\Wilayah\WilayahKecamatanController;
use App\Http\Controllers\Backend\Master\Wilayah\WilayahDesaController;
use App\Http\Controllers\Backend\Master\SkpdController;
use App\Http\Controllers\Backend\UserManagement\UserController;
use App\Http\Controllers\Backend\UserManagement\RoleController;



//FRONTEND
use App\Http\Controllers\Frontend\BerandaController;





Route::get('/', [BerandaController::class, 'index'])->name('beranda.index');
Route::get('/', [BerandaController::class, 'index'])->name('dashboard.index');




Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');


Route::middleware(['frontend.auth'])->group(function () {

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');



Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/help/log-activity', [LogActivityController::class, 'index'])->name('help.log-activity.index');
Route::get('/help/log-activity/data', [LogActivityController::class, 'getData'])->name('help.log-activity.data');
Route::get('/help/log-activity/{id}', [LogActivityController::class, 'show'])->name('help.log-activity.show');


Route::get('/my-account', [AccountController::class, 'index'])->name('account.index');
Route::get('my-account/{id}/avatar', [AccountController::class,'editAvatar'])->name('account.avatar-edit');
Route::post('my-account/{id}/update-avatar', [AccountController::class,'updateAvatar'])->name('account.avatar-update');

Route::resource('my-profile', ProfileController::class);
Route::get('/my-activity', [ActivityController::class, 'index'])->name('my-activity.index');
Route::get('my-activity/data', [ActivityController::class, 'getData'])->name('my-activity.data');
Route::get('/my-login-session', [LoginSessionController::class, 'index'])->name('my-login-session.index');
Route::get('my-login-session/data', [LoginSessionController::class, 'getData'])->name('my-login-session.data');
Route::resource('my-security', SecurityController::class);



//MASTER WILAYAH
Route::get('/master/wilayah-provinsi', [WilayahProvinsiController::class, 'index'])->name('provinsi.index');
Route::get('/master/wilayah-provinsi/data', [WilayahProvinsiController::class, 'getData'])->name('provinsi.data');

Route::get('/master/wilayah-kabupaten', [WilayahKabupatenController::class, 'index'])->name('kabupaten.index');
Route::get('/master/wilayah-kabupaten/data', [WilayahKabupatenController::class, 'getData'])->name('kabupaten.data');

Route::get('/master/wilayah-kecamatan', [WilayahKecamatanController::class, 'index'])->name('kecamatan.index');
Route::get('/master/wilayah-kecamatan/data', [WilayahKecamatanController::class, 'getData'])->name('kecamatan.data');

Route::get('/master/wilayah-desa', [WilayahDesaController::class, 'index'])->name('desa.index');
Route::get('/master/wilayah-desa/data', [WilayahDesaController::class, 'getData'])->name('desa.data');


Route::prefix('master')->group(function () {
    Route::get('/skpd/data', [SkpdController::class, 'getData'])->name('skpd.data');
    Route::resource('/skpd', SkpdController::class)->names('skpd');
});

Route::prefix('user-management')->group(function () {
    Route::get('/user/data', [UserController::class, 'getData'])->name('user.data');
    Route::resource('/user', UserController::class)->names('user');
});

Route::prefix('user-management')->group(function () {
    Route::get('/role/data', [RoleController::class, 'getData'])->name('role.data');
    Route::get('/role/permissions', [RoleController::class, 'getPermissions'])->name('role.permissions');
    Route::get('/role/select', [RoleController::class, 'select'])->name('role.select');
    Route::resource('/role', RoleController::class)->names('role');
});
});


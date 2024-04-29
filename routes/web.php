<?php

use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\{PemilikController, PenyewaController, UserController, RoleController, PermissionController, StoreController};
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

// Route::get('/', function () {
//     return view('welcome');
// });


Auth::routes();

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/postlogin', [LoginController::class, 'postlogin'])->name('postlogin');
Route::post('/postregister', [RegisterController::class, 'postregister'])->name('postregister');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home.index');
Route::group(['middleware' => ['auth']], function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::resource('/users', UserController::class);
    Route::post('/users', 'UserController@store')->name('users.store');
    Route::resource('users', UserController::class);
    Route::put('/users/{id}', 'UserController@update')->name('users.update');
    Route::delete('users/{id}', 'UserController@destroy')->name('users.destroy');
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::resource('/roles', RoleController::class);
    Route::post('/roles', 'RoleController@store')->name('roles.store');
    Route::resource('roles', RoleController::class);
    Route::put('/roles/{id}', 'RoleController@update')->name('roles.update');
    Route::delete('roles/{id}', 'RoleController@destroy')->name('roles.destroy');
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::resource('/permissions', PermissionController::class);
    Route::post('/permissions', 'PermissionController@store')->name('permissions.store');
    Route::resource('permissions', PermissionController::class);
    Route::put('/permissions/{id}', 'PermissionController@update')->name('permissions.update');
    Route::delete('permissions/{id}', 'PermissionController@destroy')->name('permissions.destroy');
    Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');
    Route::resource('/stores', StoreController::class);
    Route::post('/stores', 'StoreController@store')->name('stores.store');
    Route::get('/stores/{id}', 'StoreController@show')->name('stores.show');
    Route::resource('/stores', StoreController::class);
    Route::put('/stores/{id}', 'StoreController@update')->name('stores.update');
    Route::delete('stores/{id}', 'StoreController@destroy')->name('stores.destroy');
    Route::get('/penyewa', [PenyewaController::class, 'index'])->name('penyewa.index');
    Route::resource('/penyewa', PenyewaController::class);
    Route::post('/penyewa', 'PenyewaController@store')->name('penyewa.store');
    Route::get('/penyewa/{id}', 'PenyewaController@show')->name('penyewa.show');
    Route::resource('/penyewa', PenyewaController::class);
    Route::put('/penyewa/{id}', 'PenyewaController@update')->name('penyewa.update');
    Route::delete('penyewa/{id}', 'PenyewaController@destroy')->name('penyewa.destroy');
    Route::get('/pemilik', [PemilikController::class, 'index'])->name('pemilik.index');
    Route::resource('/pemilik', PemilikController::class);
    Route::post('/pemilik', 'PemilikController@store')->name('pemilik.store');
    Route::get('/pemilik/{id}', 'PemilikController@show')->name('pemilik.show');
    Route::resource('/pemilik', PemilikController::class);
    Route::put('/pemilik/{id}', 'PemilikController@update')->name('pemilik.update');
    Route::delete('pemilik/{id}', 'PemilikController@destroy')->name('pemilik.destroy');
})->middleware('web');
    
<?php

use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\{UserController, RoleController, PermissionController};
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

Route::post('/postlogin', [LoginController::class, 'postlogin'])->name('postlogin');
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
})->middleware('web');
    
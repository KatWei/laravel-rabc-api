<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthorizationController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'auth:admin-api'
], function(){
    Route::get('/user', [AdminUsersController::class, 'show']);
    Route::resource('/admin_users', AdminUsersController::class)->only('index', 'store', 'update', 'destroy');
    Route::get('/roles/all_roles', [RolesController::class, 'getAllRole']);
    Route::resource('/roles', RolesController::class)->only('index', 'store', 'update', 'destroy');
    Route::resource('/permissions', PermissionsController::class)->only('index', 'store', 'update', 'destroy');
    Route::get('/permissions/role_by_permissions/{roleId?}', [PermissionsController::class, 'getRoleByPermissions']);
});

Route::group([
    'middleware'=> 'api',
    'prefix' => 'auth'
], function(){
    Route::post('/login', [AuthorizationController::class, 'login']);
    Route::post('/register', [AuthorizationController::class, 'register']);
    Route::post('/logout', [AuthorizationController::class, 'logout']);
    Route::post('/refresh', [AuthorizationController::class, 'refresh']);
});

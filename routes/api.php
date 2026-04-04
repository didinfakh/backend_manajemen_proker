<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
});
Route::middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('sys-permissions', \App\Http\Controllers\SysPermissionsController::class);
    });

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('sys-user-groups', \App\Http\Controllers\SysUserGroupsController::class);
    });

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('sys-menu-permissions', \App\Http\Controllers\SysMenuPermissionsController::class);
    });

    Route::middleware('auth:sanctum')
    ->group(function () {
        Route::get('sys-menus/tree', [\App\Http\Controllers\SysMenuController::class, 'getTree']);
        Route::apiResource('sys-menus', \App\Http\Controllers\SysMenuController::class);
    });

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('sys-users', \App\Http\Controllers\SysUserController::class);
        Route::apiResource('sys-groups', \App\Http\Controllers\SysGroupController::class);
    });

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('sys-group-permissions', \App\Http\Controllers\SysGroupPermissionsController::class);
        Route::get('sys-group-permissions/{id}/mapping', [\App\Http\Controllers\SysGroupPermissionsController::class, 'mapping']);
        Route::post('sys-group-permissions/{id}/sync', [\App\Http\Controllers\SysGroupPermissionsController::class, 'sync']);
        Route::post('sys-group-permissions/add-action', [\App\Http\Controllers\SysGroupPermissionsController::class, 'addAction']);
        Route::post('sys-group-permissions/remove-action', [\App\Http\Controllers\SysGroupPermissionsController::class, 'removeAction']);
    });

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('program-kerja', \App\Http\Controllers\ProgramKerjaController::class);
        Route::apiResource('program-sie', \App\Http\Controllers\ProgramSieController::class);
        Route::apiResource('program-sie-member', \App\Http\Controllers\ProgramSieMemberController::class);
        Route::apiResource('task-status', \App\Http\Controllers\TaskStatusController::class);
    });


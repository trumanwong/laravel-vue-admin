<?php

use App\Models\Acl;
use Illuminate\Contracts\Routing\Registrar as RouteContract;
use Illuminate\Support\Facades\Route;

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

Route::namespace('Api')->group(function() {
    Route::post('auth/login', 'AuthController@login');
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('auth/logout', 'AuthController@logout');

        Route::get('/user', 'AuthController@user');

        // Api resource routes
        Route::apiResource('roles', 'RoleController')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::apiResource('users', 'UserController')->middleware('permission:' . Acl::PERMISSION_USER_MANAGE);
        Route::apiResource('permissions', 'PermissionController')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);

        // Custom routes
        Route::group(['prefix' => 'users'], function (RouteContract $api) {
            $api->get('{user}/permissions', 'UserController@permissions')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
            $api->put('{user}/permissions', 'UserController@updatePermissions')->middleware('permission:' .Acl::PERMISSION_PERMISSION_MANAGE);
            $api->get('{user}/logs', 'LogController@index');
        });

        Route::get('roles/{role}/permissions', 'RoleController@permissions')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::get('requests', 'RequestController@index');
    });
});

Route::get('/orders', function () {
    $rowsNumber = 8;
    $data = [];
    for ($rowIndex = 0; $rowIndex < $rowsNumber; $rowIndex++) {
        $row = [
            'order_no' => 'LARAVUE' . mt_rand(1000000, 9999999),
            'price' => mt_rand(10000, 999999),
            'status' => randomInArray(['success', 'pending']),
        ];

        $data[] = $row;
    }

    return responseSuccess(['items' => $data]);
});
<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/', 'AuthController@login');

Route::middleware('authentication')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::match(['get', 'post'], 'change-password', 'AuthController@changePassword');
        Route::get('logout', 'AuthController@logout');
    });

    Route::get('home', function () {
        $data = [
            'content' => 'home'
        ];

        return view('layouts.index', ['data' => $data]);
    });

    Route::prefix('master-data')->namespace('MasterData')->group(function () {
        Route::prefix('visit')->group(function () {
            Route::get('/', 'VisitController@index');
            Route::get('datatable', 'VisitController@datatable');
            Route::post('create-data', 'VisitController@createData');
            Route::get('show-data', 'VisitController@showData');
            Route::post('update-data', 'VisitController@updateData');
            Route::delete('destroy-data', 'VisitController@destroyData');
        });

        Route::prefix('contributor')->group(function () {
            Route::get('/', 'ContributorController@index');
            Route::get('datatable', 'ContributorController@datatable');
            Route::post('create-data', 'ContributorController@createData');
            Route::get('show-data', 'ContributorController@showData');
            Route::post('update-data', 'ContributorController@updateData');
            Route::delete('destroy-data', 'ContributorController@destroyData');
        });

        Route::prefix('category')->group(function () {
            Route::get('/', 'CategoryController@index');
            Route::get('datatable', 'CategoryController@datatable');
            Route::post('create-data', 'CategoryController@createData');
            Route::get('show-data', 'CategoryController@showData');
            Route::post('update-data', 'CategoryController@updateData');
            Route::delete('destroy-data', 'CategoryController@destroyData');
        });

        Route::prefix('subject')->group(function () {
            Route::get('/', 'SubjectController@index');
            Route::get('datatable', 'SubjectController@datatable');
            Route::post('create-data', 'SubjectController@createData');
            Route::get('show-data', 'SubjectController@showData');
            Route::post('update-data', 'SubjectController@updateData');
            Route::delete('destroy-data', 'SubjectController@destroyData');
        });
    });
});

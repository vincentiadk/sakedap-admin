<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/', 'AuthController@login');

Route::middleware('authentication')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::match(['get', 'post'], 'change-password', 'AuthController@changePassword');
        Route::get('logout', 'AuthController@logout');
    });

    Route::prefix('select2-serverside')->group(function () {
        Route::get('province', 'Select2ServersideController@province');
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

        Route::prefix('problem')->group(function () {
            Route::get('/', 'ProblemController@index');
            Route::get('datatable', 'ProblemController@datatable');
            Route::post('create-data', 'ProblemController@createData');
            Route::get('show-data', 'ProblemController@showData');
            Route::post('update-data', 'ProblemController@updateData');
            Route::delete('destroy-data', 'ProblemController@destroyData');
        });

        Route::prefix('author')->group(function () {
            Route::get('/', 'AuthorController@index');
            Route::get('datatable', 'AuthorController@datatable');
            Route::post('create-data', 'AuthorController@createData');
            Route::get('show-data', 'AuthorController@showData');
            Route::post('update-data', 'AuthorController@updateData');
            Route::delete('destroy-data', 'AuthorController@destroyData');
        });

        Route::prefix('organization')->group(function () {
            Route::get('/', 'OrganizationController@index');
            Route::get('datatable', 'OrganizationController@datatable');
            Route::post('create-data', 'OrganizationController@createData');
            Route::get('show-data', 'OrganizationController@showData');
            Route::post('update-data', 'OrganizationController@updateData');
            Route::delete('destroy-data', 'OrganizationController@destroyData');
        });
    });

    Route::prefix('location')->namespace('Location')->group(function () {
        Route::prefix('province')->group(function () {
            Route::get('/', 'ProvinceController@index');
            Route::get('datatable', 'ProvinceController@datatable');
            Route::post('create-data', 'ProvinceController@createData');
            Route::get('show-data', 'ProvinceController@showData');
            Route::post('update-data', 'ProvinceController@updateData');
            Route::delete('destroy-data', 'ProvinceController@destroyData');
        });

        Route::prefix('city')->group(function () {
            Route::get('/', 'CityController@index');
            Route::get('datatable', 'CityController@datatable');
            Route::post('create-data', 'CityController@createData');
            Route::get('show-data', 'CityController@showData');
            Route::post('update-data', 'CityController@updateData');
            Route::delete('destroy-data', 'CityController@destroyData');
        });
    });
});

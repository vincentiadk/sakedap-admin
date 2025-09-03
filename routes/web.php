<?php

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/', 'AuthController@login');

Route::get('stream-file', function (Request $request) {
    if ($request->type && $request->id && $request->filename) {
        return QueryAPI::getFile([
            'type' => $request->type,
            'id' => $request->id,
            'filename' => $request->filename,
        ]);
    }
});

Route::prefix('download')->group(function () {
    Route::get('from-public', 'DownloadController@fromPublic');
    Route::get('request-file', 'DownloadController@requestFile');
});

Route::middleware('authentication')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::match(['get', 'post'], 'change-password', 'AuthController@changePassword');
        Route::get('logout', 'AuthController@logout');
    });

    Route::prefix('select2-serverside')->group(function () {
        Route::get('province', 'Select2ServersideController@province');
        Route::get('city', 'Select2ServersideController@city');
        Route::get('district', 'Select2ServersideController@district');
        Route::get('branch', 'Select2ServersideController@branch');
        Route::get('publisher', 'Select2ServersideController@publisher');
        Route::get('location', 'Select2ServersideController@location');
        Route::get('collection-parent', 'Select2ServersideController@collectionParent');
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

        Route::prefix('category')->group(function () {
            Route::get('/', 'CategoryController@index');
            Route::get('datatable', 'CategoryController@datatable');
            Route::post('create-data', 'CategoryController@createData');
            Route::get('show-data', 'CategoryController@showData');
            Route::post('update-data', 'CategoryController@updateData');
            Route::delete('destroy-data', 'CategoryController@destroyData');
        });

        Route::prefix('problem')->group(function () {
            Route::get('/', 'ProblemController@index');
            Route::get('datatable', 'ProblemController@datatable');
            Route::post('create-data', 'ProblemController@createData');
            Route::get('show-data', 'ProblemController@showData');
            Route::post('update-data', 'ProblemController@updateData');
            Route::delete('destroy-data', 'ProblemController@destroyData');
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

        Route::prefix('district')->group(function () {
            Route::get('/', 'DistrictController@index');
            Route::get('datatable', 'DistrictController@datatable');
            Route::post('create-data', 'DistrictController@createData');
            Route::get('show-data', 'DistrictController@showData');
            Route::post('update-data', 'DistrictController@updateData');
            Route::delete('destroy-data', 'DistrictController@destroyData');
        });

        Route::prefix('village')->group(function () {
            Route::get('/', 'VillageController@index');
            Route::get('datatable', 'VillageController@datatable');
            Route::post('create-data', 'VillageController@createData');
            Route::get('show-data', 'VillageController@showData');
            Route::post('update-data', 'VillageController@updateData');
            Route::delete('destroy-data', 'VillageController@destroyData');
        });
    });

    Route::prefix('library')->namespace('Library')->group(function () {
        Route::prefix('data')->group(function () {
            Route::get('/', 'DataController@index');
            Route::get('datatable', 'DataController@datatable');
            Route::post('create-data', 'DataController@createData');
            Route::get('show-data', 'DataController@showData');
            Route::post('update-data', 'DataController@updateData');
            Route::delete('destroy-data', 'DataController@destroyData');
        });

        Route::prefix('location')->group(function () {
            Route::get('/', 'LocationController@index');
            Route::get('datatable', 'LocationController@datatable');
            Route::post('create-data', 'LocationController@createData');
            Route::get('show-data', 'LocationController@showData');
            Route::post('update-data', 'LocationController@updateData');
            Route::delete('destroy-data', 'LocationController@destroyData');
        });
    });

    Route::prefix('manager')->namespace('Manager')->group(function () {
        Route::prefix('create-data')->group(function () {
            Route::get('/', 'CreateDataController@index');
            Route::post('submitted', 'CreateDataController@submitted');
        });

        Route::prefix('review')->group(function () {
            Route::get('/', 'ReviewController@index');
            Route::get('datatable', 'ReviewController@datatable');
            Route::get('show-data', 'ReviewController@showData');
            Route::post('update-data', 'ReviewController@updateData');
            Route::delete('destroy-data', 'ReviewController@destroyData');
        });

        Route::prefix('manage')->group(function () {
            Route::get('/', 'ManageController@index');
            Route::get('datatable', 'ManageController@datatable');
            Route::get('show-data', 'ManageController@showData');
            Route::post('update-data', 'ManageController@updateData');
            Route::delete('destroy-data', 'ManageController@destroyData');
        });
    });

    Route::prefix('collection')->namespace('Collection')->group(function () {
        Route::prefix('create-single')->group(function () {
            Route::get('/', 'CreateSingleController@index');
            Route::get('check-isbn-code', 'CreateSingleController@checkISBNCode');
            Route::post('submitted', 'CreateSingleController@submitted');
        });

        Route::prefix('create-more')->group(function () {
            Route::get('/', 'CreateMoreController@index');
            Route::post('submitted', 'CreateMoreController@submitted');
        });

        Route::prefix('reject')->group(function () {
            Route::get('/', 'RejectController@index');
            Route::get('datatable', 'RejectController@datatable');
        });

        Route::prefix('problem')->group(function () {
            Route::get('/', 'ProblemController@index');
            Route::get('datatable', 'ProblemController@datatable');
        });

        Route::prefix('review')->group(function () {
            Route::get('/', 'ReviewController@index');
            Route::get('datatable', 'ReviewController@datatable');
            Route::match(['get', 'post'], 'detail/{id}', 'ReviewController@detail');
        });

        Route::prefix('digital-work')->group(function () {
            Route::get('/', 'DigitalWorkController@index');
            Route::get('datatable', 'DigitalWorkController@datatable');
            Route::get('detail/{id}', 'DigitalWorkController@detail');
        });

        Route::prefix('printed-work')->group(function () {
            Route::get('/', 'PrintedWorkController@index');
            Route::get('datatable', 'PrintedWorkController@datatable');
            Route::get('detail/{id}', 'PrintedWorkController@detail');
        });

        Route::prefix('analog-work')->group(function () {
            Route::get('/', 'AnalogWorkController@index');
            Route::get('datatable', 'AnalogWorkController@datatable');
            Route::get('detail/{id}', 'AnalogWorkController@detail');
        });

        Route::prefix('label')->group(function () {
            Route::get('/', 'LabelController@index');
            Route::get('datatable', 'LabelController@datatable');
            Route::get('print/{type}', 'LabelController@print');
        });
    });

    Route::prefix('bill-isbn')->group(function () {
        Route::get('/', 'BillISBNController@index');
        Route::get('datatable', 'BillISBNController@datatable');
    });

    Route::prefix('request-file')->group(function () {
        Route::get('/', 'RequestFileController@index');
        Route::get('datatable', 'RequestFileController@datatable');
        Route::post('set-status', 'RequestFileController@setStatus');
    });

    Route::prefix('template-email')->namespace('TemplateEmail')->group(function () {
        Route::prefix('header')->group(function () {
            Route::match(['get', 'post'], '/', 'HeaderController@index');
        });

        Route::prefix('footer')->group(function () {
            Route::match(['get', 'post'], '/', 'FooterController@index');
        });

        Route::prefix('receipt')->group(function () {
            Route::match(['get', 'post'], '/', 'ReceiptController@index');
        });

        Route::prefix('activation')->group(function () {
            Route::match(['get', 'post'], '/', 'ActivationController@index');
        });

        Route::prefix('manager-reject')->group(function () {
            Route::match(['get', 'post'], '/', 'PublisherRejectController@index');
        });

        Route::prefix('manager-submission')->group(function () {
            Route::match(['get', 'post'], '/', 'PublisherSubmissionController@index');
        });

        Route::prefix('manager-accept')->group(function () {
            Route::match(['get', 'post'], '/', 'PublisherAcceptController@index');
        });

        Route::prefix('collection-problem')->group(function () {
            Route::match(['get', 'post'], '/', 'CollectionProblemController@index');
        });

        Route::prefix('collection-submitted')->group(function () {
            Route::match(['get', 'post'], '/', 'CollectionSubmittedController@index');
        });

        Route::prefix('collection-accept')->group(function () {
            Route::match(['get', 'post'], '/', 'CollectionAcceptController@index');
        });
    });

    Route::prefix('report')->namespace('Report')->group(function () {
        Route::prefix('periodic')->group(function () {
            Route::get('/', 'PeriodicController@index');
            Route::get('load-data', 'PeriodicController@loadData');
        });

        Route::prefix('manager')->group(function () {
            Route::get('/', 'ManagerController@index');
            Route::get('datatable', 'ManagerController@datatable');
        });

        Route::prefix('collection')->group(function () {
            Route::get('/', 'CollectionController@index');
            Route::get('datatable', 'CollectionController@datatable');
            Route::get('detail/{id}', 'CollectionController@detail');
        });

        Route::prefix('performance-user')->group(function () {
            Route::get('/', 'PerformanceUserController@index');
            Route::get('datatable', 'PerformanceUserController@datatable');
        });
    });
});

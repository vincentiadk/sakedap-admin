<?php

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
    Route::get('notification', 'NotificationController@index');

    Route::prefix('auth')->group(function () {
        Route::match(['get', 'post'], 'change-password', 'AuthController@changePassword');
        Route::get('logout', 'AuthController@logout');
    });

    Route::prefix('select2-serverside')->group(function () {
        Route::get('branch', 'Select2ServersideController@branch');
        Route::get('executor', 'Select2ServersideController@executor');
        Route::get('location', 'Select2ServersideController@location');
        Route::get('collection-parent', 'Select2ServersideController@collectionParent');
        Route::get('problem', 'Select2ServersideController@problem');
        Route::get('catalog-id', 'Select2ServersideController@catalogId');
        Route::get('catalog', 'Select2ServersideController@catalog');
    });

    Route::get('home', function () {
        return view('layouts.index', [
            'data' => [
                'content' => 'home'
            ]
        ]);
    });

    Route::prefix('dashboard')->group(function () {
        Route::get('/', 'DashboardController@index');
        Route::get('data-province', 'DashboardController@dataProvince');
        Route::get('data-activity', 'DashboardController@dataActivity');
        Route::get('data-digital-work', 'DashboardController@dataDigitalWork');
        Route::get('data-analog-work', 'DashboardController@dataAnalogWork');
        Route::get('data-printed-work', 'DashboardController@dataPrintedWork');
        Route::get('data-collection', 'DashboardController@dataCollection');
        Route::get('data-type', 'DashboardController@dataType');
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

        Route::prefix('depo')->group(function () {
            Route::get('/', 'DepoController@index');
            Route::get('datatable', 'DepoController@datatable');
            Route::post('create-data', 'DepoController@createData');
            Route::get('show-data', 'DepoController@showData');
            Route::post('update-data', 'DepoController@updateData');
            Route::delete('destroy-data', 'DepoController@destroyData');
        });
    });

    Route::prefix('executor')->namespace('Executor')->group(function () {
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

        Route::prefix('warning')->group(function () {
            Route::get('/', 'WarningController@index');
            Route::get('datatable', 'WarningController@datatable');
            Route::post('create-data', 'WarningController@createData');
            Route::get('show-data', 'WarningController@showData');
            Route::post('update-data', 'WarningController@updateData');
            Route::delete('destroy-data', 'WarningController@destroyData');
        });
    });

    Route::prefix('bill-isbn')->group(function () {
        Route::get('/', 'BillISBNController@index');
        Route::get('datatable', 'BillISBNController@datatable');
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

    Route::prefix('promotion')->group(function () {
        Route::get('/', 'PromotionController@index');
        Route::get('datatable', 'PromotionController@datatable');
        Route::post('create-data', 'PromotionController@createData');
        Route::get('show-data', 'PromotionController@showData');
        Route::post('update-data', 'PromotionController@updateData');
        Route::delete('destroy-data', 'PromotionController@destroyData');
    });

    Route::prefix('delivery')->namespace('Delivery')->group(function () {
        Route::prefix('list')->group(function () {
            Route::get('/', 'ListController@index');
            Route::get('datatable', 'ListController@datatable');
            Route::match(['get', 'post'], 'verification/{id}', 'ListController@verification');
            Route::get('print/{id}', 'ListController@print');
        });

        Route::prefix('sent')->group(function () {
            Route::get('/', 'SentController@index');
            Route::get('datatable', 'SentController@datatable');
            Route::get('detail', 'SentController@detail');
        });

        Route::prefix('accepted')->group(function () {
            Route::get('/', 'AcceptedController@index');
            Route::get('datatable', 'AcceptedController@datatable');
            Route::get('detail', 'AcceptedController@detail');
        });

        Route::prefix('receipt')->group(function () {
            Route::get('/', 'ReceiptController@index');
            Route::get('search-isbn', 'ReceiptController@searchISBN');
            Route::get('select-catalog', 'ReceiptController@selectCatalog');
            Route::post('submitted', 'ReceiptController@submitted');
        });

        Route::prefix('reject')->group(function () {
            Route::get('/', 'RejectController@index');
            Route::get('datatable', 'RejectController@datatable');
            Route::post('grant', 'RejectController@grant');
            Route::post('retur', 'RejectController@retur');
        });
    });

    Route::prefix('supervision')->group(function () {
        Route::get('{segment}', 'SupervisionController@index');
    });

    Route::prefix('request-file')->group(function () {
        Route::get('/', 'RequestFileController@index');
        Route::get('datatable', 'RequestFileController@datatable');
        Route::post('set-status', 'RequestFileController@setStatus');
    });

    Route::prefix('template-email')->group(function () {
        Route::get('/', 'TemplateEmailController@index');
        Route::get('datatable', 'TemplateEmailController@datatable');
        Route::get('show-data', 'TemplateEmailController@showData');
        Route::post('update-data', 'TemplateEmailController@updateData');
    });

    Route::prefix('report')->namespace('Report')->group(function () {
        Route::prefix('periodic')->group(function () {
            Route::get('/', 'PeriodicController@index');
            Route::get('load-data', 'PeriodicController@loadData');
        });

        Route::prefix('executor')->group(function () {
            Route::get('/', 'ExecutorController@index');
            Route::get('datatable', 'ExecutorController@datatable');
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

        Route::prefix('log')->group(function () {
            Route::get('/', 'LogController@index');
            Route::get('datatable', 'LogController@datatable');
        });

        Route::prefix('download')->group(function () {
            Route::get('/', 'DownloadController@index');
        });
    });

    Route::prefix('setting')->namespace('Setting')->group(function () {
        Route::prefix('leader')->group(function () {
            Route::get('/', 'LeaderController@index');
            Route::get('datatable', 'LeaderController@datatable');
            Route::post('create-data', 'LeaderController@createData');
            Route::get('show-data', 'LeaderController@showData');
            Route::post('update-data', 'LeaderController@updateData');
            Route::delete('destroy-data', 'LeaderController@destroyData');
        });

        Route::prefix('banner')->group(function () {
            Route::get('/', 'BannerController@index');
            Route::get('datatable', 'BannerController@datatable');
            Route::post('create-data', 'BannerController@createData');
            Route::get('show-data', 'BannerController@showData');
            Route::post('update-data', 'BannerController@updateData');
            Route::delete('destroy-data', 'BannerController@destroyData');
        });

        Route::prefix('faq')->group(function () {
            Route::get('/', 'FaqController@index');
            Route::get('datatable', 'FaqController@datatable');
            Route::post('create-data', 'FaqController@createData');
            Route::get('show-data', 'FaqController@showData');
            Route::post('update-data', 'FaqController@updateData');
            Route::delete('destroy-data', 'FaqController@destroyData');
        });

        Route::prefix('terms-conditions')->group(function () {
            Route::match(['get', 'post'], '/', 'TermsConditionsController@index');
        });

        Route::prefix('about-us')->group(function () {
            Route::match(['get', 'post'], '/', 'AboutUsController@index');
        });

        Route::prefix('header-email')->group(function () {
            Route::match(['get', 'post'], '/', 'HeaderEmailController@index');
        });

        Route::prefix('footer-email')->group(function () {
            Route::match(['get', 'post'], '/', 'FooterEmailController@index');
        });
    });
});

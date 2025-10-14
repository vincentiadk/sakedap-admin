<?php

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/', 'AuthController@login');
Route::match(['get', 'post'], 'reset-password-request', 'AuthController@resetPasswordRequest');
Route::match(['get', 'post'], 'reset-password-action', 'AuthController@resetPasswordAction');

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
    Route::get('from-storage', 'DownloadController@fromStorage');
    Route::get('request-file', 'DownloadController@requestFile');
});

Route::middleware('authentication')->group(function () {
    Route::get('log-awb', 'LogAWBController@index');

    Route::prefix('log-activity')->group(function () {
        Route::get('/', 'LogActivityController@index');
        Route::get('datatable', 'LogActivityController@datatable');
    });

    Route::get('notification', 'NotificationController@index');

    Route::prefix('auth')->group(function () {
        Route::match(['get', 'post'], 'change-password', 'AuthController@changePassword');
        Route::match(['get', 'post'], 'profile', 'AuthController@profile');
        Route::get('logout', 'AuthController@logout');
    });

    Route::prefix('datatable-serverside')->group(function () {
        Route::get('catalog', 'DataTableServersideController@catalog');
        Route::get('catalog-parent', 'DataTableServersideController@catalogParent');
        Route::get('catalog-history', 'DataTableServersideController@catalogHistory');
    });

    Route::prefix('select2-serverside')->group(function () {
        Route::get('branch', 'Select2ServersideController@branch');
        Route::get('executor', 'Select2ServersideController@executor');
        Route::get('location', 'Select2ServersideController@location');
        Route::get('collection-parent', 'Select2ServersideController@collectionParent');
        Route::get('problem', 'Select2ServersideController@problem');
        Route::get('catalog', 'Select2ServersideController@catalog');
        Route::get('currency', 'Select2ServersideController@currency');
        Route::get('promotion', 'Select2ServersideController@promotion');
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

    Route::prefix('physical-delivery')->namespace('PhysicalDelivery')->group(function () {
        Route::prefix('delivery-verification')->group(function () {
            Route::get('/', 'DeliveryVerificationController@index');
            Route::get('datatable', 'DeliveryVerificationController@datatable');
            Route::match(['get', 'post'], 'detail/{id}', 'DeliveryVerificationController@detail');
        });

        Route::prefix('in-delivery')->group(function () {
            Route::get('/', 'InDeliveryController@index');
            Route::get('datatable', 'InDeliveryController@datatable');
            Route::match(['get', 'post'], 'detail/{id}', 'InDeliveryController@detail');
        });

        Route::prefix('accept')->group(function () {
            Route::get('/', 'AcceptController@index');
            Route::get('datatable', 'AcceptController@datatable');
            Route::match(['get', 'post'], 'detail/{id}', 'AcceptController@detail');
            Route::get('print/{id}', 'AcceptController@print');
            Route::post('send-email', 'AcceptController@sendEmail');
        });

        Route::prefix('create-receipt')->group(function () {
            Route::get('/', 'CreateReceiptController@index');
            Route::get('search-isbn', 'CreateReceiptController@searchISBN');
            Route::get('select-catalog', 'CreateReceiptController@selectCatalog');
            Route::post('submitted', 'CreateReceiptController@submitted');
        });
    });

    Route::prefix('national-management')->namespace('NationalManagement')->group(function () {
        Route::prefix('deposit-collection-list')->group(function () {
            Route::get('/', 'DepositCollectionListController@index');
        });

        Route::prefix('catalog-list')->group(function () {
            Route::get('/', 'CatalogListController@index');
        });

        Route::prefix('delivery-to-processing')->group(function () {
            Route::get('/', 'DeliveryToProcessingController@index');
        });

        Route::prefix('delivery-to-processing-list')->group(function () {
            Route::get('/', 'DeliveryToProcessingListController@index');
        });

        Route::prefix('alignment-storage')->group(function () {
            Route::get('/', 'AlignmentStorageController@index');
        });

        Route::prefix('cardex-list')->group(function () {
            Route::get('/', 'CardexListController@index');
        });

        Route::prefix('volume-by-title')->group(function () {
            Route::get('/', 'VolumeByTitleController@index');
        });

        Route::prefix('collection-volume')->group(function () {
            Route::get('/', 'CollectionVolumeController@index');
        });

        Route::prefix('create-volume')->group(function () {
            Route::get('/', 'CreateVolumeController@index');
        });

        Route::prefix('import-serial-collection')->group(function () {
            Route::get('/', 'ImportSerialCollectionController@index');
        });
    });

    Route::prefix('physical-collection')->namespace('PhysicalCollection')->group(function () {
        Route::prefix('collection-on-delivery')->group(function () {
            Route::get('/', 'CollectionOnDeliveryController@index');
            Route::get('datatable', 'CollectionOnDeliveryController@datatable');
            Route::get('detail', 'CollectionOnDeliveryController@detail');
        });

        Route::prefix('collection-accept')->group(function () {
            Route::get('/', 'CollectionAcceptController@index');
            Route::get('datatable', 'CollectionAcceptController@datatable');
            Route::get('detail', 'CollectionAcceptController@detail');
        });

        Route::prefix('collection-reject')->group(function () {
            Route::get('/', 'CollectionRejectController@index');
            Route::get('datatable', 'CollectionRejectController@datatable');
            Route::post('grant', 'CollectionRejectController@grant');
            Route::post('retur', 'CollectionRejectController@retur');
        });

        Route::prefix('collection-grant')->group(function () {
            Route::get('/', 'CollectionGrantController@index');
            Route::get('datatable', 'CollectionGrantController@datatable');
            Route::post('create-group', 'CollectionGrantController@createGroup');
            Route::post('out-group', 'CollectionGrantController@outGroup');
        });

        Route::prefix('collection-retur')->group(function () {
            Route::get('/', 'CollectionReturController@index');
            Route::get('datatable', 'CollectionReturController@datatable');
            Route::post('grant', 'CollectionReturController@grant');
            Route::post('taken', 'CollectionReturController@taken');
        });

        Route::prefix('verification-collection-received')->group(function () {
            Route::get('/', 'VerificationCollectionReceivedController@index');
        });

        Route::prefix('retrospective-collection-registration')->group(function () {
            Route::get('/', 'RetrospectiveCollectionRegistrationController@index');
        });

        Route::prefix('label')->group(function () {
            Route::get('/', 'LabelController@index');
            Route::get('datatable', 'LabelController@datatable');
            Route::get('print/{type}', 'LabelController@print');
        });
    });

    Route::prefix('digital-storage-handover')->namespace('DigitalStorageHandover')->group(function () {
        Route::prefix('review')->group(function () {
            Route::get('/', 'ReviewController@index');
            Route::get('datatable', 'ReviewController@datatable');
            Route::match(['get', 'post'], 'detail/{id}', 'ReviewController@detail');
        });

        Route::prefix('problem')->group(function () {
            Route::get('/', 'ProblemController@index');
            Route::get('datatable', 'ProblemController@datatable');
        });

        Route::prefix('accept')->group(function () {
            Route::get('/', 'AcceptController@index');
            Route::get('datatable', 'AcceptController@datatable');
            Route::get('detail/{id}', 'AcceptController@detail');
        });

        Route::prefix('reject')->group(function () {
            Route::get('/', 'RejectController@index');
            Route::get('datatable', 'RejectController@datatable');
        });

        Route::prefix('single-upload')->group(function () {
            Route::get('/', 'SingleUploadController@index');
            Route::get('check-isbn-code', 'SingleUploadController@checkISBNCode');
            Route::get('catalog-parent', 'SingleUploadController@catalogParent');
            Route::post('submitted', 'SingleUploadController@submitted');
        });

        Route::prefix('bulk-upload')->group(function () {
            Route::get('/', 'BulkUploadController@index');
            Route::get('datatable-bulk', 'BulkUploadController@datatableBulk');
            Route::get('detail-bulk', 'BulkUploadController@detailBulk');
            Route::post('submitted', 'BulkUploadController@submitted');
        });
    });

    Route::prefix('bill-isbn')->group(function () {
        Route::get('/', 'BillISBNController@index');
        Route::get('datatable', 'BillISBNController@datatable');
    });

    Route::prefix('coaching-supervision')->namespace('CoachingSupervision')->group(function () {
        Route::prefix('create-executor')->group(function () {
            Route::get('/', 'CreateExecutorController@index');
            Route::post('submitted', 'CreateExecutorController@submitted');
        });

        Route::prefix('executor-list')->group(function () {
            Route::get('/', 'ExecutorListController@index');
            Route::get('datatable', 'ExecutorListController@datatable');
            Route::get('show-data', 'ExecutorListController@showData');
            Route::post('update-data', 'ExecutorListController@updateData');
            Route::post('send-email-reset-password', 'ExecutorListController@sendEmailResetPassword');
            Route::delete('destroy-data', 'ExecutorListController@destroyData');
        });

        Route::prefix('problem')->group(function () {
            Route::get('/', 'ProblemController@index');
            Route::get('datatable', 'ProblemController@datatable');
        });

        Route::prefix('review')->group(function () {
            Route::get('/', 'ReviewController@index');
            Route::get('datatable', 'ReviewController@datatable');
            Route::get('show-data', 'ReviewController@showData');
            Route::post('update-data', 'ReviewController@updateData');
            Route::delete('destroy-data', 'ReviewController@destroyData');
        });

        Route::prefix('compliance')->group(function () {
            Route::get('/', 'ComplianceController@index');
        });

        Route::prefix('coaching-schedule')->group(function () {
            Route::get('/', 'CoachingScheduleController@index');
        });

        Route::prefix('monitoring')->group(function () {
            Route::get('/', 'MonitoringController@index');
        });

        Route::prefix('warning')->group(function () {
            Route::get('/', 'WarningController@index');
            Route::get('datatable', 'WarningController@datatable');
            Route::post('create-data', 'WarningController@createData');
            Route::get('show-data', 'WarningController@showData');
            Route::post('update-data', 'WarningController@updateData');
            Route::post('lockable', 'WarningController@lockable');
            Route::delete('destroy-data', 'WarningController@destroyData');
        });
    });

    Route::prefix('report')->namespace('Report')->group(function () {
        Route::prefix('delivery')->group(function () {
            Route::get('/', 'DeliveryController@index');
            Route::get('datatable', 'DeliveryController@datatable');
        });

        Route::prefix('promotion')->group(function () {
            Route::get('/', 'PromotionController@index');
            Route::get('datatable', 'PromotionController@datatable');
        });

        Route::prefix('physical-reception')->group(function () {
            Route::get('/', 'PhysicalReceptionController@index');
            Route::get('datatable', 'PhysicalReceptionController@datatable');
        });

        Route::prefix('physical-recording')->group(function () {
            Route::get('/', 'PhysicalRecordingController@index');
        });

        Route::prefix('physical-alignment')->group(function () {
            Route::get('/', 'PhysicalAlignmentController@index');
            Route::get('datatable', 'PhysicalAlignmentController@datatable');
        });

        Route::prefix('digital-empowerment')->group(function () {
            Route::get('/', 'DigitalEmpowermentController@index');
            Route::get('datatable', 'DigitalEmpowermentController@datatable');
        });

        Route::prefix('physical-empowerment')->group(function () {
            Route::get('/', 'PhysicalEmpowermentController@index');
            Route::get('datatable', 'PhysicalEmpowermentController@datatable');
        });

        Route::prefix('manage')->group(function () {
            Route::get('/', 'ManageController@index');
            Route::get('datatable', 'ManageController@datatable');
            Route::get('detail/{id}', 'ManageController@detail');
        });

        Route::prefix('digital-manage')->group(function () {
            Route::get('/', 'DigitalManageController@index');
            Route::get('datatable', 'DigitalManageController@datatable');
        });

        Route::prefix('service')->group(function () {
            Route::get('/', 'ServiceController@index');
            Route::get('load-data', 'ServiceController@loadData');
        });

        Route::prefix('physical-recording')->group(function () {
            Route::get('/', 'PhysicalRecordingController@index');
        });

        Route::prefix('download')->group(function () {
            Route::get('/', 'DownloadController@index');
        });
    });

    Route::prefix('administration-system')->namespace('AdministrationSystem')->group(function () {
        Route::prefix('setting-system')->group(function () {
            Route::get('/', 'SettingSystemController@index');
            Route::post('submitted', 'SettingSystemController@submitted');
            Route::post('test-send-email', 'SettingSystemController@testSendEmail');
        });

        Route::prefix('promotion')->group(function () {
            Route::get('/', 'PromotionController@index');
            Route::get('datatable', 'PromotionController@datatable');
            Route::post('create-data', 'PromotionController@createData');
            Route::get('show-data', 'PromotionController@showData');
            Route::post('update-data', 'PromotionController@updateData');
            Route::delete('destroy-data', 'PromotionController@destroyData');
        });

        Route::prefix('template-email')->group(function () {
            Route::get('/', 'TemplateEmailController@index');
            Route::get('datatable', 'TemplateEmailController@datatable');
            Route::get('show-data', 'TemplateEmailController@showData');
            Route::post('update-data', 'TemplateEmailController@updateData');
        });

        Route::prefix('header-email')->group(function () {
            Route::match(['get', 'post'], '/', 'HeaderEmailController@index');
        });

        Route::prefix('footer-email')->group(function () {
            Route::match(['get', 'post'], '/', 'FooterEmailController@index');
        });

        Route::prefix('user')->group(function () {
            Route::get('/', 'UserController@index');
        });

        Route::prefix('news')->group(function () {
            Route::get('/', 'NewsController@index');
            Route::get('datatable', 'NewsController@datatable');
            Route::post('create-data', 'NewsController@createData');
            Route::get('show-data', 'NewsController@showData');
            Route::post('update-data', 'NewsController@updateData');
            Route::delete('destroy-data', 'NewsController@destroyData');
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

        Route::prefix('media-type')->group(function () {
            Route::get('/', 'MediaTypeController@index');
            Route::get('datatable', 'MediaTypeController@datatable');
            Route::post('create-data', 'MediaTypeController@createData');
            Route::get('show-data', 'MediaTypeController@showData');
            Route::post('update-data', 'MediaTypeController@updateData');
            Route::delete('destroy-data', 'MediaTypeController@destroyData');
        });

        Route::prefix('news-category')->group(function () {
            Route::get('/', 'NewsCategoryController@index');
            Route::get('datatable', 'NewsCategoryController@datatable');
            Route::post('create-data', 'NewsCategoryController@createData');
            Route::get('show-data', 'NewsCategoryController@showData');
            Route::post('update-data', 'NewsCategoryController@updateData');
            Route::delete('destroy-data', 'NewsCategoryController@destroyData');
        });

        Route::prefix('collection-category')->group(function () {
            Route::get('/', 'CollectionCategoryController@index');
            Route::get('datatable', 'CollectionCategoryController@datatable');
            Route::post('create-data', 'CollectionCategoryController@createData');
            Route::get('show-data', 'CollectionCategoryController@showData');
            Route::post('update-data', 'CollectionCategoryController@updateData');
            Route::delete('destroy-data', 'CollectionCategoryController@destroyData');
        });

        Route::prefix('problem')->group(function () {
            Route::get('/', 'ProblemController@index');
            Route::get('datatable', 'ProblemController@datatable');
            Route::post('create-data', 'ProblemController@createData');
            Route::get('show-data', 'ProblemController@showData');
            Route::post('update-data', 'ProblemController@updateData');
            Route::delete('destroy-data', 'ProblemController@destroyData');
        });

        Route::prefix('library')->group(function () {
            Route::get('/', 'LibraryController@index');
            Route::get('datatable', 'LibraryController@datatable');
            Route::post('create-data', 'LibraryController@createData');
            Route::get('show-data', 'LibraryController@showData');
            Route::post('update-data', 'LibraryController@updateData');
            Route::delete('destroy-data', 'LibraryController@destroyData');
        });

        Route::prefix('depo')->group(function () {
            Route::get('/', 'DepoController@index');
            Route::get('datatable', 'DepoController@datatable');
            Route::post('create-data', 'DepoController@createData');
            Route::get('show-data', 'DepoController@showData');
            Route::post('update-data', 'DepoController@updateData');
            Route::delete('destroy-data', 'DepoController@destroyData');
        });

        Route::prefix('leader')->group(function () {
            Route::get('/', 'LeaderController@index');
            Route::get('datatable', 'LeaderController@datatable');
            Route::post('create-data', 'LeaderController@createData');
            Route::get('show-data', 'LeaderController@showData');
            Route::post('update-data', 'LeaderController@updateData');
            Route::delete('destroy-data', 'LeaderController@destroyData');
        });

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
});

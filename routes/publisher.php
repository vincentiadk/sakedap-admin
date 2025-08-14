<?php

use Illuminate\Support\Facades\Route;
//////////////////// PUBLISHER ////////////////////

// Login
Route::match(['get', 'post'], 'reset-password', 'ResetPasswordController@resetPassword');
Route::match(['get', 'post'], 'reset-password/confirm/{token}', 'ResetPasswordController@confirmResetPassword');

Route::any('/tus/{any?}', 'Publisher\TusServerController@index')->where('any', '.*');
Route::any('download/original/{id}', 'Publisher\DownloadFileOriginal@download');

Route::group(['prefix' => 'api/publisher', 'namespace' => 'Publisher\Api', 'middleware' => 'authenticate.api'], function () {
    Route::post('collection', 'CollectionController@create');
    Route::get('collection/list', 'CollectionController@get');
    Route::get('bill-isbn', 'BillIsbnController@datatableDetail');
});


Route::match(['get', 'post'], 'register', 'AuthController@register');
Route::get('register-success', 'AuthController@registerSuccess');
Route::post('publisher/select2_serverside/load_province', 'Publisher\HandleLoadSelect2Controller@loadProvince');
Route::post('publisher/select2_serverside/load_city', 'Publisher\HandleLoadSelect2Controller@loadCity');
Route::post('publisher/select2_serverside/load_district', 'Publisher\HandleLoadSelect2Controller@loadDistrict');
Route::post('publisher/select2_serverside/load_village', 'Publisher\HandleLoadSelect2Controller@loadVillage');
Route::post('publisher/select2_serverside/load_collection', 'Publisher\HandleLoadSelect2Controller@loadCollection');

Route::group(['middleware' => 'protectLoginMiddleware'], function () {
    Route::group(['prefix' => 'publisher', 'namespace' => 'Publisher'], function () {
        Route::get('get_file/{id}', 'FileController@get_file');
        // Auth
        Route::prefix('auth')->group(function () {
            Route::match(['get', 'post'], 'profile', 'AuthController@profile');
            Route::match(['get', 'post'], 'change_password', 'AuthController@changePassword');
            Route::post('connect', 'AuthController@connect');
        });
        Route::prefix('dashboard')->group(function () {
            Route::get('/', 'DashboardController@index');
            Route::get('/collection_status/{id}', 'DashboardController@getCollectionStatus');
            Route::get('/activity/{id}', 'DashboardController@getActivity');
            Route::get('/collection_last_day/{id}', 'DashboardController@getCollectionLastDay');
            Route::get('/total_collection/{id}', 'DashboardController@getTotalCollection');
            Route::get('/file_type/{id}', 'DashboardController@getFileType');
            Route::get('/collection_type_status/{id}', 'DashboardController@getCollectionTypeStatus');
            Route::get('/collection_monitoring/{id}', 'DashboardController@getCollectionMonitoring');

        });
        Route::get('/dashboard', 'DashboardController@index');
        Route::get('/load_dashboard/{for}/{param?}', 'DashboardController@statistic');
        Route::get('/api/documentation/collection', 'Api\DocumentationController@index');

        // Book
        Route::prefix('book')->group(function () {
            Route::get('create_import', 'BookRequestController@createImport');
        });

        // Select2 ServerSide
        Route::prefix('select2_serverside')->group(function () {
            Route::post('load_publisher', 'HandleLoadSelect2Controller@loadPublisher');
            Route::post('load_publisher_bill', 'HandleLoadSelect2Controller@loadPublisherBill');
            Route::post('load_category/{type}', 'HandleLoadSelect2Controller@loadCategory');
            Route::post('load_subject', 'HandleLoadSelect2Controller@loadSubject');
            Route::post('load_contributor/{type}', 'HandleLoadSelect2Controller@loadContributor');
            Route::post('load_author', 'HandleLoadSelect2Controller@loadAuthor');
        });

        Route::prefix('collection')->group(function () {
            Route::match(['get', 'post'], 'create_manual', 'CollectionController@createManual');

            Route::get('history', 'CollectionMonitoringController@history');


            Route::prefix('isbn')->group(function () {
                Route::get('/', 'CollectionIsbnController@index');
                Route::post('/submit', 'CollectionIsbnController@submit');
                Route::post('/datatable', 'CollectionIsbnController@datatable');
                Route::post('/upload', 'CollectionIsbnController@upload')->name('isbn.upload');
                Route::get('/{id}', 'CollectionIsbnController@find');
                Route::post('/{id}/update', 'CollectionIsbnController@update');
                Route::post('/{id}/delete', 'CollectionIsbnController@delete');
            });

            Route::post('serial', 'CollectionSerialController@datatable');
            Route::post('serial/{id}', 'CollectionSerialController@find');

            Route::get('isbn-by-publisher', 'CollectionController@getIsbnByPublisher');
            Route::get('isbn-by-publisher-all', 'CollectionController@getIsbnByPublisherAll');
            Route::post('save_temporary', 'CollectionRequestController@saveTemporary');
            Route::get('stream_file_pdf', 'CollectionRequestController@streamFilePdf');
            Route::get('destroy/{id}', 'CollectionRequestController@destroy');
            Route::get('isbn-jilid/{kd_penerbit_dtl}', 'CollectionController@getIsbnJilid');

            // Monitoring
            Route::prefix('monitoring')->group(function () {
                Route::get('/', 'CollectionMonitoringController@index');
                Route::get('datatable/', 'CollectionMonitoringController@datatable');
                Route::match(['get', 'post'], 'detail/{id}', 'CollectionMonitoringController@review');
            });

            // Problem
            Route::prefix('problem')->group(function () {
                Route::get('/', 'CollectionProblemController@index');
                Route::get('datatable/', 'CollectionProblemController@datatable');
            });

            // Problem
            Route::prefix('problem_kckra')->group(function () {
                Route::get('/', 'CollectionProblemKckraController@index');
                Route::get('datatable/', 'CollectionProblemKckraController@datatable');
                Route::get('handling/{id}', 'CollectionProblemKckraController@updateCopyRejected');
            });

            // Monitoring KCKRA
            Route::prefix('monitoring_kckra')->group(function () {
                Route::get('/', 'CollectionMonitoringKckraController@index');
                Route::get('datatable/', 'CollectionMonitoringKckraController@datatable');
                Route::get('show/{id}', 'CollectionMonitoringKckraController@show');
            });


            // Accepted
            Route::prefix('accepted')->group(function () {
                Route::get('/', 'CollectionAcceptedController@index');
                Route::get('datatable/', 'CollectionAcceptedController@datatable');
                Route::match(['get', 'post'], 'detail/{id}', 'CollectionAcceptedController@review');
            });

            // Edition
            Route::prefix('edition')->group(function () {
                Route::post('create/{id}', 'CollectionEditionController@create');
                Route::post('destroy', 'CollectionEditionController@destroy');
            });

            Route::prefix('request')->group(function () {

                Route::get('/', 'CollectionRequestController@index');
                Route::get('/datatable', 'CollectionRequestController@datatable');
                Route::match(['get', 'post'], 'original/{collectionId}', 'CollectionRequestController@requestOriginal');
                Route::get('receipt/{collectionId}', 'CollectionRequestController@requestReceipt');

                Route::get('monitor', 'CollectionMonitoringRequest@index');
                Route::get('monitor/datatable', 'CollectionMonitoringRequest@datatable');
                Route::get('verification', 'CollectionRequestController@verificationRequest');
            });

            Route::match(['get', 'post'], 'update/{id}', 'CollectionUpdateController@update');
            Route::post('update/{id}/preview-access', 'CollectionUpdateController@updatePreviewAndAccess');

            //upload bulk
            Route::get('import', 'CollectionUploadBulkController@selectType');
            Route::get('import/jobs', 'CollectionUploadBulkController@datatable');
            Route::get('import/{typeId}', 'CollectionUploadBulkController@index');
            Route::post('import/{typeId}', 'CollectionUploadBulkController@upload');
            Route::post('import/download/isbn', 'CollectionUploadBulkController@downloadBillISBN');

            Route::get('serial/datatable', 'SerialBulkEditionController@datatable');

            // Delivery KCKR Analog
            Route::match(['get', 'post'], 'delivery_form', 'CollectionDeliveryFormController@formDelivery');
            Route::prefix('delivery')->group(function () {
                Route::get('/', 'CollectionDeliveryController@index');
                Route::get('datatable/', 'CollectionDeliveryController@datatable');
                Route::match(['get', 'post'], 'detail/{id}', 'CollectionDeliveryController@review');
                Route::post('edit/{id}', 'CollectionDeliveryFormController@editDelivery');
                Route::post('send/{id}', 'CollectionDeliveryFormController@sendDelivery');
                Route::get('download_receipt/{letter_no}', 'CollectionDeliveryController@downloadReceipt')->withoutMiddleware(['protectLoginMiddleware']);
                Route::get('download_shipping/{id}', 'CollectionDeliveryController@downloadShipping')->withoutMiddleware(['protectLoginMiddleware']);
                Route::get('destroy/{id}', 'CollectionDeliveryController@destroy');
            });

            Route::post('check_code_isbn', 'CollectionDeliveryFormController@checkCodeIsbn');
        });

        // Bill ISBN
        Route::prefix('bill_isbn')->group(function () {
            Route::get('/', 'BillIsbnController@index');
            Route::get('/total', 'BillIsbnController@totalBill');
            Route::get('/locked', 'BillIsbnController@locked');
            Route::get('/warning', 'BillIsbnController@warning');
            Route::get('datatable_summary', 'BillIsbnController@datatableSummary');
            Route::get('datatable_detail', 'BillIsbnController@datatableDetail');
        });

        // Report
        Route::prefix('report')->group(function () {
            // Collection
            Route::prefix('collection')->group(function () {
                Route::get('/', 'ReportController@collection');
                Route::get('datatable_summary', 'ReportController@collectionDatatableSummary');
                Route::get('datatable_detail', 'ReportController@collectionDatatableDetail');
            });

            // File Download
            Route::prefix('file_download')->group(function () {
                Route::get('/', 'ReportController@fileDownload');
                Route::get('datatable', 'ReportController@fileDownloadDatatable');
                Route::post('processing', 'ReportController@fileDownloadProcessing');
                Route::get('download/{id}', 'ReportController@fileDownloadRun');
                Route::get('jobs', 'ReportController@datatableJobs');
            });
        });

        Route::prefix('notification')->group(function () {
            Route::get('/', 'NotificationController@index');
            Route::get('/read/{id}', 'NotificationController@findOne');
            Route::get('/datatable', 'NotificationController@datatable');
        });
    });
});

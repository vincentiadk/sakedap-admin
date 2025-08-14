<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', 'login');

Route::group(['middleware' => ['strip_tags']], function () {
    // Login
    Route::match(['get', 'post'], 'login', 'AuthController@login')->middleware('log_form:login');

    // Register
    Route::match(['get', 'post'], 'register', 'AuthController@register')->middleware('log_form:register');

    // Reset Password
    Route::match(['get', 'post'], 'reset-password', 'ResetPasswordController@resetPassword');
    Route::match(['get', 'post'], 'reset-password/confirm/{token}', 'ResetPasswordController@confirmResetPassword')->middleware('log_form:forgot_password');
});

// Logout
Route::get('logout', 'AuthController@logout');

Route::group(['middleware' => 'protectLoginMiddleware'], function () {
    Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
        // Auth
        Route::prefix('auth')->group(function () {
            Route::match(['get', 'post'], 'profile', 'AuthController@profile');
            Route::match(['get', 'post'], 'change_password', 'AuthController@changePassword');
        });
        Route::get('get_file/{id}', 'FileController@get_file');

        // Select2 ServerSide
        Route::prefix('select2_serverside')->group(function () {
            Route::post('load_extension', 'HandleLoadSelect2Controller@loadExtension');
            Route::post('load_publisher', 'HandleLoadSelect2Controller@loadPublisher');
            Route::post('load_publisher_bill', 'HandleLoadSelect2Controller@loadPublisherBill');
            Route::post('load_publisher_isrc', 'HandleLoadSelect2Controller@loadPublisherISRC');
            Route::post('load_province', 'HandleLoadSelect2Controller@loadProvince');
            Route::post('load_city/{province_id?}', 'HandleLoadSelect2Controller@loadCity');
            Route::post('load_district/{city_id?}', 'HandleLoadSelect2Controller@loadDistrict');
            Route::post('load_village/{district_id?}', 'HandleLoadSelect2Controller@loadVillage');
            Route::post('load_subject', 'HandleLoadSelect2Controller@loadSubject');
            Route::post('load_user', 'HandleLoadSelect2Controller@loadUser');
            Route::post('load_library', 'HandleLoadSelect2Controller@loadLibrary');
            Route::post('load_lib_loc', 'HandleLoadSelect2Controller@loadLibraryLocation');
            Route::post('load_author', 'HandleLoadSelect2Controller@loadAuthor');
            Route::post('load_author_manage', 'HandleLoadSelect2Controller@loadAuthorManage');
            Route::post('load_edition/{collection_id?}', 'HandleLoadSelect2Controller@loadCollectionEdition');
        });

        // Dashboard
        Route::get('dashboard', 'DashboardController@index');
        Route::get('load_dashboard/{for}/{param?}', 'DashboardController@statistic');

        // ISRC
        Route::prefix('isrc')->middleware('protect.menu:admin/isrc')->group(function () {
            Route::get('/', 'IsrcController@index');
            Route::get('datatable', 'IsrcController@datatable');
            Route::get('show', 'IsrcController@show');
        });

        // Master Author
        Route::prefix('author')->middleware('protect.menu:admin/author')->group(function () {
            Route::get('/', 'AuthorController@index');
            Route::get('datatable', 'AuthorController@datatable');
            Route::get('show/{id}', 'AuthorController@show');
            Route::post('update/{id}', 'AuthorController@update');
            Route::get('destroy/{id}', 'AuthorController@destroy');
        });

        // Master Banner
        Route::prefix('banner')->middleware('protect.menu:admin/banner')->group(function () {
            Route::get('/', 'BannerController@index');
            Route::get('datatable', 'BannerController@datatable');
            Route::post('create', 'BannerController@create');
            Route::get('show/{id}', 'BannerController@show');
            Route::post('update/{id}', 'BannerController@update');
            Route::get('destroy/{id}', 'BannerController@destroy');
        });

        // Master Contributor
        Route::prefix('contributor')->middleware('protect.menu:admin/contributor')->group(function () {
            Route::get('/', 'ContributorController@index');
            Route::get('datatable', 'ContributorController@datatable');
            Route::post('create', 'ContributorController@create');
            Route::get('show/{id}', 'ContributorController@show');
            Route::post('update/{id}', 'ContributorController@update');
            Route::get('destroy/{id}', 'ContributorController@destroy');
        });

        // Master Category
        Route::prefix('category')->middleware('protect.menu:admin/category')->group(function () {
            Route::get('/', 'CategoryController@index');
            Route::get('datatable', 'CategoryController@datatable');
            Route::post('create', 'CategoryController@create');
            Route::get('show/{id}', 'CategoryController@show');
            Route::post('update/{id}', 'CategoryController@update');
            Route::get('destroy/{id}', 'CategoryController@destroy');
        });

        // Master Subject
        Route::prefix('subject')->middleware('protect.menu:admin/subject')->group(function () {
            Route::get('/', 'SubjectController@index');
            Route::get('datatable', 'SubjectController@datatable');
            Route::post('create', 'SubjectController@create');
            Route::get('show/{id}', 'SubjectController@show');
            Route::post('update/{id}', 'SubjectController@update');
            Route::get('destroy/{id}', 'SubjectController@destroy');
        });

        // Master Problem
        Route::prefix('problem')->middleware('protect.menu:admin/problem')->group(function () {
            Route::get('/', 'ProblemController@index');
            Route::get('datatable', 'ProblemController@datatable');
            Route::post('create', 'ProblemController@create');
            Route::get('show/{id}', 'ProblemController@show');
            Route::post('update/{id}', 'ProblemController@update');
            Route::get('destroy/{id}', 'ProblemController@destroy');
        });

        // Master Province
        Route::prefix('province')->middleware('protect.menu:admin/province')->group(function () {
            Route::get('/', 'ProvinceController@index');
            Route::get('datatable', 'ProvinceController@datatable');
            Route::post('create', 'ProvinceController@create');
            Route::get('show/{id}', 'ProvinceController@show');
            Route::post('update/{id}', 'ProvinceController@update');
            Route::get('destroy/{id}', 'ProvinceController@destroy');
        });

        // Master City
        Route::prefix('city')->middleware('protect.menu:admin/city')->group(function () {
            Route::get('/', 'CityController@index');
            Route::get('datatable', 'CityController@datatable');
            Route::post('create', 'CityController@create');
            Route::get('show/{id}', 'CityController@show');
            Route::post('update/{id}', 'CityController@update');
            Route::get('destroy/{id}', 'CityController@destroy');
        });

        // Master District
        Route::prefix('district')->middleware('protect.menu:admin/district')->group(function () {
            Route::get('/', 'DistrictController@index');
            Route::get('datatable', 'DistrictController@datatable');
            Route::post('create', 'DistrictController@create');
            Route::get('show/{id}', 'DistrictController@show');
            Route::post('update/{id}', 'DistrictController@update');
            Route::get('destroy/{id}', 'DistrictController@destroy');
        });

        // Master Village
        Route::prefix('village')->middleware('protect.menu:admin/village')->group(function () {
            Route::get('/', 'VillageController@index');
            Route::get('datatable', 'VillageController@datatable');
            Route::post('create', 'VillageController@create');
            Route::get('show/{id}', 'VillageController@show');
            Route::post('update/{id}', 'VillageController@update');
            Route::get('destroy/{id}', 'VillageController@destroy');
        });

        // Master Organization
        Route::prefix('organization')->middleware('protect.menu:admin/organization')->group(function () {
            Route::get('/', 'OrganizationController@index');
            Route::get('datatable', 'OrganizationController@datatable');
            Route::post('create', 'OrganizationController@create');
            Route::get('show/{id}', 'OrganizationController@show');
            Route::post('update/{id}', 'OrganizationController@update');
            Route::get('destroy/{id}', 'OrganizationController@destroy');
        });

        // Master Director
        Route::prefix('director')->middleware('protect.menu:admin/director')->group(function () {
            Route::get('/', 'DirectorController@index');
            Route::get('datatable', 'DirectorController@datatable');
            Route::post('create', 'DirectorController@create');
            Route::get('show/{id}', 'DirectorController@show');
            Route::post('update/{id}', 'DirectorController@update');
            Route::get('destroy/{id}', 'DirectorController@destroy');
        });

        // Master Library
        Route::prefix('library')->middleware('protect.menu:admin/library')->group(function () {
            Route::get('/', 'LibraryController@index');
            Route::get('datatable', 'LibraryController@datatable');
            Route::post('create', 'LibraryController@create');
            Route::get('show/{id}', 'LibraryController@show');
            Route::post('update/{id}', 'LibraryController@update');
            Route::get('destroy/{id}', 'LibraryController@destroy');
        });

        // Master Kunjungan
        Route::prefix('kunjungan')->middleware('protect.menu:admin/kunjungan')->group(function () {
            Route::get('/', 'KunjunganController@index');
            Route::get('datatable', 'KunjunganController@datatable');
            Route::post('create', 'KunjunganController@create');
            Route::get('show/{id}', 'KunjunganController@show');
            Route::post('update/{id}', 'KunjunganController@update');
            Route::get('destroy/{id}', 'KunjunganController@destroy');
        });


        // Master Kunjungan
        Route::prefix('publisher_group')->middleware('protect.menu:admin/publisher_group')->group(function () {

            Route::prefix('{group_id}/access')->group(function () {
                Route::get('/', 'PublisherAccessController@index');
                Route::get('datatable', 'PublisherAccessController@datatable');
                Route::post('create', 'PublisherAccessController@create');
                Route::get('show/{id}', 'PublisherAccessController@show');
                Route::post('update/{id}', 'PublisherAccessController@update');
                Route::get('destroy/{id}', 'PublisherAccessController@destroy');
            });
            Route::get('get-publisher/{id}', 'PublisherAccessController@getPublisher');
            Route::get('/', 'PublisherGroupController@index');
            Route::get('datatable', 'PublisherGroupController@datatable');
            Route::post('create', 'PublisherGroupController@create');
            Route::get('show/{id}', 'PublisherGroupController@show');
            Route::post('update/{id}', 'PublisherGroupController@update');
            Route::get('destroy/{id}', 'PublisherGroupController@destroy');
        });

        // Master Location
        Route::prefix('location')->middleware('protect.menu:admin/location')->group(function () {
            Route::get('/', 'LocationController@index');
            Route::get('datatable', 'LocationController@datatable');
            Route::get('show/{id}', 'LocationController@show');
            Route::post('update/{id}', 'LocationController@update');
            Route::get('destroy/{id}', 'LocationController@destroy');
        });

        // Collection
        Route::prefix('collection')->group(function () {
            Route::get('load_image_pdf', 'CollectionController@loadImagePdf');
            Route::get('stream_pdf/{id}', 'CollectionController@streamPdf');
            Route::get('reset_filed/{type}/{id}', 'CollectionController@resetFilter');

            // Bulk Upload
            Route::prefix('bulk_upload')->group(function () {
                Route::get('/', 'CollectionBulkUploadController@index');
                Route::get('download', 'CollectionBulkUploadController@download');
                Route::post('action_upload', 'CollectionBulkUploadController@actionUpload');
                Route::get('datatable_serial', 'CollectionBulkUploadController@datatableSerial');
                Route::prefix('progress')->group(function () {
                    Route::get('/', 'CollectionBulkUploadController@progress');
                    Route::get('datatable', 'CollectionBulkUploadController@datatableProgress');
                    Route::get('show', 'CollectionBulkUploadController@showProgress');
                });
            });

            // Request
            Route::match(['get', 'post'], 'create_manual/{type?}/{connect?}', 'CollectionRequestController@createManual');
            Route::post('save_temporary', 'CollectionRequestController@saveTemporary');
            Route::post('check_code_isbn', 'CollectionRequestController@checkCodeIsbn');
            Route::post('lockable/{id}', 'CollectionRequestController@lockable');
            Route::get('destroy/{id}', 'CollectionRequestController@destroy');

            // Request KCKRA
            Route::prefix('kckra')->group(function () {
                Route::get('testing', 'CollectionKcKraController@testing');
                Route::match(['get', 'post'], 'create_manual/{type?}/{connect?}', 'CollectionKcKraController@createManual');
                Route::post('datatable_serial_parent', 'CollectionKcKraController@datatableSerialParents');
                Route::post('save_temporary', 'CollectionKcKraController@saveTemporary');
                Route::post('check_code_isbn', 'CollectionKcKraController@checkCodeIsbn');
                Route::post('check_code_deposit', 'CollectionKckraShippingController@checkCodeDeposit');
                Route::get('get_publisher', 'CollectionKcKraController@getPublisher');
                Route::post('lockable/{id}', 'CollectionKcKraController@lockable');
                Route::get('destroy/{id}', 'CollectionKcKraController@destroy');
                Route::get('approval', 'CollectionKcKraController@approval');
                Route::get('approval_datatable', 'CollectionKcKraController@datatableApproval');
                Route::post('create_copies/{id}', 'CollectionManageKckraController@createCopies');
                Route::post('update_copies/{id}', 'CollectionManageKckraController@updateCopies');
                Route::post('datatable_copies/{id}/{type?}', 'CollectionManageKckraController@datatableCopies');
                Route::get('show_copies/{id}', 'CollectionManageKckraController@showCopies');
                Route::delete('karantina_copies/{id}', 'CollectionManageKckraController@karantinaCopies');
                Route::post('create_editions/{id}', 'CollectionManageKckraController@createEditions');
                Route::post('datatable_editions/{id}', 'CollectionManageKckraController@datatableEditions');
                Route::get('show_editions/{id}', 'CollectionManageKckraController@showEditions');
                Route::post('update_editions/{id}', 'CollectionManageKckraController@updateEditions');
                Route::delete('karantina_editions/{id}', 'CollectionManageKckraController@karantinaEditions');
                //manage kckr                Route::get('approval_datatable', 'CollectionKcKraController@datatableApproval');a
                Route::prefix('manage')->middleware('protect.menu:admin/collection/kckra/manage/all')->group(function () {
                    Route::get('all', 'CollectionManageKckraController@index');
                    Route::get('datatable', 'CollectionManageKckraController@datatable');
                    Route::match(['get', 'post'], 'update/{id}', 'CollectionManageKckraController@update');
                });

                Route::prefix('shipping')->middleware('protect.menu:admin/collection/kckra/shipping')->group(function () {
                    Route::get('/', 'CollectionKckraShippingController@index');
                    Route::post('datatable', 'CollectionKckraShippingController@datatableShipping');
                    Route::post('create', 'CollectionKckraShippingController@create');
                    Route::get('print', 'CollectionKckraShippingController@printPdf');
                    Route::delete('karantina/{id}', 'CollectionKckraShippingController@karantina');
                });

                Route::prefix('print')->middleware('protect.menu:admin/collection/kckra/print')->group(function () {
                    Route::get('/', 'CollectionPrintCodeController@index');
                    Route::get('datatable', 'CollectionPrintCodeController@datatable');
                    Route::get('barcode', 'CollectionPrintCodeController@printBarcode');
                    Route::get('qrcode', 'CollectionPrintCodeController@printQrcode');
                });

                Route::prefix('bulk_upload')->middleware('protect.menu:admin/collection/kckra/bulk_upload')->group(function () {
                    Route::get('/', 'CollectionKckraBulkUploadController@index');
                    Route::get('download', 'CollectionKckraBulkUploadController@download');
                    Route::post('action_upload', 'CollectionKckraBulkUploadController@actionUpload');
                    Route::get('datatable_serial/{deposit_head}', 'CollectionKckraBulkUploadController@datatableSerial');
                    Route::get('download_template/{type?}', 'CollectionKckraBulkUploadController@downloadTemplate');
                    Route::prefix('progress')->group(function () {
                        Route::get('/', 'CollectionKckraBulkUploadController@progress');
                        Route::get('datatable', 'CollectionKckraBulkUploadController@datatableProgress');
                        Route::get('show', 'CollectionKckraBulkUploadController@showProgress');
                    });
                });
                Route::prefix('problem')->middleware('protect.menu:admin/collection/kckra/problem/all')->group(function () {
                    Route::get('all', 'CollectionKckraProblemController@index');
                    Route::get('datatable', 'CollectionKckraProblemController@datatable');
                });
            });

            // Edition
            Route::prefix('edition')->group(function () {
                Route::post('create/{id}', 'CollectionEditionController@create');
                Route::post('destroy', 'CollectionEditionController@destroy');
            });

            // Problem
            Route::prefix('problem')->middleware('protect.menu:admin/collection/problem')->group(function () {
                Route::get('{type?}', 'CollectionProblemController@index');
                Route::get('datatable/{type}', 'CollectionProblemController@datatable');
            });

            // Monitoring
            Route::prefix('monitoring')->middleware('protect.menu:admin/collection/monitoring')->group(function () {
                Route::get('/', 'CollectionMonitoringController@index');
                Route::match(['get', 'post'], 'review/{id}', 'CollectionMonitoringController@review');
            });

            // Manage
            Route::prefix('manage')->middleware('protect.menu:admin/collection/manage')->group(function () {
                Route::get('{type?}', 'CollectionManageController@index');
                Route::get('datatable/{type}', 'CollectionManageController@datatable');
                Route::match(['get', 'post'], 'update/{id}', 'CollectionManageController@update');
            });

            // Monitoring
            Route::prefix('delivery')->group(function () {
                Route::get('/', 'CollectionDeliveryController@index');
                Route::get('create', 'CollectionDeliveryController@create');
                Route::get('datatable', 'CollectionDeliveryController@datatable');
                Route::match(['get', 'post'], 'review/{id}', 'CollectionDeliveryController@review');
                Route::match(['get', 'post'], 'accept/{id}', 'CollectionDeliveryController@accept');
                Route::get('download_receipt/{letter_no}', 'CollectionDeliveryController@downloadReceipt')->withoutMiddleware(['protectLoginMiddleware']);
                // Route::get('download_shipping/{id}', 'CollectionDeliveryController@downloadShipping')->withoutMiddleware(['protectLoginMiddleware']);
            });
        });

        // Publisher
        Route::prefix('publisher')->group(function () {
            // Request
            Route::match(['get', 'post'], 'create', 'PublisherRequestController@create');
            Route::get('stream_pdf/{id}/{type}', 'PublisherRequestController@streamPdf');

            // Monitoring
            Route::prefix('monitoring')->middleware('protect.menu:admin/publisher/monitoring')->group(function () {
                Route::get('/', 'PublisherMonitoringController@index');
                Route::get('datatable', 'PublisherMonitoringController@datatable');
                Route::get('show/{id}', 'PublisherMonitoringController@show');
                Route::post('review/{id}', 'PublisherMonitoringController@review');
                Route::get('destroy/{id}', 'PublisherMonitoringController@destroy');
            });

            // Manage
            Route::prefix('manage')->middleware('protect.menu:admin/publisher/manage')->group(function () {
                Route::get('/', 'PublisherManageController@index');
                Route::get('datatable', 'PublisherManageController@datatable');
                Route::get('show/{id}', 'PublisherManageController@show');
                Route::post('update/{id}', 'PublisherManageController@update');
                Route::get('destroy/{id}', 'PublisherManageController@destroy');
                Route::get('lock-unlock/{id}', 'PublisherManageController@lockUnlock');
                Route::get('sync-isbn/{id}', 'PublisherManageController@syncIsbn');
            });
        });

        // Bill ISBN
        Route::prefix('bill_isbn')->middleware('protect.menu:admin/bill_isbn')->group(function () {
            Route::get('/', 'BillIsbnController@index');
            Route::post('datatable_summary', 'BillIsbnController@datatableSummary');
            Route::post('datatable_detail', 'BillIsbnController@datatableDetail');
        });

        // Article
        Route::prefix('article')->middleware('protect.menu:admin/article')->group(function () {
            Route::get('/', 'NewsController@index');
            Route::get('datatable', 'NewsController@datatable');
            Route::post('create', 'NewsController@create');
            Route::get('show/{id}', 'NewsController@show');
            Route::post('update/{id}', 'NewsController@update');
            Route::get('destroy/{id}', 'NewsController@destroy');
        });

        // Report
        Route::prefix('report')->group(function () {
            // Distribution
            Route::prefix('distribution')->middleware('protect.menu:admin/report/distribution')->group(function () {
                Route::get('/', 'ReportController@distribution');
                Route::get('datatable', 'ReportController@distributionDatatable');
            });

            // Collection
            Route::prefix('collection')->middleware('protect.menu:admin/report/collection')->group(function () {
                Route::get('/', 'ReportController@collection');
                Route::get('datatable_summary', 'ReportController@collectionDatatableSummary');
                Route::get('datatable_detail', 'ReportController@collectionDatatableDetail');
                Route::get('download_receipt/{id}', 'ReportController@downloadReceipt');
                // Collection KCRA
                Route::prefix('kckra')->group(function () {
                    Route::get('/', 'ReportKckraController@collection');
                    Route::post('datatable_summary', 'ReportKckraController@collectionDatatableSummary');
                    Route::post('datatable_detail', 'ReportKckraController@collectionDatatableDetail');
                    Route::get('download_receipt/{id}', 'ReportKckraController@downloadReceipt');
                });
            });

            // Collection Delivery
            Route::prefix('collection_delivery')->middleware('protect.menu:admin/report/collection_delivery')->group(function () {
                Route::get('/', 'ReportController@collectionDelivery');
                Route::post('datatable', 'ReportController@collectionDeliveryDatatable');
            });

            // Kinerja User
            Route::prefix('performance_user')->middleware('protect.menu:admin/report/performance_user')->group(function () {
                Route::get('/', 'ReportController@performanceUser');
                Route::get('datatable', 'ReportController@performanceUserDatatable');
            });

            // Publisher
            Route::prefix('publisher')->middleware('protect.menu:admin/report/publisher')->group(function () {
                Route::get('/', 'ReportController@publisher');
                Route::post('datatable', 'ReportController@publisherDatatable');
            });

            // Publisher ISBN
            Route::prefix('publisher_isbn')->middleware('protect.menu:admin/report/publisher_isbn')->group(function () {
                Route::get('/', 'ReportController@publisherISBN');
                Route::post('datatable', 'ReportController@publisherISBNDatatable');
            });

            // Log Activity
            Route::prefix('log_activity')->middleware('protect.menu:admin/report/log_activity')->group(function () {
                Route::get('/', 'ReportController@logActivity');
                Route::post('datatable', 'ReportController@logActivityDatatable');
            });

            // File Download
            Route::prefix('file_download')->middleware('protect.menu:admin/report/file_download')->group(function () {
                Route::get('/', 'ReportController@fileDownload');
                Route::get('datatable', 'ReportController@fileDownloadDatatable');
                Route::match(['get', 'post'], 'processing', 'ReportController@fileDownloadProcessing');
                Route::get('show_description/{id}', 'ReportController@fileDownloadDescription');
                Route::get('download/{id}', 'ReportController@fileDownloadRun');

                //for collection kckra
                Route::prefix('kckra')->group(function () {
                    Route::get('/', 'ReportKckraController@fileDownload');
                    Route::post('datatable', 'ReportKckraController@fileDownloadDatatable');
                    Route::post('processing', 'ReportKckraController@fileDownloadProcessing');
                    Route::get('show_description/{id}', 'ReportKckraController@fileDownloadDescription');
                    Route::get('download/{id}', 'ReportKckraController@fileDownloadRun');
                });
            });

            // Periodic
            Route::prefix('periodic')->middleware('protect.menu:admin/report/periodic')->group(function () {
                Route::get('/', 'ReportController@periodic');
                Route::get('load_data', 'ReportController@loadDataPeriodic');
            });
        });

        // Setting
        Route::prefix('setting')->group(function () {
            // User
            Route::prefix('user')->middleware('protect.menu:admin/setting/user')->group(function () {
                Route::get('/', 'AdminController@index');
                Route::get('datatable', 'AdminController@datatable');
                Route::post('create', 'AdminController@create');
                Route::get('show/{id}', 'AdminController@show');
                Route::post('update/{id}', 'AdminController@update');
                Route::get('destroy/{id}', 'AdminController@destroy');
                Route::get('reset_password/{id}', 'AdminController@resetPassword');
            });

            // Role
            Route::prefix('role')->middleware('protect.menu:admin/setting/role')->group(function () {
                Route::get('/', 'RoleController@index');
                Route::get('datatable', 'RoleController@datatable');
                Route::post('create', 'RoleController@create');
                Route::get('show/{id}', 'RoleController@show');
                Route::post('update/{id}', 'RoleController@update');
                Route::get('destroy/{id}', 'RoleController@destroy');
                Route::get('user_access/{role_id}', 'UserAccessController@index');
                Route::post('user_access/checkbox_permission', 'UserAccessController@checkboxPermission');
            });

            // Menu
            Route::prefix('menu')->middleware('protect.menu:admin/setting/menu')->group(function () {
                Route::get('/', 'MenuController@index');
                Route::get('datatable', 'MenuController@datatable');
                Route::post('create', 'MenuController@create');
                Route::get('show/{id}', 'MenuController@show');
                Route::post('update/{id}', 'MenuController@update');
                Route::get('destroy/{id}', 'MenuController@destroy');
            });

            // Terms Condition
            Route::middleware('protect.menu:admin/setting/terms_condition')
                ->match(['get', 'post'], 'terms_condition', 'SettingController@termsCondition');

            // About Me
            Route::middleware('protect.menu:admin/setting/about_me')
                ->match(['get', 'post'], 'about_me', 'SettingController@aboutMe');
        });

        // Setting
        Route::prefix('template_email')->group(function () {
            // Activation
            Route::prefix('activation')->middleware('protect.menu:admin/template_email/activation')->group(function () {
                Route::get('/', 'TemplateEmailController@activation');
            });

            // Change Password
            Route::prefix('change_password')->middleware('protect.menu:admin/template_email/change_password')->group(function () {
                Route::get('/', 'TemplateEmailController@changePassword');
            });

            // Collection Problem
            Route::prefix('collection_problem')->middleware('protect.menu:admin/template_email/collection_problem')->group(function () {
                Route::get('/', 'TemplateEmailController@collectionProblem');
            });

            // Collection Submitted
            Route::prefix('collection_submitted')->middleware('protect.menu:admin/template_email/collection_submitted')->group(function () {
                Route::get('/', 'TemplateEmailController@collectionSubmitted');
            });

            // Collection Success
            Route::prefix('collection_success')->middleware('protect.menu:admin/template_email/collection_success')->group(function () {
                Route::get('/', 'TemplateEmailController@collectionSuccess');
            });

            // Collection Bulk SUccess
            Route::prefix('collection_bulk')->middleware('protect.menu:admin/template_email/collection_bulk')->group(function () {
                Route::get('/', 'TemplateEmailController@collectionBulk');
            });

            // Reset Password
            Route::prefix('reset_password')->middleware('protect.menu:admin/template_email/reset_password')->group(function () {
                Route::get('/', 'TemplateEmailController@resetPassword');
            });

            // Publisher Rejected
            Route::prefix('publisher_rejected')->middleware('protect.menu:admin/template_email/publisher_rejected')->group(function () {
                Route::get('/', 'TemplateEmailController@publisherRejected');
            });

            // Publisher Submission
            Route::prefix('publisher_submission')->middleware('protect.menu:admin/template_email/publisher_submission')->group(function () {
                Route::get('/', 'TemplateEmailController@publisherSubmission');
            });

            // Publisher Success
            Route::prefix('publisher_success')->middleware('protect.menu:admin/template_email/publisher_success')->group(function () {
                Route::get('/', 'TemplateEmailController@publisherSuccess');
            });

            // Delivery Receipt
            Route::prefix('delivery_receipt')->middleware('protect.menu:admin/template_email/delivery_receipt')->group(function () {
                Route::get('/', 'TemplateEmailController@deliveryReceipt');
            });

            // Header
            Route::prefix('header')->middleware('protect.menu:admin/template_email/header')->group(function () {
                Route::get('/', 'TemplateEmailController@header');
            });

            // Footer
            Route::prefix('footer')->middleware('protect.menu:admin/template_email/footer')->group(function () {
                Route::get('/', 'TemplateEmailController@footer');
            });

            // Create Or Update
            Route::post('create_update', 'TemplateEmailController@createUpdate');
        });

        // Role
        Route::prefix('collection/request')->middleware('protect.menu:admin/collection/request')->group(function () {
            Route::get('/', 'CollectionRequestFileOriginal@index');
            Route::post('update_status', 'CollectionRequestFileOriginal@update');
            Route::get('datatable', 'CollectionRequestFileOriginal@datatable');
        });

        // Guest
        Route::prefix('guest')->middleware('protect.menu:admin/guest')->group(function () {
            Route::get('/', 'GuestController@index');
            Route::get('datatable', 'GuestController@datatable');
            Route::get('detail/{id}', 'GuestController@show');
        });

        Route::prefix('faq')->middleware('protect.menu:admin/faq')->group(function () {
            Route::get('/', 'FaqController@index');
            Route::post('datatable', 'FaqController@datatable');
            Route::post('create', 'FaqController@create');
            Route::get('show/{id}', 'FaqController@show');
            Route::post('update/{id}', 'FaqController@update');
            Route::post('destroy/{id}', 'FaqController@destroy');
        });

        Route::prefix('tutorial')->middleware('protect.menu:admin/tutorial')->group(function () {
            Route::get('/', 'TutorialController@index');
            Route::post('datatable', 'TutorialController@datatable');
            Route::post('create', 'TutorialController@create');
            Route::get('show/{id}', 'TutorialController@show');
            Route::post('update/{id}', 'TutorialController@update');
            Route::post('destroy/{id}', 'TutorialController@destroy');
        });

        Route::prefix('library_location')->middleware('protect.menu:admin/library_location')->group(function () {
            Route::get('/', 'LibraryLocationController@index');
            Route::post('datatable', 'LibraryLocationController@datatable');
            Route::post('create', 'LibraryLocationController@create');
            Route::get('show/{id}', 'LibraryLocationController@show');
            Route::post('update/{id}', 'LibraryLocationController@update');
            Route::post('destroy/{id}', 'LibraryLocationController@destroy');
        });

        Route::prefix('publisher-warning')->middleware('protect.menu:admin/publisher-warning')->group(function () {
            Route::get('/', 'PublisherWarningController@index');
            Route::post('datatable', 'PublisherWarningController@datatable');
            Route::post('datatable/list', 'PublisherWarningController@listDatatable');
            Route::post('publisher/datatable', 'PublisherWarningController@publisherDatatable');
            Route::match(['get', 'post'], 'create', 'PublisherWarningController@create');
            Route::match(['get', 'post'], 'update/{id}', 'PublisherWarningController@update');
            Route::get('destroy/{id}', 'PublisherWarningController@destroy');
            Route::get('show/{id}', 'PublisherWarningController@show');
            Route::post('update/{id}', 'PublisherWarningController@update');
            Route::get('count/{publisherId}', 'PublisherWarningController@countPublisherWarnings');
        });
    });
});

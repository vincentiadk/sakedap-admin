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
        Route::post('datatable', 'LogActivityController@datatable');
    });

    Route::get('notification', 'NotificationController@index');

    Route::prefix('auth')->group(function () {
        Route::match(['get', 'post'], 'change-password', 'AuthController@changePassword');
        Route::match(['get', 'post'], 'profile', 'AuthController@profile');
        Route::get('logout', 'AuthController@logout');
    });

    Route::prefix('datatable-serverside')->group(function () {
        Route::post('catalog', 'DataTableServersideController@catalog');
        Route::post('catalog-parent', 'DataTableServersideController@catalogParent');
        Route::post('catalog-history', 'DataTableServersideController@catalogHistory');
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
        Route::get('news-category', 'Select2ServersideController@newsCategory');
        Route::get('news', 'Select2ServersideController@news');
        Route::get('user', 'Select2ServersideController@user');
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
        Route::get('data-collection-status', 'DashboardController@dataCollectionStatus');
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
            Route::post('datatable', 'DeliveryVerificationController@datatable');
            Route::post('datatable-collection', 'DeliveryVerificationController@datatableCollection');
            Route::post('checked-action', 'DeliveryVerificationController@checkedAction');
            Route::match(['get', 'post'], 'detail/{id}', 'DeliveryVerificationController@detail');
            Route::match(['get', 'post'], 'update-data/{id}', 'DeliveryVerificationController@updateData');
            Route::delete('destroy-data', 'DeliveryVerificationController@destroyData');
            Route::delete('destroy-data-ld/{id}', 'DeliveryVerificationController@destroyDataLD');
        });

        Route::prefix('single-verification')->group(function () {
            Route::get('/', 'SingleVerificationController@index');
            Route::post('search', 'SingleVerificationController@search');
        });    

        Route::prefix('delivery-to-destination')->group(function () {
            Route::get('/', 'DeliveryToDestinationController@index');
            Route::post('datatable', 'DeliveryToDestinationController@datatable');
            Route::get('detail/{id}', 'DeliveryToDestinationController@detail');
        });

        Route::prefix('in-delivery')->group(function () {
            Route::get('/', 'InDeliveryController@index');
            Route::post('datatable', 'InDeliveryController@datatable');
            Route::get('detail/{id}', 'InDeliveryController@detail');
            Route::post('mark-sent', 'InDeliveryController@markSent');
        });

        Route::prefix('accept')->group(function () {
            Route::get('/', 'AcceptController@index');
            Route::post('datatable', 'AcceptController@datatable');
            Route::get('detail/{id}', 'AcceptController@detail');
            Route::get('print/{id}', 'AcceptController@print');
            Route::post('send-email', 'AcceptController@sendEmail');
            Route::post('send-whatsapp', 'AcceptController@sendWhatsapp');
            Route::post('isbn-numbering', 'AcceptController@isbnNumbering');
            Route::post('letter-update', 'AcceptController@letterUpdate');
            Route::delete('destroy-collection', 'AcceptController@destroyCollection');
            Route::delete('destroy-letter/{id}', 'AcceptController@destroyLetter');
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
            Route::post('datatable', 'CollectionOnDeliveryController@datatable');
            Route::get('detail', 'CollectionOnDeliveryController@detail');
        });

        Route::prefix('collection-accept')->group(function () {
            Route::get('/', 'CollectionAcceptController@index');
            Route::post('datatable', 'CollectionAcceptController@datatable');
            Route::get('detail', 'CollectionAcceptController@detail');
            Route::post('set-received/{id}', 'CollectionAcceptController@setReceived');
        });

        Route::prefix('collection-reject')->group(function () {
            Route::get('/', 'CollectionRejectController@index');
            Route::post('datatable', 'CollectionRejectController@datatable');
            Route::post('grant', 'CollectionRejectController@grant');
            Route::post('retur', 'CollectionRejectController@retur');
        });

        Route::prefix('collection-grant')->group(function () {
            Route::get('/', 'CollectionGrantController@index');
            Route::post('datatable', 'CollectionGrantController@datatable');
            Route::post('create-group', 'CollectionGrantController@createGroup');
            Route::post('out-group', 'CollectionGrantController@outGroup');
        });

        Route::prefix('collection-retur')->group(function () {
            Route::get('/', 'CollectionReturController@index');
            Route::post('datatable', 'CollectionReturController@datatable');
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
            Route::post('datatable', 'LabelController@datatable');
            Route::get('print/{type}', 'LabelController@print');
        });
    });

    Route::prefix('digital-storage-handover')->namespace('DigitalStorageHandover')->group(function () {
        Route::prefix('review')->group(function () {
            Route::get('/', 'ReviewController@index');
            Route::post('datatable', 'ReviewController@datatable');
            Route::match(['get', 'post'], 'detail/{id}', 'ReviewController@detail');
        });

        Route::prefix('review-edition')->group(function () {
            Route::get('/', 'ReviewEditionController@index');
            Route::post('datatable', 'ReviewEditionController@datatable');
            Route::match(['get', 'post'], 'detail/{id}', 'ReviewEditionController@detail');
        });

        Route::prefix('problem')->group(function () {
            Route::get('/', 'ProblemController@index');
            Route::post('datatable', 'ProblemController@datatable');
        });

        Route::prefix('accept')->group(function () {
            Route::get('/', 'AcceptController@index');
            Route::post('datatable', 'AcceptController@datatable');
            Route::get('detail/{id}', 'AcceptController@detail');
        });
        
        Route::prefix('accept-article-journal')->group(function () {
            Route::get('/', 'AcceptArticleJournalController@index');
            Route::post('datatable', 'AcceptArticleJournalController@datatable');
            Route::get('detail', 'AcceptArticleJournalController@detail');
            Route::post('verification', 'AcceptArticleJournalController@verification');
            Route::delete('destroy-data', 'AcceptArticleJournalController@destroyData');
            Route::post('update-inline-field', 'AcceptArticleJournalController@updateInlineField');
        });

        Route::prefix('reject')->group(function () {
            Route::get('/', 'RejectController@index');
            Route::post('datatable', 'RejectController@datatable');
        });

        Route::prefix('single-upload')->group(function () {
            Route::get('/', 'SingleUploadController@index');
            Route::get('check-isbn-code', 'SingleUploadController@checkISBNCode');
            Route::get('catalog-parent', 'SingleUploadController@catalogParent');
            Route::post('submitted', 'SingleUploadController@submitted');
        });

        Route::prefix('bulk-upload')->group(function () {
            Route::get('/', 'BulkUploadController@index');
            Route::post('datatable-bulk', 'BulkUploadController@datatableBulk');
            Route::get('detail-bulk', 'BulkUploadController@detailBulk');
            Route::post('submitted', 'BulkUploadController@submitted');
        });
        Route::prefix('journal/zip-upload')->group(function () {
            Route::get('/', 'JournalUploadController@index')->name('journal.zip.index');
            Route::post('datatable', 'JournalUploadController@datatable');
            Route::post('store', 'JournalUploadController@store')->name('journal.zip.store');
            Route::get('progress/{id}', 'JournalUploadController@progress')->name('journal.zip.progress');
            Route::get('history/{id}', 'JournalUploadController@show')->name('journal.zip.show');
            Route::post('history/datatable/{id}', 'JournalUploadController@datatableShow');
            Route::get('history/progress-realtime/{id}', 'JournalUploadController@progressRealtime')->name('journal.zip.progress-realtime');
            });
    });

    Route::prefix('bill-isbn')->group(function () {
        Route::get('/', 'BillISBNController@index');
        Route::post('datatable', 'BillISBNController@datatable');
    });

    Route::prefix('coaching-supervision')->namespace('CoachingSupervision')->group(function () {
        Route::prefix('create-executor')->group(function () {
            Route::get('/', 'CreateExecutorController@index');
            Route::post('submitted', 'CreateExecutorController@submitted');
        });

        Route::prefix('executor-list')->group(function () {
            Route::get('/', 'ExecutorListController@index');
            Route::post('datatable', 'ExecutorListController@datatable');
            Route::get('show-data', 'ExecutorListController@showData');
            Route::post('update-data', 'ExecutorListController@updateData');
            Route::post('send-email-reset-password', 'ExecutorListController@sendEmailResetPassword');
            Route::post('approve-api-access', 'ExecutorListController@approveAPIAccess');
            Route::post('reject-api-access', 'ExecutorListController@rejectAPIAccess');
            Route::post('revoke-api-access', 'ExecutorListController@revokeAPIAccess');
            Route::delete('destroy-data', 'ExecutorListController@destroyData');
        });

        Route::prefix('problem')->group(function () {
            Route::get('/', 'ProblemController@index');
            Route::post('datatable', 'ProblemController@datatable');
        });

        Route::prefix('review')->group(function () {
            Route::get('/', 'ReviewController@index');
            Route::post('datatable', 'ReviewController@datatable');
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
            Route::post('datatable', 'WarningController@datatable');
            Route::post('create-data', 'WarningController@createData');
            Route::get('show-data', 'WarningController@showData');
            Route::post('update-data', 'WarningController@updateData');
            Route::post('lockable', 'WarningController@lockable');
            Route::delete('destroy-data', 'WarningController@destroyData');
            Route::post('send-email', 'WarningController@sendEmail');
            Route::post('send-whatsapp', 'WarningController@sendWhatsapp');
        });

        Route::prefix('executor-group')->group(function () {
            Route::get('/', 'ExecutorGroupController@index');
            Route::post('datatable', 'ExecutorGroupController@datatable');
            Route::post('create-data', 'ExecutorGroupController@createData');
            Route::get('show-data', 'ExecutorGroupController@showData');
            Route::post('update-data', 'ExecutorGroupController@updateData');
            Route::delete('destroy-data', 'ExecutorGroupController@destroyData');
        });

        Route::prefix('executor-access')->group(function () {
            Route::get('/', 'ExecutorAccessController@index');
            Route::post('datatable', 'ExecutorAccessController@datatable');
            Route::post('create-data', 'ExecutorAccessController@createData');
            Route::get('show-data', 'ExecutorAccessController@showData');
            Route::post('update-data', 'ExecutorAccessController@updateData');
            Route::delete('destroy-data', 'ExecutorAccessController@destroyData');
        });
    });

    Route::prefix('report')->namespace('Report')->group(function () {
        Route::prefix('delivery')->group(function () {
            Route::get('/', 'DeliveryController@index');
            Route::post('datatable', 'DeliveryController@datatable');
        });

        Route::prefix('promotion')->group(function () {
            Route::get('/', 'PromotionController@index');
            Route::post('datatable', 'PromotionController@datatable');
        });

        Route::prefix('physical-reception')->group(function () {
            Route::get('/', 'PhysicalReceptionController@index');
            Route::post('datatable', 'PhysicalReceptionController@datatable');
            Route::post('datatable-summary', 'PhysicalReceptionController@datatableSummary');
        });

        Route::prefix('physical-recording')->group(function () {
            Route::get('/', 'PhysicalRecordingController@index');
        });

        Route::prefix('physical-alignment')->group(function () {
            Route::get('/', 'PhysicalAlignmentController@index');
            Route::post('datatable', 'PhysicalAlignmentController@datatable');
        });

        Route::prefix('digital-empowerment')->group(function () {
            Route::get('/', 'DigitalEmpowermentController@index');
            Route::post('datatable', 'DigitalEmpowermentController@datatable');
        });

        Route::prefix('physical-empowerment')->group(function () {
            Route::get('/', 'PhysicalEmpowermentController@index');
            Route::post('datatable', 'PhysicalEmpowermentController@datatable');
        });

        Route::prefix('manage')->group(function () {
            Route::get('/', 'ManageController@index');
            Route::post('datatable', 'ManageController@datatable');
            Route::get('detail/{id}', 'ManageController@detail');
        });

        Route::prefix('digital-manage')->group(function () {
            Route::get('/', 'DigitalManageController@index');
            Route::post('datatable', 'DigitalManageController@datatable');
        });

        Route::prefix('service')->group(function () {
            Route::get('/', 'ServiceController@index');
            Route::get('load-data', 'ServiceController@loadData');
        });

        Route::prefix('physical-recording')->group(function () {
            Route::get('/', 'PhysicalRecordingController@index');
        });

        Route::prefix('asset')->group(function () {
            Route::get('/', 'AssetController@index');
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
            Route::post('test-send-whatsapp', 'SettingSystemController@testSendWhatsapp');
        });

        Route::prefix('promotion')->group(function () {
            Route::get('/', 'PromotionController@index');
            Route::post('datatable', 'PromotionController@datatable');
            Route::post('create-data', 'PromotionController@createData');
            Route::get('show-data', 'PromotionController@showData');
            Route::post('update-data', 'PromotionController@updateData');
            Route::delete('destroy-data', 'PromotionController@destroyData');
        });

        Route::prefix('template-email')->group(function () {
            Route::get('/', 'TemplateEmailController@index');
            Route::post('datatable', 'TemplateEmailController@datatable');
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

        Route::prefix('site-setting')->group(function () {
            Route::get('/', 'SiteSettingController@index');
            Route::post('submitted', 'SiteSettingController@submitted');
        });

        Route::prefix('news')->group(function () {
            Route::get('/', 'NewsController@index');
            Route::post('datatable', 'NewsController@datatable');
            Route::get('preview/{id}', 'NewsController@preview');
            Route::post('create-data', 'NewsController@createData');
            Route::get('show-data', 'NewsController@showData');
            Route::post('update-data', 'NewsController@updateData');
            Route::delete('destroy-data', 'NewsController@destroyData');
        });

        Route::prefix('event')->group(function () {
            Route::get('/', 'EventController@index');
            Route::post('datatable', 'EventController@datatable');
            Route::get('preview/{id}', 'EventController@preview');
            Route::post('create-data', 'EventController@createData');
            Route::get('show-data', 'EventController@showData');
            Route::post('update-data', 'EventController@updateData');
            Route::delete('destroy-data', 'EventController@destroyData');
        });

        Route::prefix('tutorial')->group(function () {
            Route::get('/', 'TutorialController@index');
            Route::post('datatable', 'TutorialController@datatable');
            Route::get('preview/{id}', 'TutorialController@preview');
            Route::post('create-data', 'TutorialController@createData');
            Route::get('show-data', 'TutorialController@showData');
            Route::post('update-data', 'TutorialController@updateData');
            Route::delete('destroy-data', 'TutorialController@destroyData');
        });

        Route::prefix('pages')->group(function () {
            Route::get('/', 'PagesController@index');
            Route::post('submitted', 'PagesController@submitted');
        });

        Route::prefix('banner')->group(function () {
            Route::get('/', 'BannerController@index');
            Route::post('datatable', 'BannerController@datatable');
            Route::post('create-data', 'BannerController@createData');
            Route::get('show-data', 'BannerController@showData');
            Route::post('update-data', 'BannerController@updateData');
            Route::delete('destroy-data', 'BannerController@destroyData');
        });

        Route::prefix('faq')->group(function () {
            Route::get('/', 'FaqController@index');
            Route::post('datatable', 'FaqController@datatable');
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
            Route::post('datatable', 'MediaTypeController@datatable');
            Route::post('create-data', 'MediaTypeController@createData');
            Route::get('show-data', 'MediaTypeController@showData');
            Route::post('update-data', 'MediaTypeController@updateData');
            Route::delete('destroy-data', 'MediaTypeController@destroyData');
        });

        Route::prefix('news-category')->group(function () {
            Route::get('/', 'NewsCategoryController@index');
            Route::post('datatable', 'NewsCategoryController@datatable');
            Route::post('create-data', 'NewsCategoryController@createData');
            Route::get('show-data', 'NewsCategoryController@showData');
            Route::post('update-data', 'NewsCategoryController@updateData');
            Route::delete('destroy-data', 'NewsCategoryController@destroyData');
        });

        Route::prefix('collection-category')->group(function () {
            Route::get('/', 'CollectionCategoryController@index');
            Route::post('datatable', 'CollectionCategoryController@datatable');
            Route::post('create-data', 'CollectionCategoryController@createData');
            Route::get('show-data', 'CollectionCategoryController@showData');
            Route::post('update-data', 'CollectionCategoryController@updateData');
            Route::delete('destroy-data', 'CollectionCategoryController@destroyData');
        });

        Route::prefix('compliance')->group(function () {
            Route::get('/', 'ComplianceController@index');
            Route::post('datatable', 'ComplianceController@datatable');
            Route::post('create-data', 'ComplianceController@createData');
            Route::get('show-data', 'ComplianceController@showData');
            Route::post('update-data', 'ComplianceController@updateData');
            Route::delete('destroy-data', 'ComplianceController@destroyData');
        });

        Route::prefix('problem')->group(function () {
            Route::get('/', 'ProblemController@index');
            Route::post('datatable', 'ProblemController@datatable');
            Route::post('create-data', 'ProblemController@createData');
            Route::get('show-data', 'ProblemController@showData');
            Route::post('update-data', 'ProblemController@updateData');
            Route::delete('destroy-data', 'ProblemController@destroyData');
        });

        Route::prefix('library')->group(function () {
            Route::get('/', 'LibraryController@index');
            Route::post('datatable', 'LibraryController@datatable');
            Route::post('create-data', 'LibraryController@createData');
            Route::get('show-data', 'LibraryController@showData');
            Route::post('update-data', 'LibraryController@updateData');
            Route::delete('destroy-data', 'LibraryController@destroyData');
        });

        Route::prefix('depo')->group(function () {
            Route::get('/', 'DepoController@index');
            Route::post('datatable', 'DepoController@datatable');
            Route::post('create-data', 'DepoController@createData');
            Route::get('show-data', 'DepoController@showData');
            Route::post('update-data', 'DepoController@updateData');
            Route::delete('destroy-data', 'DepoController@destroyData');
        });

        Route::prefix('leader')->group(function () {
            Route::get('/', 'LeaderController@index');
            Route::post('datatable', 'LeaderController@datatable');
            Route::post('create-data', 'LeaderController@createData');
            Route::get('show-data', 'LeaderController@showData');
            Route::post('update-data', 'LeaderController@updateData');
            Route::delete('destroy-data', 'LeaderController@destroyData');
        });

        Route::prefix('storage-space')->group(function () {
            Route::get('/', 'StorageSpaceController@index');
            Route::post('datatable', 'StorageSpaceController@datatable');
            Route::post('create-data', 'StorageSpaceController@createData');
            Route::get('show-data', 'StorageSpaceController@showData');
            Route::post('update-data', 'StorageSpaceController@updateData');
            Route::delete('destroy-data', 'StorageSpaceController@destroyData');
        });

        Route::prefix('size-weight-book')->group(function () {
            Route::get('/', 'SizeWeightBookController@index');
            Route::post('datatable', 'SizeWeightBookController@datatable');
            Route::post('create-data', 'SizeWeightBookController@createData');
            Route::get('show-data', 'SizeWeightBookController@showData');
            Route::post('update-data', 'SizeWeightBookController@updateData');
            Route::delete('destroy-data', 'SizeWeightBookController@destroyData');
        });

        Route::prefix('delivery-service')->group(function () {
            Route::get('/', 'DeliveryServiceController@index');
        });

        Route::prefix('setting-deposit-number')->group(function () {
            Route::get('/', 'SettingDepositNumberController@index');
        });

        Route::prefix('province')->group(function () {
            Route::get('/', 'ProvinceController@index');
            Route::post('datatable', 'ProvinceController@datatable');
            Route::post('create-data', 'ProvinceController@createData');
            Route::get('show-data', 'ProvinceController@showData');
            Route::post('update-data', 'ProvinceController@updateData');
            Route::delete('destroy-data', 'ProvinceController@destroyData');
        });

        Route::prefix('city')->group(function () {
            Route::get('/', 'CityController@index');
            Route::post('datatable', 'CityController@datatable');
            Route::post('create-data', 'CityController@createData');
            Route::get('show-data', 'CityController@showData');
            Route::post('update-data', 'CityController@updateData');
            Route::delete('destroy-data', 'CityController@destroyData');
        });

        Route::prefix('district')->group(function () {
            Route::get('/', 'DistrictController@index');
            Route::post('datatable', 'DistrictController@datatable');
            Route::post('create-data', 'DistrictController@createData');
            Route::get('show-data', 'DistrictController@showData');
            Route::post('update-data', 'DistrictController@updateData');
            Route::delete('destroy-data', 'DistrictController@destroyData');
        });

        Route::prefix('village')->group(function () {
            Route::get('/', 'VillageController@index');
            Route::post('datatable', 'VillageController@datatable');
            Route::post('create-data', 'VillageController@createData');
            Route::get('show-data', 'VillageController@showData');
            Route::post('update-data', 'VillageController@updateData');
            Route::delete('destroy-data', 'VillageController@destroyData');
        });
    });

    Route::prefix('request-file')->group(function () {
        Route::get('/', 'RequestFileController@index');
        Route::post('datatable', 'RequestFileController@datatable');
        Route::post('set-status', 'RequestFileController@setStatus');
    });

    Route::prefix('award')->group(function () {
        Route::get('/', 'AwardController@index');
        Route::post('datatable', 'AwardController@datatable');
        Route::post('create-data', 'AwardController@createData');
        Route::get('show-data', 'AwardController@showData');
        Route::post('update-data', 'AwardController@updateData');
        Route::delete('destroy-data', 'AwardController@destroyData');

        Route::prefix('nomination/{id}')->group(function () {
            Route::match(['get', 'post'], '/', 'AwardController@nomination');
            Route::post('datatable', 'AwardController@nominationDatatable');
            Route::post('add', 'AwardController@nominationAdd');
            Route::delete('remove', 'AwardController@nominationRemove');
        });
    });
});

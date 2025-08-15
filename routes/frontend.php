<?php

use Illuminate\Support\Facades\Route;
//////////////////// FRONTEND ////////////////////

Route::group(['namespace' => 'Frontend'], function () {
    Route::redirect('/', 'login');
    Route::get('/article', 'ArticleController@index');
    Route::get('/article/{slug}', 'ArticleController@detail');

    Route::get('/collection', 'CollectionController@index');
    Route::get('/collection/images/{file}', 'FileController@getFileFromEncrypt');
    Route::get('/collection/cover/{id}', 'FileController@getCover');
    Route::get('/collection/file.epub', 'FileController@getEpub');
    Route::get('/banner/{id}', 'FileController@getBanner');
    Route::get('/collection/load_image_pdf', 'CollectionController@loadImagePdf');
    Route::get('/collection/{slug}/{id}', 'CollectionController@detail');
    Route::get('/collection-iframe/{id}', 'CollectionController@detailIframe');
    Route::get('/faq', 'FaqController@index');
    Route::get('/tutorial', 'TutorialController@index');
    Route::get('/statistic', 'StatisticController@index');
    Route::post('/statistic/datatable-isbn', 'StatisticController@publisherISBNDatatable');
    Route::get('/tutorial/{slug}', 'TutorialController@detail');
    Route::get('/stepper', 'StepperController@index');
    Route::post('/get-data-isbn-stepper', 'StepperController@getDataIsbn');
    Route::post('/get-data-isrc-stepper', 'StepperController@getDataIsrc');
    Route::post('/generate-number-stepper', 'StepperController@generateUniqueCode');
    Route::post('/compare-number-stepper', 'StepperController@compareUniqueCode');
    Route::post('/datatable_copies/{id}/{type?}', 'CollectionController@datatableCopies');
    Route::post('/datatable_editions/{id}', 'CollectionController@datatableEditions');

    Route::prefix('select2_serverside')->group(function () {
        Route::post('load_province', 'HandleLoadSelect2Controller@loadProvince');
    });
});

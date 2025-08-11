<?php

use Illuminate\Http\Request;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['namespace' => 'API'], function() {

	//route authentications
	Route::post('version', 'VersionController@index');
	Route::post('get-token', 'TokenController@authentication');


	Route::get('collection/media/audio/{encrypt}', 'CollectionMediaController@getFileAudio');
	Route::get('collection/media/audiovisual/{encrypt}', 'CollectionMediaController@getFileAudioVisual');


	Route::group(['middleware' => 'desktop_token'], function() {

		Route::post('collections', 'CollectionController@getAll');

		Route::group(['prefix' => 'collection'], function() {
			Route::post('favorite', 'CollectionController@favorite');
			Route::post('category', 'CollectionCategoryController@get');
			Route::post('subject', 'CollectionSubjectController@get');
			Route::post('type', 'CollectionTypeController@get');
			Route::post('{id}', 'CollectionController@findOne');
			Route::post('serial/{id}', 'CollectionController@serial');
			Route::post('serial/{id}/detail/{serialid}', 'CollectionController@serialDetail');
			Route::post('pdf/{id}/page/{page}', 'CollectionMediaController@getContentBook');
			Route::post('book/{id}', 'CollectionMediaController@getContentBook2');
			Route::post('serial-file/{id}', 'CollectionMediaController@getContentBook2');
			Route::post('audio/{id}', 'CollectionMediaController@getContentAudio');
			Route::post('audiovisual/{id}', 'CollectionMediaController@getContentAudioVisual');
			Route::post('cover/{id}', 'CollectionMediaController@cover');
		});

		Route::group(['prefix' => 'collection-access'], function() {
			Route::post('/', 'CollectionAccessController@save');
			Route::post('/{id}', 'CollectionAccessController@get');
			Route::post('/last/{id}', 'CollectionAccessController@last');
		});

		Route::group(['prefix' => 'collection-favourite'], function() {
			Route::post('/', 'CollectionFavouriteController@save');
			Route::post('/get', 'CollectionFavouriteController@get');
		});

		Route::group(['prefix' => 'kunjungan'], function() {
			Route::post('/save', 'KunjunganController@save');
			Route::post('/statistics', 'KunjunganController@statistics');
			Route::post('/', 'KunjunganController@index');
		});

		Route::group(['prefix' => 'pemustaka'], function() {
			Route::post('/detail', 'PemustakaController@getDetail');
		});

	});

});


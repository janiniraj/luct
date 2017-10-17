<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/articles/{page?}', [
    'uses' => 'API\ArticleController@index',
    'as'   => 'article.index'
]);

Route::post('/login', 'API\ApiController@login');

Route::group(['middleware' => 'jwt.auth'], function() {
    Route::get('/check-user', [
        'uses' => 'API\ApiController@checkUser',
        'as'   => 'auth.check-user'
    ]);

    Route::post('/bookmark', [
        'uses' => 'API\BookmarkController@create',
        'as'   => 'bookmark.create'
    ]);

    Route::post('/remove-bookmark', [
        'uses' => 'API\BookmarkController@remove',
        'as'   => 'bookmark.remove'
    ]);


});
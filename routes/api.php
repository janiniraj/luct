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

/*
|--------------------------------------------------------------------------
| No Authentication APIs
|--------------------------------------------------------------------------
|
*/
Route::post('/login', 'API\ApiController@login');
Route::post('/member-login', 'API\ApiController@memberLogin');
Route::post('/student-login', 'API\ApiController@studentLogin');

/*
|--------------------------------------------------------------------------
| Authenticated APIs
|--------------------------------------------------------------------------
|
*/
Route::group(['middleware' => 'jwt.auth'], function()
{
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

/*
|--------------------------------------------------------------------------
| Guest User APIs - Authentication is optional
|--------------------------------------------------------------------------
|
*/
Route::group(['middleware' => 'jwt.guest'], function()
{
    Route::get('/articles/{page?}', [
        'uses' => 'API\ArticleController@index',
        'as'   => 'article.index'
    ]);

    Route::get('/article/{id}', [
        'uses' => 'API\ArticleController@show',
        'as'   => 'article.show'
    ]);

    Route::get('/events/{page?}', [
        'uses' => 'API\EventController@index',
        'as'   => 'event.index'
    ]);

    Route::get('/event/{id}', [
        'uses' => 'API\EventController@show',
        'as'   => 'event.show'
    ]);

    Route::get('/special_features/{page?}', [
        'uses' => 'API\SpecialFeatureController@index',
        'as'   => 'special_feature.index'
    ]);

    Route::get('/special_feature/{id}', [
        'uses' => 'API\SpecialFeatureController@show',
        'as'   => 'special_feature.show'
    ]);

    Route::get('/highlights', [
        'uses' => 'API\HighLightController@index',
        'as'   => 'highlight.index'
    ]);

    Route::get('/courses', [
        'uses' => 'API\CourseController@index',
        'as'   => 'course.index'
    ]);

    Route::get('/course/{id}', [
        'uses' => 'API\CourseController@show',
        'as'   => 'course.show'
    ]);
});
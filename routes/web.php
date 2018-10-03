<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/privacy', 'HomeController@index')->name('privacy');
Route::get('/terms', 'HomeController@index')->name('terms');

# Facebook Login
Route::get('login/facebook', 'Auth\LoginController@redirectToProvider')->name('fb_login');
Route::get('login/facebook/callback', 'Auth\LoginController@handleProviderCallback')->name('fb_login_callback');

Route::get('api/tourpoints', 'Api\TourPointController@index')->name('tp_all');
Route::get('api/tourpoints/me', 'Api\TourPointController@listByUser')->name('tp_all_me');
Route::get('api/tourpoints/{id}', 'Api\TourPointController@show')->name('tp_show');
Route::post('api/tourpoints', 'Api\TourPointController@store')->name('tp_store');
Route::put('api/tourpoints/{id}', 'Api\TourPointController@update')->name('tp_update');
Route::delete('api/tourpoints/{id}', 'Api\TourPointController@destroy')->name('tp_destroy');
Route::post('api/tourpoints/{id}/checkin', 'Api\TourPointController@checkIn')->name('tp_checkin');

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

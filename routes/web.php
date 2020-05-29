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

Route::get('/','StaticPagesController@home')->name('home');
Route::get('/help','StaticPagesController@help')->name('help');
Route::get('/about','StaticPagesController@about')->name('about');
Route::get('signup','UsersController@create')->name('signup');
// Route::Resource('users','UsersController');//必须是复数形式的，Route::Resource('user','UserController')会报错。
Route::resource('users', 'UsersController');
Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');

//会话
Route::get('login','SessionsController@create')->name('login');
Route::post('login','SessionsController@store')->name('login');
Route::delete('logout','SessionsController@destory')->name('logout');


Route::resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);

//关注的人
Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings');
//粉丝
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers');



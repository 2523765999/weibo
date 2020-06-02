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

//重置密码
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

//会话
Route::get('login','SessionsController@create')->name('login');
Route::post('login','SessionsController@store')->name('login');
Route::delete('logout','SessionsController@destory')->name('logout');


Route::resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);

//关注的人
Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings');
//粉丝
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers');


//关注用户和取消关注
Route::post('/users/followers/{user}', 'FollowersController@store')->name('followers.store');
Route::delete('/users/followers/{user}', 'FollowersController@destroy')->name('followers.destroy');

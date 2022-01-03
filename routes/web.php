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

Route::get('/all_user', 'MessageController@index')->name('all.user');
Route::get('/get_user_message/{id}', 'MessageController@user_message')->name('user.message');
Route::post('/send_message', 'MessageController@send_message')->name('send.message');
Route::get('/delete_message/{id}', 'MessageController@delete_message')->name('delete.message');
Route::get('/delete_all_message/{id}', 'MessageController@delete_all_message')->name('delete.all.message');

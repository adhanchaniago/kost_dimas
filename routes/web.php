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
    return view('landing');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('guests','GuestsController');

Route::resource('locations','LocationsController');

Route::get('/attendance','AttendanceController@index');

Route::post('/attendance/create','AttendanceController@atd_form');

Route::post('/attendance/submit','AttendanceController@attend');

Route::get('/attendance/record','AttendanceController@record');

Route::post('/attendance/showRecord','AttendanceController@showRecord');

Route::get('/invoice','InvoiceController@index');

Route::post('/invoice/create','InvoiceController@generateInvoice');

Route::get('/invoice/settings','InvoiceController@enterSettings');

Route::post('/invoice/settings','InvoiceController@modifySettings');

Route::get('/receipt','InvoiceController@receipt_index');

Route::post('/receipt/create','InvoiceController@generateReceipt');

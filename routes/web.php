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

// Route::resource('guests','GuestsController');

Route::get('guests','GuestsController@index');

Route::get('guests/get_room','GuestsController@get_room');

Route::post('guests/get_room','GuestsController@create');

Route::post('guests/create','GuestsController@store');

Route::get('guests/{id}','GuestsController@show');

Route::get('guests/{id}/edit','GuestsController@edit');

Route::post('guests/{id}/edit','GuestsController@update');

Route::delete('guests/{id}','GuestsController@destroy');

Route::post('guests/search','GuestsController@search');

Route::resource('locations','LocationsController');
Route::post('locations/search','LocationsController@search');

Route::get('/attendance','AttendanceController@index');

Route::post('/attendance/create','AttendanceController@atd_form');

Route::post('/attendance/submit','AttendanceController@attend');

Route::get('/attendance/record','AttendanceController@record');

Route::post('/attendance/showRecord','AttendanceController@showRecord');

Route::get('/invoice','InvoiceController@index');

Route::post('/invoice/create','InvoiceController@generateInvoice');
// Route::post('/invoice/create','InvoiceController@gen_Invoice');

Route::get('/invoice/settings','InvoiceController@enterSettings');

Route::post('/invoice/settings','InvoiceController@modifySettings');

Route::get('/receipt','InvoiceController@receipt_index');

Route::post('/receipt/create','InvoiceController@generateReceipt');

Route::get('/attendance/report','AttendanceController@createReport');

Route::post('/attendance/report','AttendanceController@generateReport');

Route::get('/404', 'HomeController@notFound')->name('notFound');


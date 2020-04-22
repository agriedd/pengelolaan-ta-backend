<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
// use App\Repository\AdminRepository as Admin;
// use App\Model\Admin as Admin;
// use Closure;

// Route::get('/', function () use ($router) {
//     return Route::app->version();
// });



Route::post('/login', "LoginController@loginPost");

Route::get('/self', "UserController@self");


/**
 * @api /admin
 * 
 * @uses AdminController only SuperAdmin
 * 
 */
Route::group([ 'prefix'	=> 'admin' ], function($router){

	Route::group([ 'prefix' => "{id}" ], function($router){
		Route::get('/', 'AdminController@get');
		Route::delete('/', 'SuperAdmin\AdminController@delete');
		Route::put('/', 'Admin\AdminController@update');
		Route::get('/password/reset', 'SuperAdmin\AdminController@resetPassword');
	});

	Route::get('/', 'AdminController@getAll');
	Route::post('/', 'SuperAdmin\AdminController@insert');
	Route::post('/dosen/{id}', 'SuperAdmin\AdminController@insertByDosen');
	Route::put('/', 'Admin\AdminController@update');

});


/**
 * @api /riwayat
 * 
 */
Route::group(['prefix' => 'riwayat'], function($router){
	Route::get('/', 'SuperAdmin\RiwayatLoginController@getAll');
	Route::get('/self', 'User\RiwayatLoginController@self');
});


Route::group(['prefix' => 'jurusan'], function($router){

	Route::get('/', 'JurusanController@getAll');
	Route::post('/', 'SuperAdmin\JurusanController@insert');
	Route::put('/{id: [0-9]+}', 'Admin\JurusanController@update');
	Route::put('/{kd_jurusan:  }', 'Admin\JurusanController@updateByKdJurusan');
	Route::get('/{id: [0-9]+}', 'JurusanController@get');
	Route::get('/{kd_jurusan: [a-zA-Z0-9\-\_]{4,} }', 'JurusanController@getByKdJurusan');
	Route::delete('/{id: [0-9]+}', 'SuperAdmin\JurusanController@delete');

});


Route::group(['prefix' => 'dosen'], function($router){

	Route::get('/', 'DosenController@getAll');
	Route::post('/', 'Admin\DosenController@insert');
	Route::get('/{id: [0-9]{18} }', 'DosenController@getByNip');
	Route::get('/{id: [0-9]+}', 'DosenController@get');

});


/**
 * @todo tambah kolom kd_prodi
 * @todo router by kd_prodi
 * 
 */
Route::group(['prefix' => 'prodi'], function($router){

	Route::get('/', 'ProdiController@getAll');
	Route::get('/{id: [0-9]+}', 'ProdiController@get');
	Route::put('/{id: [0-9]+}', 'Admin\ProdiController@update');
	Route::post('/', 'SuperAdmin\ProdiController@insert');
	Route::delete('/{id: [0-9]+}', 'SuperAdmin\ProdiController@delete');

});

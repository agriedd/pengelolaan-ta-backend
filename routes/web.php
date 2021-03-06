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
	
	Route::group(['prefix' => "self"], function(){
		Route::get('/', 'User\RiwayatLoginController@self');
		Route::get('/logout', 'User\RiwayatLoginController@logoutAll');
		Route::get('/logout/{id}', 'User\RiwayatLoginController@logout');
	});

	Route::get('/', 'SuperAdmin\RiwayatLoginController@getAll');

	Route::group(['prefix' => 'admin/{id}'], function(){
		Route::get('/clear', 'SuperAdmin\RiwayatLoginController@clearAdmin');
	});
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
	Route::get('/{nip: [0-9]{18} }', 'DosenController@getByNip');

	Route::group(['prefix' => '{id: [0-9]+}'], function(){
		Route::get('/', 'DosenController@get');
		Route::put('/', 'Admin\DosenController@update');
		Route::delete('/', 'Admin\DosenController@delete');
	});

	Route::get('/{username}', 'User\DosenController@getByUsername');
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



/**
 * router darurat
 * 
 */
Route::group(['prefix' => 'edd'], function($router){
	Route::get("/", function(){
		return "Hello world!";
	});
});
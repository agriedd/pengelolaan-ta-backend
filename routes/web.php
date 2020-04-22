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

// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });



Route::post('/login', "LoginController@loginPost");

Route::get('/self', "UserController@self");


/**
 * @api /admin
 * 
 * @uses AdminController only SuperAdmin
 * 
 */
Route::group([ 'prefix'	=> '/admin' ], function($router){
	$router->get('/', 'AdminController@getAll');
	$router->post('/', 'SuperAdmin\AdminController@insert');
	$router->post('/dosen/{id}', 'SuperAdmin\AdminController@insertByDosen');
	$router->get('/{id}', 'AdminController@get');
	$router->delete('/{id}', 'SuperAdmin\AdminController@delete');
	$router->put('/', 'Admin\AdminController@update');
	$router->put('/{id}', 'Admin\AdminController@update');
});


/**
 * @api /riwayat
 * 
 */
Route::group(['prefix' => '/riwayat'], function($router){
	$router->get('/', 'SuperAdmin\RiwayatLoginController@getAll');
	$router->get('/self', 'User\RiwayatLoginController@self');
});


Route::group(['prefix' => '/jurusan'], function($router){

	$router->get('/', 'JurusanController@getAll');
	$router->post('/', 'SuperAdmin\JurusanController@insert');
	$router->put('/{id: [0-9]+}', 'Admin\JurusanController@update');
	$router->put('/{kd_jurusan:  }', 'Admin\JurusanController@updateByKdJurusan');
	$router->get('/{id: [0-9]+}', 'JurusanController@get');
	$router->get('/{kd_jurusan: [a-zA-Z0-9\-\_]{4,} }', 'JurusanController@getByKdJurusan');
	$router->delete('/{id: [0-9]+}', 'SuperAdmin\JurusanController@delete');

});


Route::group(['prefix' => '/dosen'], function($router){

	$router->get('/', 'DosenController@getAll');
	$router->post('/', 'Admin\DosenController@insert');
	$router->get('/{id: [0-9]{18} }', 'DosenController@getByNip');
	$router->get('/{id: [0-9]+}', 'DosenController@get');

});


/**
 * @todo tambah kolom kd_prodi
 * @todo router by kd_prodi
 * 
 */
Route::group(['prefix' => '/prodi'], function($router){

	$router->get('/', 'ProdiController@getAll');
	$router->get('/{id: [0-9]+}', 'ProdiController@get');
	$router->put('/{id: [0-9]+}', 'Admin\ProdiController@update');
	$router->post('/', 'SuperAdmin\ProdiController@insert');
	$router->delete('/{id: [0-9]+}', 'SuperAdmin\ProdiController@delete');

});

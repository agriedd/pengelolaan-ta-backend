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
use App\Model\Admin as Admin;
// use Closure;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/login', "LoginController@loginPost");

Route::group([ 'prefix'	=> '/admin' ], function($router){

	/**
	 * @api /admin
	 * 
	 * @uses AdminController only SuperAdmin
	 * 
	 */
	$router->get('/', 'Admin\AdminController@getAll');
	$router->post('/', 'Admin\AdminController@insert');
	$router->post('/dosen/{id}', 'Admin\AdminController@insertByDosen');
	$router->get('/{id}', 'Admin\AdminController@get');
	$router->put('/{id}', 'Admin\AdminController@update');

});

Route::group(['prefix' => '/riwayat'], function($router){

	/**
	 * @api /riwayat
	 * 
	 * @uses only Super Admin | Admin can access
	 * 
	 */
	$router->get('/', 'RiwayatLoginController@getAll');
});

Route::group(['prefix' => '/jurusan'], function($router){

	$router->get('/', 'JurusanController@getAll');
	$router->post('/', 'SuperAdmin\JurusanController@insert');
	$router->put('/{id: [0-9]+}', 'Admin\JurusanController@update');
	$router->put('/{id: [a-zA-Z0-9\-\_]{4,} }', 'Admin\JurusanController@updateByKdJurusan');
	$router->get('/{id: [0-9]+}', 'JurusanController@get');
	$router->get('/{id: [a-zA-Z0-9\-\_]{4,} }', 'JurusanController@getByKdJurusan');
	$router->delete('/{id: [0-9]+}', 'SuperAdmin\JurusanController@delete');

});

Route::group(['prefix' => '/dosen'], function($router){

	$router->get('/', 'DosenController@getAll');
	$router->post('/', 'Admin\DosenController@insert');
	$router->get('/{id: [0-9]{18} }', 'DosenController@getByNip');
	$router->get('/{id: [0-9]+}', 'DosenController@get');

});

Route::group(['prefix' => '/prodi'], function($router){

	/**
	 * @todo tambah kolom kd_prodi
	 * @todo router by kd_prodi
	 * 
	 */
	$router->get('/', 'ProdiController@getAll');
	$router->get('/{id: [0-9]+}', 'ProdiController@get');
	$router->put('/{id: [0-9]+}', 'Admin\ProdiController@update');
	$router->post('/', 'SuperAdmin\ProdiController@insert');
	$router->delete('/{id: [0-9]+}', 'SuperAdmin\ProdiController@delete');

});

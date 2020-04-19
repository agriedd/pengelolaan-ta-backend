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

Route::group([
	'middleware' 	=> ['auth', 'active', 'super'],
	'prefix'	 	=> '/'
], function($router){

	Route::group([
		'prefix'	=> '/admin',
	], function($router){
			$router->get('/', 'Admin\AdminController@getAll');
			$router->post('/', 'Admin\AdminController@insert');
			$router->get('/{id}', 'Admin\AdminController@get');
			$router->put('/{id}', 'Admin\AdminController@update');
		}
	);

	$router->get('/riwayat/', 'RiwayatLoginController@getAll');

} );


// $router->post('/login', "LoginController@loginPost");


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

$router->get('/admin/', 'Admin\AdminController@getAll');
$router->post('/admin/', 'Admin\AdminController@insert');
$router->get('/admin/{id}', [ 'middleware' => 'auth', 'uses' => 'Admin\AdminController@get']);
$router->put('/admin/{id}', 'Admin\AdminController@update');

$router->get('/riwayat/', [ 'middleware' => [ 'auth', 'active', 'super' ], 'uses' => 'RiwayatLoginController@getAll' ]);

// $router->post('/login', "LoginController@loginPost");


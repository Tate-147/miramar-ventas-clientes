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

$router->group(['prefix' => 'clientes'], function () use ($router) {
    $router->get('/', 'ClienteController@index');
    $router->post('/', 'ClienteController@store');
    $router->get('/{id}', 'ClienteController@show');
    $router->put('/{id}', 'ClienteController@update');
    $router->delete('/{id}', 'ClienteController@destroy');
});

$router->group(['prefix' => 'ventas'], function () use ($router) {
    $router->get('/', 'VentaController@index');
    $router->post('/', 'VentaController@store');
    $router->get('/{id}', 'VentaController@show');
    $router->put('/{id}', 'VentaController@update');
    $router->delete('/{id}', 'VentaController@destroy');
});

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

// Création du groupe api : http://localhost:8000/api/
$router->group(['prefix' => 'api'], function () use ($router) {

    //inscription
    // api/register
    $router->post('register', 'AuthController@register');
    // api/login
    $router->post('login', 'AuthController@login');
    // api/logout
    $router->post('logout', 'AuthController@logout');
    // api/refresh
    $router->post('refresh', 'AuthController@refresh');
    // api/me
    $router->post('me', 'AuthController@me');

    // Qr code scanné
    $router->get('scans/{code_id}', 'QrCodeController@scanQrcode');

    $router->group(['prefix' => 'qrcodes'], function () use ($router) {

        // Tous les QR
        $router->get('', ['uses' => 'QrcodeController@showAllQrcodes']);
        // Détails d'un QR
        $router->get('{id}', ['middleware' => 'mustBeOwnerOfQrcode', 'uses' => 'QrcodeController@showOneQrcode']);
        // Nombre de visites
        $router->get('{id}/stats', ['middleware' => 'mustBeOwnerOfQrcode', 'uses' => 'QrcodeController@countStats']);
        // Ajout d'un QR
        $router->post('', ['uses' => 'QrcodeController@createQrcode']);
        // Modification d'un QR
        $router->put('{id}', ['middleware' => 'mustBeOwnerOfQrcode', 'uses' => 'QrcodeController@updateQrcode']);
        // Suppression d'un QR
        $router->delete('{id}', ['middleware' => 'mustBeOwnerOfQrcode', 'uses' => 'QrcodeController@deleteQrcode']);
    });
});

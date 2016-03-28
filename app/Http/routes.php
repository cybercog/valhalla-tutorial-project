<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => ['web']], function () {

    Route::get('/', function () {
        return view('welcome');
    });

});


$router->group(['prefix' => 'api/v1'], function ($router) {
    // Applications Authentication...
    $router->post('/auth/app', 'Api\AuthController@authenticateApp');

    // Users Authentication...
    $router->post('/auth/user', 'Api\AuthController@authenticateUser')->middleware('auth.api.app');
    $router->post('/auth/user/logout', 'Api\AuthController@logoutUser')->middleware('auth.api.user');

    // Testing routes...
    $router->get('/application-data', 'Api\HomeController@appData')->middleware('auth.api.app');
    $router->get('/user-data', 'Api\HomeController@userData')->middleware('auth.api.user');
});

// Authorize an application for user data...
$router->get('/authorize', 'HomeController@showAuthorizationForm')->middleware('web');
$router->post('/authorize', 'HomeController@authorizeApp')->middleware('web');
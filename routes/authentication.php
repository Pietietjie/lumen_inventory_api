<?php

/* @package Authentication */
$router->group([
    'prefix' => 'auth',
], function () use ($router) {
    $router->post('register', [
        'as' => 'auth.register',
        'uses' => 'AuthenticationController@register',
    ]);

    $router->post('login', [
        'as' => 'auth.login',
        'uses' => 'AuthenticationController@login',
    ]);

    $router->post('logout', [
        'middleware' => 'auth',
        'as' => 'auth.logout',
        'uses' => 'AuthenticationController@logout',
    ]);

    $router->group([
        'middleware' => 'auth',
    ], function () use ($router) {
        $router->post('refresh', [
            'as' => 'auth.refresh',
            'uses' => 'AuthenticationController@refresh',
        ]);

        $router->post('validate', [
            'as' => 'auth.validate',
            'uses' => 'AuthenticationController@validate',
        ]);
    });
});

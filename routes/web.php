<?php

$router->group([
    'prefix' => 'api/v1'
], function () use ($router) {

    require 'authentication.php';

    $router->group([
        'middleware' => 'auth',
    ], function () use ($router) {
        require 'items.php';
    });
});

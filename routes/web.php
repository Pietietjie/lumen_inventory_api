<?php

$router->group([
    'prefix' => 'api/v1'
], function () use ($router) {

    require 'authentication.php';

    $router->get('/', function () use ($router) {
        return $router->app->version();
    });
});

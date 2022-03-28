<?php

/* @package Authentication */
$router->group([
    'prefix' => 'store',
], function () use ($router) {
    $router->get('', [
        'as' => 'store.view',
        'uses' => 'StoreController@view',
    ]);
    $router->post('', [
        'as' => 'store.create',
        'uses' => 'StoreController@create',
    ]);
});

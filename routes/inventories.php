<?php

/* @package Authentication */
$router->group([
    'prefix' => 'inventory',
], function () use ($router) {
    $router->get('', [
        'as' => 'inventory.view',
        'uses' => 'InventoryController@view',
    ]);
    $router->post('/add', [
        'as' => 'inventory.add',
        'uses' => 'InventoryController@add',
    ]);
    $router->post('/subtract', [
        'as' => 'inventory.subtract',
        'uses' => 'InventoryController@subtract',
    ]);
});

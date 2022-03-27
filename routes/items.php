<?php

/* @package Authentication */
$router->group([
    'prefix' => 'item',
], function () use ($router) {
    $router->post('add', [
        'as' => 'item.add',
        'uses' => 'ItemController@create',
    ]);
    $router->put('update', [
        'as' => 'item.update',
        'uses' => 'ItemController@update',
    ]);
    $router->post('/restore', [
        'as' => 'item.restore',
        'uses' => 'ItemController@restore',
    ]);
    $router->delete('', [
        'as' => 'item.delete',
        'uses' => 'ItemController@delete',
    ]);
});

<?php

/* @package Authentication */
$router->group([
    'prefix' => 'item',
], function () use ($router) {
    $router->get('', [
        'as' => 'item.view',
        'uses' => 'ItemController@view',
    ]);
    $router->post('', [
        'as' => 'item.add',
        'uses' => 'ItemController@create',
    ]);
    $router->put('', [
        'as' => 'item.update',
        'uses' => 'ItemController@update',
    ]);
    $router->delete('', [
        'as' => 'item.delete',
        'uses' => 'ItemController@delete',
    ]);
    $router->post('/restore', [
        'as' => 'item.restore',
        'uses' => 'ItemController@restore',
    ]);
});

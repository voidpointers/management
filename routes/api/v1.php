<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
        'middleware' => 'api.auth'
], function ($api) {
    $api->group([
        'namespace' => 'Api\Receipt\V1\Controllers',
        'prefix' => 'receipts',
    ], function ($api) {
        $api->get('lists', 'ReceiptsController@lists');
        $api->get('export', 'ReceiptsController@export');
        $api->get('sales/export', 'SalesController@export');
        $api->post('import', 'ReceiptsController@import');
        $api->post('close', 'ReceiptsController@close');
        $api->post('{receipt_sn}/update', 'ReceiptsController@update');
        $api->post('transaction/{receipt_sn}/create', 'TransactionsController@create');
        $api->post('consignee/{receipt_sn}/update', 'ConsigneesController@update');
    });
    $api->group([
        'namespace' => 'Api\Customization\V1\Controllers',
        'prefix' => 'customizations',
    ], function ($api) {
        $api->get('lists', 'CustomizationController@lists');
        $api->post('create', 'CustomizationController@create');
        $api->post('complete', 'CustomizationController@complete');
        $api->post('close', 'CustomizationController@close');
    });
    $api->group([
        'namespace' => 'Api\Follow\V1\Controllers',
        'prefix' => 'follows',
    ], function ($api) {
        $api->get('lists', 'FollowsController@lists');
        $api->post('create', 'FollowsController@create');
        $api->post('complete', 'FollowsController@complete');
        $api->post('close', 'FollowsController@close');
    });
    $api->group([
        'namespace' => 'Api\Package\V1\Controllers',
        'prefix' => 'packages',
    ], function ($api) {
        $api->get('lists', 'PackagesController@lists');
        $api->get('tracking/info/{order_number}', 'LogisticsController@trackInfo');
        $api->post('create', 'PackagesController@create');
        $api->post('dispatch', 'PackagesController@delivery');
        $api->post('print', 'LogisticsController@labels');
        $api->post('logistics/create', 'LogisticsController@create');
    });
    $api->group([
        'namespace' => 'Api\Logistics\V1\Controllers',
        'prefix' => 'logistics',
    ], function ($api) {
        $api->get('provider/lists', 'ProvidersController@lists');
        $api->get('channel/lists', 'ChannelsController@lists');
    });
    $api->group([
        'namespace' => 'Api\System\V1\Controllers',
        'prefix' => 'systems',
    ], function ($api) {
        $api->get('country/lists', 'CountriesController@lists');
    });
});
